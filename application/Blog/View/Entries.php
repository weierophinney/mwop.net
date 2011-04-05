<?php
namespace Blog\View;

use Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zend\Tag\Cloud as TagCloud,
    Phly\Mustache\Pragma\SubView;

class Entries
{
    protected $entries;
    protected $request;

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

        $this->entries = $entities;
        $this->request = $request;
        $this->layout  = new Layout();
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

        if (!$pages->pageCount) {
            return false;
        }

        $pageList = array();
        $current  = $pages->current;
        foreach ($pages->pagesInRange as $p) {
            $page = array(
                'page' => array(
                    'url'    => '/blog?page=' . $p,
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
            'first'    => array('url' => '/blog'),
            'previous' => isset($pages->previous) 
                        ? array('page' => '/blog?page=' . $pages->previous) 
                        : false,
            'pages'    => $pageList,
            'next'     => isset($pages->next)
                        ? array('page' => '/blog?page=' . $pages->next)
                        : false,
            'last'     => array('url' => '/blog?page=' . $pages->last),
        ));
    }
}
