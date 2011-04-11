<?php
namespace Blog\View;

use mwop\Mvc\Presentation,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zend\Tag\Cloud as TagCloud,
    Phly\Mustache\Pragma\SubView;

class Entries
{
    protected $entries;
    protected $request;
    protected $presentation;
    protected $paginatorUrl = '/blog';

    public function __construct(array $values)
    {
        if (!isset($values['entities'])) {
            throw new \DomainException('Expected entities; received none');
        }

        $entities = new Paginator(new IteratorPaginator($values['entities']));
        $request  = isset($values['request']) ? $values['request'] : false;
        $page     = $request ? $request->query('page', 1) : 1;

        $entities->setCurrentPageNumber($page);
        $entities->setItemCountPerPage(10);
        $entities->setPageRange(10);

        if (isset($values['title'])) {
            $this->title = $values['title'];
        }
        if (isset($values['paginator_url'])) {
            $this->paginatorUrl = $values['paginator_url'];
        }

        $this->entries = $entities;
        $this->request = $request;
    }

    public function setPresentation(Presentation $presentation)
    {
        $this->presentation = $presentation;
        Layout::setup($presentation);
    }

    public function entities()
    {
        $array = array();
        foreach ($this->entries as $entry) {
            $array[] = new Entry(array('entity' => $entry, 'request' => $this->request));
        }
        return $array;
    }

    public function paginator()
    {
        $pages = $this->entries->getPages();

        if (!$pages->pageCount || $pages->pageCount == 1) {
            return false;
        }

        $pageList = array();
        $current  = $pages->current;
        foreach ($pages->pagesInRange as $p) {
            $page = array(
                'page' => array(
                    'url'    => $this->paginatorUrl . '?page=' . $p,
                    'number' => $p,
                )
            );
            if ($current == $p) {
                $page = array(
                    'current' => array(
                        'number' => $p,
                    )
                );
            }
            $pageList[] = $page;
        }

        return new SubView('paginator', array(
            'first'    => (1 === $current) ? false : array('url' => $this->paginatorUrl),
            'previous' => isset($pages->previous) 
                        ? array('page' => $this->paginatorUrl . '?page=' . $pages->previous) 
                        : false,
            'pages'    => $pageList,
            'next'     => isset($pages->next)
                        ? array('page' => $this->paginatorUrl . '?page=' . $pages->next)
                        : false,
            'last'     => ($current === $pages->last) ? false : array('url' => $this->paginatorUrl . '?page=' . $pages->last),
        ));
    }
}
