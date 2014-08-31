<?php
namespace Mwop\Blog;

use Phly\Mustache\Mustache;

class Middleware
{
    private $mapper;
    private $renderer;

    public function __construct(MapperInterface $mapper, Mustache $renderer)
    {
        $this->mapper = $mapper;
        $this->renderer = $renderer;
    }

    public function __invoke($req, $res, $next)
    {
        if (preg_match('#/tag/(?P<tag>[^/]+)#', $req->getUrl()->path, $matches)) {
            return $this->listPosts($req, $res, $next, $matches['tag']);
        }

        if (preg_match('#^/(?P<id>[^/]+)\.html$#', $req->getUrl()->path, $matches)) {
            return $this->displayPost($matches['id'], $req, $res, $next);
        }

        return $this->listPosts($req, $res, $next);
    }

    private function listPosts($req, $res, $next, $tag = null)
    {
        $path  = $req->originalUrl->path;
        $page  = $this->getPageFromRequest($req);
        $title = 'Blog Posts';

        if ($tag) {
            $posts = $this->mapper->fetchAllByTag($tag);
            $title = 'Tag: ' . $tag;
        } else {
            $posts = $this->mapper->fetchAll();
        }

        $posts->setItemCountPerPage(10);
        if ($page > count($posts)) {
            $res->setStatusCode(302);
            $res->addHeader('Location', sprintf('%s?page=%d', $path, count($posts)));
            $res->end();
            return;
        }

        $posts->setCurrentPageNumber($page);

        $pagination = $posts->getPages();
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

        $self    = $this;
        $entries = array_map(function ($post) use ($self, $path) {
            return $self->prepPost($post, $path);
        }, iterator_to_array($posts->getItemsByPage($page)));

        $res->end($this->renderer->render('blog.list', [
            'title'      => $title,
            'posts'      => $entries,
            'pagination' => $pagination,
        ]));
    }

    private function displayPost($id, $req, $res, $next)
    {
        $post = $this->mapper->fetch($id);
        
        if (! $post) {
            $res->setStatusCode(404);
            return $next('Not found');
        }

        $post = include $post['path'];
        if (! $post instanceof EntryEntity) {
            $res->setStatusCode(404);
            return $next('Not found');
        }

        $path = substr($req->originalPath, 0, strlen($req->originalPath) - strlen($req->getUrl()->path));
        $post = $this->prepPost($post->getArrayCopy(), $path);

        $res->end($this->renderer->render('blog.post', $post));
    }

    private function getPageFromRequest($req)
    {
        $page = isset($req->query['page']) ? $req->query['page'] : 1;
        $page = (int) $page;
        return ($page < 1) ? 1 : $page;
    }

    private function prepPost(array $post, $path)
    {
        return new EntryView($post, $path);
    }
}
