<?php
namespace Mwop\Blog;

use Phly\Mustache\Mustache;
use Phly\Http\Stream;

class Middleware
{
    private $feedPath;
    private $mapper;
    private $renderer;

    public function __construct(MapperInterface $mapper, Mustache $renderer, $feedPath = 'data/feeds')
    {
        $this->mapper   = $mapper;
        $this->renderer = $renderer;
        $this->feedPath = $feedPath;
    }

    public function __invoke($req, $res, $next)
    {
        if (preg_match('#/tag/(?P<tag>[^/]+)#', $req->getUrl()->path, $matches)) {
            $tag = $matches['tag'];
            if (preg_match('#/(?P<feed>atom|rss)\.xml$#', $req->getUrl()->path, $matches)) {
                return $this->displayFeed($req, $res, $next, $matches['feed'], $tag);
            }
            return $this->listPosts($req, $res, $next, $matches['tag']);
        }

        if (preg_match('#^/(?P<id>[^/]+)\.html$#', $req->getUrl()->path, $matches)) {
            return $this->displayPost($matches['id'], $req, $res, $next);
        }

        if (preg_match('#/(?P<feed>atom|rss)\.xml$#', $req->getUrl()->path, $matches)) {
            return $this->displayFeed($req, $res, $next, $matches['feed']);
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

        $self     = $this;

        // Strip "/tag/<tag>" from base path in order to create paths to posts
        $postPath = ($tag) ? substr($path, 0, - (strlen($tag) + 5)) : $path;
        $entries  = array_map(function ($post) use ($self, $postPath) {
            return $self->prepPost($post, $postPath);
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

        $path = substr($req->originalUrl, 0, -(strlen($post->getId()) + 6));
        $post = $this->prepPost($post->getArrayCopy(), $path);

        $res->end($this->renderer->render('blog.post', $post));
    }

    private function displayFeed($req, $res, $next, $type, $tag = null)
    {
        if ($tag) {
            $path = sprintf('%s/%s.%s.xml', $this->feedPath, str_replace(' ', '+', $tag), $type);
        } else {
            $path = sprintf('%s/%s.xml', $this->feedPath, $type);
        }

        if (! file_exists($path)) {
            $res->setStatusCode(404);
            return $next('Not found');
        }

        $res->addHeader('Content-Type', sprintf('application/%s+xml', $type));
        $res->setBody(new Stream(fopen($path, 'r')));
        $res->end();
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
