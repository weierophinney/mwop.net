<?php
namespace Blog\Controller;

use Blog\EntryResource,
    Blog\EventListeners\EntryControllerListener,
    DateTime,
    DateTimezone,
    Iterator,
    Mongo,
    Zend\Filter\InputFilter,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zend\View\Renderer,
    Zend\Mvc\Controller\RestfulController;

class EntryController extends RestfulController
{
    protected $apiKey;
    protected $resource;
    protected $view;

    public function __construct()
    {
        $events = $this->events();
        $listeners = new EntryControllerListener;
        $events->attachAggregate($listeners);
    }

    public function setResource(EntryResource $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function resource()
    {
        return $this->resource;
    }

    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setApiKeyLocation($key)
    {
        if (file_exists($key)) {
            $this->apiKey = file_get_contents($key);
            $this->apiKey = trim($this->apiKey);
        }
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getList()
    {
        $entries = $this->resource()->getEntries(0, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Blog Entries',
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog',
        );
    }

    public function get($id)
    {
        $entry = $this->resource()->get($id);
        if (!$entry) {
            $this->response->setStatusCode(404);
            return array(
                'error' => 'Entry not found',
            );
        }

        // Is the entry public?
        $published = $entry->getCreated();
        $timezone  = new DateTimeZone($entry->getTimezone());
        $published = new DateTime('@' . $published, $timezone);
        $now       = new DateTime('now', $timezone);
        if (!$entry->isPublic() || $entry->isDraft() || $now < $published)  {
            // Not public
            $event      = clone $this->event;
            $routeMatch = $event->getRouteMatch();

            // Cache original action
            $action     = $routeMatch->getParam('action');

            // Override action for purposes of authentication
            $routeMatch->setParam('action', 'preview');
            $result = $this->events()->trigger('authenticate', $event);

            // Reset original action
            $routeMatch->setParam('action', $action);

            // If authentication failed, set the content to whatwe received in 
            // the event, and return early
            if (!$result->last()) {
                $this->event->setParam('content', $event->getParam('content'));
                return false;
            }
        }

        // Display the entry
        return array(
            'entry' => $entry,
        );
    }

    public function create($data)
    {
        $entry = $this->resource()->create($data);

        if ($entry instanceof InputFilter) {
            return array(
                'url'    => '/blog',
                'errors' => $entry->getMessages(),
            );
        }

        $response = $this->redirect()->toRoute('blog/entry', array('id' => $entry->getId()));
        $response->setStatusCode(201);
        return $response;
    }

    public function update($id, $data)
    {
        $entry = $this->resource()->update($id, $data);

        if ($entry instanceof InputFilter) {
            return array(
                'url'    => '/blog',
                'errors' => $entry->getMessages(),
            );
        }

        return array(
            'updated' => true,
            'entry'   => $entry,
            'id'      => $id,
        );
    }

    public function delete($id)
    {
        $result = $this->resource()->delete($id);

        $this->response->setStatusCode(204);
        return $this->response;
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
        $event   = $this->getEvent();
        $request = $event->getRequest();
        $matches = $event->getRouteMatch();
        $tag     = $matches->getParam('tag', false);

        if (!$tag) {
            return $this->getList();
        }

        $rawTag  = $tag;
        $tag     = strtolower(urldecode($tag));
        $entries = $this->resource()->getEntriesByTag($tag, false);
        $page    = $this->request->query()->get('page', 1);

        return array(
            'title'         => 'Tag: ' . $tag,
            'tag'           => $tag,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => $this->url()->fromRoute('blog/tag', array('tag' => $tag)),
        );
    }

    public function yearAction()
    {
        $event   = $this->getEvent();
        $request = $event->getRequest();
        $matches = $event->getRouteMatch();
        $year    = $matches->getParam('year', false);

        if (!$year) {
            return $this->getList();
        }

        $entries = $this->resource()->getEntriesByYear($year, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Blog Entries for ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => $this->url()->fromRoute('blog/year', array('year' => $year)),
        );
    }

    public function monthAction()
    {
        $event   = $this->getEvent();
        $request = $event->getRequest();
        $matches = $event->getRouteMatch();
        $year    = $matches->getParam('year', false);

        if (!$year) {
            return $this->getList();
        }

        $month = $matches->getParam('month', false);
        if (!$month) {
            return $this->getList();
        }

        $entries = $this->resource()->getEntriesByMonth($month, $year, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Blog Entries for ' . date('F', strtotime($year . '-' . $month . '-01')) . ' ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => $this->url()->fromRoute('blog/month', array('year' => $year, 'month' => $month)),
        );
    }

    public function dayAction()
    {
        $event   = $this->getEvent();
        $request = $event->getRequest();
        $matches = $event->getRouteMatch();
        $year    = $matches->getParam('year', false);

        if (!$year) {
            return $this->getList();
        }

        $month = $matches->getParam('month', false);
        if (!$month) {
            return $this->getList();
        }

        $day = $matches->getParam('day', false);
        if (!$day) {
            return $this->getList();
        }

        $entries = $this->resource()->getEntriesByDay($day, $month, $year, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Blog Entries for ' . $day . ' ' . date('F', strtotime($year . '-' . $month . '-' . $day)) . ' ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => $this->url()->fromRoute('blog/day', array('year' => $year, 'month' => $month, 'day' => $day)),
        );
    }

    public function getPaginator(Iterator $it, $page)
    {
        $paginator = new Paginator(new IteratorPaginator($it));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);
        return $paginator;
    }
}
