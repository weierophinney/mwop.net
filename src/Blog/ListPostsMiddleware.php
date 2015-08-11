<?php
namespace Mwop\Blog;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateInterface;

class ListPostsMiddleware
{
    private $mapper;

    private $template;

    public function __construct(MapperInterface $mapper, TemplateInterface $template)
    {
        $this->mapper   = $mapper;
        $this->template = $template;
    }

    public function __invoke($req, $res, $next)
    {
        $tag   = str_replace(array('+', '%20'), ' ', $req->getAttribute('tag', ''));
        $path  = $req->getOriginalRequest()->getUri()->getPath();
        $page  = $this->getPageFromRequest($req);
        $posts = $tag ? $this->mapper->fetchAllByTag($tag) : $this->mapper->fetchAll();

        $posts->setItemCountPerPage(10);

        // If the requested page is later than the last, redirect to the last
        if (count($posts) && $page > count($posts)) {
            return $res
                ->withStatus(302)
                ->withHeader('Location', sprintf('%s?page=%d', $path, count($posts)));
        }

        $posts->setCurrentPageNumber($page);

        $pagination = $this->preparePagination($path, $page, $posts->getPages());
        $entries    = $this->prepareEntries($tag, $path, $page, $posts);

        return new HtmlResponse($this->template->render(
            'blog.list',
            $this->prepareView($tag, $entries, $pagination)
        ));
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @return int
     */
    private function getPageFromRequest($req)
    {
        $page = isset($req->getQueryParams()['page']) ? $req->getQueryParams()['page'] : 1;
        $page = (int) $page;
        return ($page < 1) ? 1 : $page;
    }

    /**
     * @var string $path
     * @var int $page
     * @var object $pagination
     * @return object $pagination
     */
    private function preparePagination($path, $page, $pagination)
    {
        $pagination->base_path = $path;
        $pagination->is_first  = ($page === $pagination->first);
        $pagination->is_last   = ($page === $pagination->last);

        $pages = array();
        for ($i = $pagination->firstPageInRange; $i <= $pagination->lastPageInRange; $i += 1) {
            $pages[] = [
                'base_path' => $path,
                'number'    => $i,
                'current'   => ($page === $i),
            ];
        }
        $pagination->pages = $pages;

        return $pagination;
    }

    /**
     * @param string $tag
     * @param string path
     * @param int $page
     * @param object $posts
     * @return object Entries
     */
    private function prepareEntries($tag, $path, $page, $posts)
    {
        // Strip "/tag/<tag>" from base path in order to create paths to posts
        $postPath = $tag ? substr($path, 0, - (strlen($tag) + 5)) : $path;
        return array_map(function ($post) use ($postPath) {
            return new EntryView($post, $postPath);
        }, iterator_to_array($posts->getItemsByPage($page)));
    }

    /**
     * @param string $tag
     * @param object $entries
     * @param object $pagination
     * @return array
     */
    private function prepareView($tag, $entries, $pagination)
    {
        $view = $tag ? ['tag' => $tag] : [];
        return array_merge($view, [
            'title'      => $tag ? 'Tag: ' . $tag  : 'Blog Posts',
            'posts'      => $entries,
            'pagination' => $pagination,
        ]);
    }
}
