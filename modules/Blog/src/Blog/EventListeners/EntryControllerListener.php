<?php

namespace Blog\EventListeners;

use Blog\Exception,
    Traversable,
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
        $this->listeners[] = $events->attach('dispatch',  array($this, 'verifyApiKey'), 200);
        $this->listeners[] = $events->attach('dispatch',  array($this, 'normalizeId'), 100);
        $this->listeners[] = $events->attach('dispatch', array($this, 'generateFeed'), -10);
        $this->listeners[] = $events->attach('dispatch', array($this, 'injectTagCloud'), -100);
        $this->listeners[] = $events->attach('dispatch', array($this, 'renderRestfulActions'), -50);
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
        $request    = $e->getRequest();
        $matches    = $e->getRouteMatch();
        $format     = $matches->getParam('format', false);
        if (strtolower($format) != 'xml') {
            return;
        }

        $feedType = $request->query()->get('type', 'atom');
        if (!in_array($feedType, array('rss', 'atom'))) {
            $feedType = 'atom';
        }

        $view       = $e->getResult();
        if (!isset($view['entries'])) {
            // No entries, thus no feed
            return;
        }

        $controller = $e->getTarget();
        $renderer   = $controller->getView();
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
            $link      = $urlHelper(array('tag' => $view['tag']), array('name' => 'blog/tag'));
            $feedLink  = $urlHelper(array('tag' => $view['tag']), array('name' => 'blog/tag/feed'));
        } else {
            $link      = $urlHelper(array(), array('name' => 'blog'));
            $feedLink  = $urlHelper(array(), array('name' => 'blog/feed'));
        }
        $link     = $baseUri . $link;
        $feedLink = $baseUri . $feedLink;

        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($link);
        $feed->setFeedLink($feedLink, $feedType);

        // Make this configurable?
        if ('rss' == $feedType) {
            $feed->setDescription($title);
        }

        $latest = false;
        foreach ($view['entries']->getIterator() as $post) {
            if (!$latest) {
                $latest = $post;
            }
            $entry = $feed->createEntry();
            $entry->setTitle($post->getTitle());
            $entry->setLink($baseUri . $urlHelper(array('id' => $post->getId()), array('name' => 'blog/entry')));

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

        $response = $e->getResponse();
        $response->setContent($feed->export($feedType));

        $headers = $response->headers();
        switch ($feedType) {
            case 'rss':
                $headers->addHeaderLine('Content-Type', 'application/rss+xml');
                break;
            case 'atom':
            default:
                $headers->addHeaderLine('Content-Type', 'application/atom+xml');
                break;
        }

        $e->stopPropagation(true);
        return $response;
    }

    public function injectTagCloud($e)
    {
        $view       = $e->getResult();
        $controller = $e->getTarget();
        $tags       = $controller->resource()->getTagCloud();
        $renderer   = $controller->getView();
        $cloud      = function() use ($tags, $renderer) {
            $url = $renderer->plugin('url');
            foreach ($tags as $key => $tag) {
                $tags[$key]['params'] = array(
                    'url' => $url(array('tag' => $tag['title']), array('name' => 'blog/tag')),
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
        $response   = $e->getResponse();
        if (!$response->isSuccess()) {
            // Don't render 401's and 404's
            return;
        }

        $vars       = $e->getResult();
        if (!$vars) {
            $vars = array();
        }

        $controller = $e->getTarget();
        $renderer   = $controller->getView();
        $request    = $e->getRequest();
        $matches    = $e->getRouteMatch();

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
                    $script = 'blog-entry/list.phtml';
                    break;
                }
                $script = 'blog-entry/entry.phtml';
                break;
            case 'post':
                if (isset($vars['errors'])) {
                    $script = 'blog-entry/form.phtml';
                    break;
                }
                $script = 'blog-entry/entry.phtml';
                break;
            case 'put':
                if (isset($vars['errors'])) {
                    $script = 'blog-entry/form.phtml';
                    break;
                }
                $script = 'blog-entry/entry.phtml';
                break;
            case 'delete':
                $script = 'blog-entry/list.phtml';
                break;
            default:
                $script = 'blog-entry/list.phtml';
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
                $script = 'blog-entry/form.phtml';
                break;
            case 'tag':
                $script = 'blog-entry/list.phtml';
                break;
            case 'year':
                $script = 'blog-entry/list.phtml';
                break;
            case 'month':
                $script = 'blog-entry/list.phtml';
                break;
            case 'day':
                $script = 'blog-entry/list.phtml';
                break;
            default:
                $script = 'blog-entry/' . $action . '.phtml';
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

    public function verifyApiKey($e)
    {
        $routeMatch = $e->getRouteMatch();
        $action     = $routeMatch->getParam('action', false);
        if ($action) {
            // If we have an action, then we're not RESTful
            return;
        }

        $request = $e->getRequest();
        if ($request->isGet()) {
            // If we have a GET request, nothing to worry about
            return;
        }

        $headers = $request->headers();
        if ($headers->has('X-MWOP-APIKEY')) {
            $key  = $e->getTarget()->getApiKey();
            $test = $headers->get('X-MWOP-APIKEY')->getFieldValue();
            if ($key && ($key == $test)) {
                // We have a matching key
                return;
            }
        }

        // No key provided, or invalid
        $response = $e->getResponse();
        $response->setStatusCode(401);
        $response->setContent('Unauthorized');
        return $response;
    }
}
