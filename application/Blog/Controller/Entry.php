<?php
namespace Blog\Controller;

use Blog\View\Entries as EntriesView,
    Blog\View\TagCloud,
    Mongo,
    mwop\Controller\Restful as RestfulController,
    mwop\DataSource\Mongo as MongoDataSource,
    mwop\Mvc\Presentation,
    mwop\Stdlib\Resource,
    mwop\Resource\EntryResource,
    Phly\Mustache\Pragma\SubView,
    Zend_Feed_Writer_Feed as FeedWriter;

class Entry extends RestfulController
{
    protected $presentation;

    protected $views = array(
        'getList' => 'Blog\View\Entries',
        'get'     => 'Blog\View\Entry',
    );

    public function __construct()
    {
        $events = $this->events();

        // Normalize ID
        $events->attach('dispatch.pre', function($e) {
            $request = $e->getParam('request');
            $id      = $request->getMetadata('id', false);
            if (!$id) {
                return;
            }

            $id = urldecode($id);
            $request->setMetadata('id', $id);
        });

        // Setup feeds
        $events->attach('dispatch.post', function($e) {
            $request    = $e->getParam('request');
            $format     = $request->getMetadata('format', false);
            if ($format !== 'xml') {
                return;
            }

            $view       = $e->getParam('__RESULT__');
            if (!$view instanceof EntriesView) {
                // No entities, thus no feed
                return;
            }
            if (isset($view->paginatorUrl) && empty($view->tag)) {
                // only doing feeds for main list and tags
                return;
            }

            $controller = $e->getTarget();
            if (null === ($layout = $controller->getPresentation())) {
                return;
            }
            if (is_object($view) && method_exists($view, 'setPresentation')) {
                $view->setPresentation($layout);
            }

            if (isset($view->title) && isset($view->title['text'])) {
                $layout->titleSegments->unshift($view->title['text']);
            }
            $title         = $layout->title();

            $urlHelper     = $layout->helper('url');
            if (false !== strstr($title, 'Tag: ')) {
                $link      = $urlHelper->generate(array('tag' => $view->tag), array('name' => 'blog-tag'));
                $feedLink  = $urlHelper->generate(array('tag' => $view->tag), array('name' => 'blog-tag-feed'));
            } else {
                $link      = $urlHelper->generate(array(), array('name' => 'blog'));
                $feedLink  = $urlHelper->generate(array(), array('name' => 'blog-feed'));
            }

            $feed = new FeedWriter();
            $feed->setTitle($title);
            $feed->setLink($link);
            $feed->setFeedLink($feedLink, 'atom');

            $latest = false;
            foreach ($view->entries->getIterator() as $post) {
                if (!$latest) {
                    $latest = $post;
                }
                $entry = $feed->createEntry();
                $entry->setTitle($post->getTitle());
                $entry->setLink($urlHelper->generate(array('id' => $post->getId()), array('name' => 'blog-entry')));
                /**
                 * @todo inject this info!
                 */
                $entry->addAuthor(array(
                    'name'  => "Matthew Weier O'Phinney",
                    'email' => 'matthew@weierophinney.net',
                    'uri'   => $link,
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
            $response->getHeaders()->addHeader('Content-Type', 'application/atom+xml');

            $e->stopPropagation(true);
            return $response;
        }, 100);

        // Setup presentation
        $events->attach('dispatch.post', function($e) {
            $controller = $e->getTarget();
            if (null === ($layout = $controller->getPresentation())) {
                return;
            }

            $view       = $e->getParam('__RESULT__');
            if (is_object($view) && method_exists($view, 'setPresentation')) {
                $view->setPresentation($layout);
            }

            if (isset($view->title) && isset($view->title['text'])) {
                $layout->titleSegments->unshift($view->title['text']);
            } elseif (isset($view->entity)) {
                $layout->titleSegments->unshift($view->entity->getTitle());
            }

            $tags       = $controller->resource()->getTagCloud();
            $subView    = new SubView('tag-cloud', new TagCloud($tags, $layout));

            if (!isset($layout->footer)) {
                $layout->footer = array('tags' => array('cloud' => $subView));
            } elseif (is_array($layout->footer)) {
                if (!isset($layout->footer['tags'])) {
                    $layout->footer['tags'] = array('cloud' => $subView);
                } elseif (is_array($layout->footer['tags'])) {
                    $layout->footer['tags']['cloud'] = $subView;
                }
            }
        });
    }

    public function resource(Resource $resource = null)
    {
        if (null !== $resource) {
            if (!$resource instanceof EntryResource) {
                throw new \DomainException('Entry controller expects an Entry resource');
            }
            $this->resource = $resource;
        }
        return $this->resource;
    }

    public function setPresentation(Presentation $presentation)
    {
        $this->presentation = $presentation;
        return $this;
    }

    public function getPresentation()
    {
        return $this->presentation;
    }

    public function getList()
    {
        $entries = $this->resource()->getEntries(0, false);
        return new EntriesView(array(
            'entities' => $entries,
            'request'  => $this->getRequest(),
        ));
    }

    public function createAction()
    {
        $request = $this->getRequest();
        if (!$request->isGet()) {
            $response = $this->getResponse();
            $response->headers()->setStatusCode(405);
            $response->setContent('<h2>Illegal Method</h2>');
            return $response;
        }
        return array('url' => '/blog');
    }

    public function tagAction()
    {
        if (!$tag = $this->getRequest()->getMetadata('tag', false)) {
            return $this->getList();
        }
        $tag     = urldecode($tag);
        $entries = $this->resource()->getEntriesByTag($tag, false);
        return new EntriesView(array(
            'tag'      => $tag,
            'title'    => array('text' => 'Tag: ' . $tag),
            'entities' => $entries,
            'request'  => $this->getRequest(),
            'paginator_url' => '/blog/tag/' . $tag,
        ));
    }

    public function yearAction()
    {
        if (!$year = $this->getRequest()->getMetadata('year', false)) {
            return $this->getList();
        }
        $entries = $this->resource()->getEntriesByYear($year, false);
        return new EntriesView(array(
            'title'    => array('text' => 'Entries for ' . $year),
            'entities' => $entries,
            'request'  => $this->getRequest(),
            'paginator_url' => '/blog/year/' . $year,
        ));
    }

    public function monthAction()
    {
        if (!$year = $this->getRequest()->getMetadata('year', false)) {
            return $this->getList();
        }
        if (!$month = $this->getRequest()->getMetadata('month', false)) {
            return $this->getList();
        }
        $entries = $this->resource()->getEntriesByMonth($month, $year, false);
        return new EntriesView(array(
            'title'    => array('text' => 'Entries for ' . date('F', strtotime($year . '-' . $month . '-01')) . ' ' . $year),
            'entities' => $entries,
            'request'  => $this->getRequest(),
            'paginator_url' => '/blog/month/' . $year . '/' . $month,
        ));
    }

    public function dayAction()
    {
        if (!$year = $this->getRequest()->getMetadata('year', false)) {
            return $this->getList();
        }
        if (!$month = $this->getRequest()->getMetadata('month', false)) {
            return $this->getList();
        }
        if (!$day = $this->getRequest()->getMetadata('day', false)) {
            return $this->getList();
        }
        $entries = $this->resource()->getEntriesByDay($day, $month, $year, false);
        return new EntriesView(array(
            'title'    => array('text' => 'Entries for ' . $day . ' ' . date('F', strtotime($year . '-' . $month . '-' . $day)) . ' ' . $year),
            'entities' => $entries,
            'request'  => $this->getRequest(),
            'paginator_url' => '/blog/day/' . $year . '/' . $month . '/'. $day,
        ));
    }
}
