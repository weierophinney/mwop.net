<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Phly\Expressive\Mustache\UriHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use stdClass;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Paginator\Paginator;

class ListPostsMiddleware
{
    private $mapper;

    private $router;

    private $template;

    private $uriHelper;

    public function __construct(
        MapperInterface $mapper,
        TemplateRendererInterface $template,
        RouterInterface $router,
        UrlHelper $urlHelper
    ) {
        $this->mapper    = $mapper;
        $this->template  = $template;
        $this->router    = $router;
        $this->uriHelper = new UriHelper($urlHelper);
    }

    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $tag   = str_replace(['+', '%20'], ' ', $req->getAttribute('tag', ''));
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
        $entries    = $this->prepareEntries($page, $posts);

        return new HtmlResponse($this->template->render(
            'blog::list',
            $this->prepareView($tag, $entries, $pagination)
        ));
    }

    private function getPageFromRequest(Request $req) : int
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
    private function preparePagination(string $path, int $page, stdClass $pagination) : stdClass
    {
        $pagination->base_path = $path;
        $pagination->is_first  = ($page === $pagination->first);
        $pagination->is_last   = ($page === $pagination->last);

        $pages = [];
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
     * @return EntryView[]
     */
    private function prepareEntries(int $page, Paginator $posts) : array
    {
        return array_map(function ($post) {
            $post['uriHelper'] = $this->uriHelper;
            return new EntryView($post);
        }, iterator_to_array($posts->getItemsByPage($page)));
    }

    /**
     * @param string $tag
     * @param object $entries
     * @param object $pagination
     * @return array
     */
    private function prepareView(string $tag, array $entries, stdClass $pagination) : array
    {
        $view = $tag ? ['tag' => $tag] : [];
        if ($tag) {
            $view['atom'] = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'atom']);
            $view['rss']  = $this->router->generateUri('blog.tag.feed', ['tag' => $tag, 'type' => 'rss']);
        }

        return array_merge($view, [
            'title'      => $tag ? 'Tag: ' . $tag : 'Blog Posts',
            'posts'      => $entries,
            'pagination' => $pagination,
        ]);
    }
}
