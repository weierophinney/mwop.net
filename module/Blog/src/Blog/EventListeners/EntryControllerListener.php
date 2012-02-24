<?php

namespace Blog\EventListeners;

use Blog\Exception,
    Traversable,
    Zend\EventManager\StaticEventCollection as Events,
    Zend\Feed\Writer\Feed as FeedWriter,
    Zend\Tag\Cloud,
    Zend\View\Model\ViewModel,
    Zend\View\Variables as ViewVariables;

class EntryControllerListener
{
    protected $listeners = array();

    public function attach(Events $events)
    {
        $controller = 'Blog\Controller\EntryController';
        $this->listeners[] = $events->attach($controller, 'dispatch', array($this, 'verifyApiKey'),            200);
        $this->listeners[] = $events->attach($controller, 'dispatch', array($this, 'normalizeId'),             100);
        $this->listeners[] = $events->attach($controller, 'dispatch', array($this, 'generateFeed'),            -10);
        $this->listeners[] = $events->attach($controller, 'dispatch', array($this, 'prepareRestfulTemplates'), -85);
        $this->listeners[] = $events->attach($controller, 'dispatch', array($this, 'injectTagCloud'),         -100);
    }

    public function detach(Events $events)
    {
        $controller = 'Blog\Controller\EntryController';
        foreach ($this->listeners as $listener) {
            $events->detach($controller, $listener);
        }
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
        $format     = $matches->getParam('format', '');
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
        $renderer   = $controller->getRenderer();
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

        $urlHelper     = $controller->plugin('url');
        if (false !== strstr($title, 'Tag: ')) {
            $link      = $urlHelper->fromRoute('blog/tag', array('tag' => $view['tag']));
            $feedLink  = $urlHelper->fromRoute('blog/tag/feed/', array('tag' => $view['tag']));
        } else {
            $link      = $urlHelper->fromRoute('blog');
            $feedLink  = $urlHelper->fromRoute('blog/feed');
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
            $entry->setLink($baseUri . $urlHelper->fromRoute('blog/entry', array('id' => $post->getId())));

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
        $controller = $e->getTarget();
        $urlHelper  = $controller->plugin('url');
        $tags       = $controller->resource()->getTagCloud();
        $renderer   = $controller->getRenderer();

        $cloud      = function() use ($tags, $urlHelper, $renderer) {
            foreach ($tags as $key => $tag) {
                $tags[$key]['params'] = array(
                    'url' => $urlHelper->fromRoute('blog/tag', array('tag' => $tag['title'])),
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

        $viewModel = $e->getViewModel();
        $viewModel->footer = $cloud;
    }

    public function prepareRestfulTemplates($e)
    {
        $response   = $e->getResponse();
        if (!$response->isSuccess()) {
            // Don't render 401's and 404's
            return;
        }

        $viewModel  = $e->getResult();
        if (!$viewModel) {
            $viewModel = new ViewModel;
        }

        $controller = $e->getTarget();
        $renderer   = $controller->getRenderer();
        $request    = $e->getRequest();
        $matches    = $e->getRouteMatch();

        $renderer->plugin('headLink')->appendStylesheet('/css/Blog/blog.css');
        $renderer->plugin('dojo')->setDjConfigOption('baseUrl', '/js/dojo/')
                                 ->setDjConfigOption('modulePaths', array('Blog' => '/js/Blog'))
                                 ->requireModule('Blog.blog');

        $action     = $matches->getParam('action', false);
        if ($action) {
            $this->injectActionTemplate($action, $viewModel);
            return;
        }

        switch (strtolower($request->getMethod())) {
            case 'get':
                if (!$matches->getParam('id', false)) {
                    $script = 'blog/list';
                    break;
                }
                $script = 'blog/entry';
                break;
            case 'post':
                if (isset($vars['errors'])) {
                    $script = 'blog/form';
                    break;
                }
                $script = 'blog/entry';
                break;
            case 'put':
                if (isset($vars['errors'])) {
                    $script = 'blog/form';
                    break;
                }
                $script = 'blog/entry';
                break;
            case 'delete':
                $script = 'blog/list';
                break;
            default:
                $script = 'blog/list';
                break;
        }

        $viewModel->setTemplate($script);
        return;
    }

    protected function injectActionTemplate($action, $viewModel)
    {
        switch ($action) {
            case 'create':
                $script = 'blog/form';
                break;
            case 'tag':
                $script = 'blog/list';
                break;
            case 'year':
                $script = 'blog/list';
                break;
            case 'month':
                $script = 'blog/list';
                break;
            case 'day':
                $script = 'blog/list';
                break;
            default:
                $script = 'blog/' . $action;
                break;
        }

        $viewModel->setTemplate($script);
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
