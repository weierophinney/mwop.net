<?php
namespace Blog\Controller;

use Blog\EntryResource,
    Blog\EventListeners\EntryControllerListener,
    Iterator,
    Mongo,
    Zend\Filter\InputFilter,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zf2Mvc\Controller\RestfulController;

class EntryController extends RestfulController
{
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

    public function getList()
    {
        $entries = $this->resource()->getEntries(0, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Entries',
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog',
        );
    }

    public function get($id)
    {
        $entry = $this->resource()->get($id);
        if (!$entry) {
            return array(
                'error' => 'Entry not found',
            );
        }
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

        return array(
            'created' => true,
            'entry'   => $entry,
            'id'      => $id,
        );
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

        return array(
            'id'      => $id,
            'deleted' => $result,
        );
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
        $request = $this->getRequest();
        $matches = $request->getParam('route-match', false);
        $tag     = $matches->getParam('tag', false);

        if (!$tag) {
            return $this->getList();
        }

        $rawTag  = $tag;
        $tag     = urldecode($tag);
        $entries = $this->resource()->getEntriesByTag($tag, false);
        $page    = $this->request->query()->get('page', 1);

        return array(
            'title'         => 'Tag: ' . $tag,
            'tag'           => $tag,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog/tag/' . $rawTag,
        );
    }

    public function yearAction()
    {
        $request = $this->getRequest();
        $matches = $request->getParam('route-match', false);
        $year    = $matches->getParam('year', false);

        if (!$year) {
            return $this->getList();
        }

        $entries = $this->resource()->getEntriesByYear($year, false);
        $page    = $this->request->query()->get('page', 1);
        return array(
            'title'         => 'Entries for ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog/year/' . $year,
        );
    }

    public function monthAction()
    {
        $request = $this->getRequest();
        $matches = $request->getParam('route-match', false);
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
            'title'         => 'Entries for ' . date('F', strtotime($year . '-' . $month . '-01')) . ' ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog/month/' . $year . '/' . $month,
        );
    }

    public function dayAction()
    {
        $request = $this->getRequest();
        $matches = $request->getParam('route-match', false);
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
            'title'         => 'Entries for ' . $day . ' ' . date('F', strtotime($year . '-' . $month . '-' . $day)) . ' ' . $year,
            'entries'       => $this->getPaginator($entries, $page),
            'paginator_url' => '/blog/day/' . $year . '/' . $month . '/'. $day,
        );
    }

    public function getPaginator(Iterator $it, $page)
    {
        $paginator = new Paginator(new IteratorPaginator($it));
        $paginator-setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);
        return $paginator;
    }
}
