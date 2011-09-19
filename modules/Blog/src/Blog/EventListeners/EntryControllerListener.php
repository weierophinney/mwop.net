<?php

namespace Blog\EventListeners;

use Traversable,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\Feed\Writer\Feed as FeedWriter,
    Zend\Tag\Cloud,
    Zend\View\Variables as ViewVariables;

class EntryControllerListener implements ListenerAggregate
{
    protected $listeners = array();

    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch.pre',  array($this, 'normalizeId'));
        $this->listeners[] = $events->attach('dispatch.post', array($this, 'generateFeed'), 100);
        $this->listeners[] = $events->attach('dispatch.post', array($this, 'injectTagCloud'));
        $this->listeners[] = $events->attach('dispatch.post', array($this, 'renderRestfulActions'), 50);
    }

    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener);
        }

        $this->listeners = array();
    }

    public function normalizeId($e)
    {
        $request = $e->getParam('request');
        $id      = $request->getMetadata('id', false);
        if (!$id) {
            return;
        }

        $id      = urldecode($id);
        $matches = $request->getMetadata('route-match');
        $matches->setParam('id', $id);
    }

    public function generateFeed($e)
    {
        $request    = $e->getParam('request');
        $matches    = $request->getMetadata('route-match');
        $format     = $matches->getParam('format', false);
        if (strtolower($format) != 'xml') {
            return;
        }

        $view       = $e->getParam('__RESULT__');
        if (!isset($view['entries'])) {
            // No entries, thus no feed
            return;
        }

        $controller = $e->getTarget();
        $renderer   = $controller->getView();
        $request    = $e->getParam('request');
        $uri        = $request->uri();
        $baseUri    = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://')
                    . $_SERVER['HTTP_HOST'];

        /**
         * Get full feed title without HTML tags
         */
        $headTitle  = $renderer->plugin('headTitle');
        if (isset($view['title'])) {
            $headTitle->prepend($view['title']);
        }
        $title = $headTitle->toString();
        $title = strip_tags($title);

        $urlHelper     = $renderer->plugin('url');
        if (false !== strstr($title, 'Tag: ')) {
            $link      = $urlHelper->direct(array('tag' => $view['tag']), array('name' => 'blog-tag'));
            $feedLink  = $urlHelper->direct(array('tag' => $view['tag']), array('name' => 'blog-tag-feed'));
        } else {
            $link      = $urlHelper->direct(array(), array('name' => 'blog'));
            $feedLink  = $urlHelper->direct(array(), array('name' => 'blog-feed'));
        }
        $link     = $baseUri . $link;
        $feedLink = $baseUri . $feedLink;

        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($link);
        $feed->setFeedLink($feedLink, 'atom');

        $latest = false;
        foreach ($view['entries']->getIterator() as $post) {
            if (!$latest) {
                $latest = $post;
            }
            $entry = $feed->createEntry();
            $entry->setTitle($post->getTitle());
            $entry->setLink($baseUri . $urlHelper->direct(array('id' => $post->getId()), array('name' => 'blog-entry')));

            /**
             * @todo inject this info!
             */
            $entry->addAuthor(array(
                'name'  => "Matthew Weier O'Phinney",
                'email' => 'matthew@weierophinney.net',
                'uri'   => $baseUri,
            ));
            $entry->setDateModified($post->getUpdated());
            $entry->setDateCreated($post->getCreated());
            $entry->setContent($post->getBody());
            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified($latest->getUpdated());

        $response = $e->getParam('response');
        $response->setContent($feed->export('atom'));
        $response->headers()->addHeaderLine('Content-Type', 'application/atom+xml');

        $e->stopPropagation(true);
        return $response;
    }

    public function injectTagCloud($e)
    {
        $view       = $e->getParam('__RESULT__');
        $controller = $e->getTarget();
        $tags       = $controller->resource()->getTagCloud();
        $renderer   = $controller->getView();
        $cloud      = function() use ($tags, $renderer) {
            $url = $renderer->plugin('url');
            foreach ($tags as $key => $tag) {
                $tags[$key]['params'] = array(
                    'url' => $url->direct(array('tag' => $tag['title']), array('name' => 'blog-tag')),
                );
            }
            $cloud = new Cloud(array(
                'tags' => $tags,
                'tagDecorator' => array(
                    'decorator' => 'html_tag',
                    'options'   => array(
                        'fontSizeUnit' => '%',
                        'minFontSize' => 80,
                        'maxFontSize' => 300,
                    ),
                ),
            ));
            return "<h4>Tag Cloud</h4>\n<div class=\"cloud\">\n" . $cloud->render() . "</div>\n";
        };
        $e->setParam('footer', $cloud);
    }

    public function renderRestfulActions($e)
    {
        $vars       = $e->getParam('__RESULT__', array());
        $controller = $e->getTarget();
        $renderer   = $controller->getView();
        $request    = $e->getParam('request');
        $response   = $e->getParam('response');
        $matches    = $request->getMetadata('route-match');

        $renderer->plugin('headLink')->appendStylesheet('/css/Blog/blog.css');
        $renderer->plugin('dojo')->setDjConfigOption('baseUrl', '/js/dojo/')
                                 ->setDjConfigOption('modulePaths', array('Blog', '/js/Blog'))
                                 ->requireModule('Blog.blog');

        $action     = $matches->getParam('action', false);
        if ($action) {
            $content = $this->renderAction($action, $vars, $renderer);
            $e->setParam('content', $content);
            return;
        }

        switch (strtolower($request->getMethod())) {
            case 'get':
                if (!$matches->getParam('id', false)) {
                    $script = 'blog/list.phtml';
                    break;
                }
                $script = 'blog/entry.phtml';
                break;
            case 'post':
                if (isset($vars['errors'])) {
                    $script = 'blog/form.phtml';
                    break;
                }
                $script = 'blog/entry.phtml';
                break;
            case 'put':
                if (isset($vars['errors'])) {
                    $script = 'blog/form.phtml';
                    break;
                }
                $script = 'blog/entry.phtml';
                break;
            case 'delete':
                $script = 'blog/list.phtml';
                break;
            default:
                $script = 'blog/list.phtml';
                break;
        }

        if (is_object($vars)) {
            if ($vars instanceof Traversable) {
                $viewVars = new ViewVariables(array());
                $vars = iterator_apply($vars, function($it) use ($viewVars) {
                    $viewVars[$it->key()] = $it->current();
                }, $it);
                $vars = $viewVars;
            } else {
                $vars = new ViewVariables((array) $vars);
            }
        } else {
            $vars = new ViewVariables($vars);
        }

        // Action content
        $content = $renderer->render($script, $vars);
        $e->setParam('content', $content);
        return;
    }

    protected function renderAction($action, $vars, $renderer)
    {
        switch ($action) {
            case 'create':
                $script = 'blog/form.phtml';
                break;
            case 'tag':
                $script = 'blog/list.phtml';
                break;
            case 'year':
                $script = 'blog/list.phtml';
                break;
            case 'month':
                $script = 'blog/list.phtml';
                break;
            case 'day':
                $script = 'blog/list.phtml';
                break;
            default:
                $script = 'blog/' . $action . '.phtml';
                break;
        }
        if (is_object($vars)) {
            if ($vars instanceof Traversable) {
                $viewVars = new ViewVariables(array());
                $vars = iterator_apply($vars, function($it) use ($viewVars) {
                    $viewVars[$it->key()] = $it->current();
                }, $it);
                $vars = $viewVars;
            } else {
                $vars = new ViewVariables((array) $vars);
            }
        } else {
            $vars = new ViewVariables($vars);
        }

        // Action content
        $content = $renderer->render($script, $vars);
        return $content;
    }
}
