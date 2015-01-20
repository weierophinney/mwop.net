<?php
namespace Mwop\Blog;

use Phly\Mustache\Mustache;
use Phly\Http\Stream;

class EngineMiddleware
{
    private $disqus;
    private $feedPath;
    private $mapper;

    public function __construct(
        MapperInterface $mapper,
        array $disqus = [],
        $feedPath = 'data/feeds'
    ) {
        $this->mapper   = $mapper;
        $this->feedPath = $feedPath;
        $this->disqus   = $disqus;
    }

    public function __invoke($req, $res, $next)
    {
        $path = $req->getUri()->getPath();
        if ('/tag/php.xml' === $path) {
            return $this->displayFeed($req, $res, $next, 'rss', 'php');
        }

        if (preg_match('#/tag/(?P<tag>[^/]+)#', $path, $matches)) {
            $tag = $matches['tag'];
            if (preg_match('#/(?P<feed>atom|rss)\.xml$#', $path, $matches)) {
                return $this->displayFeed($req, $res, $next, $matches['feed'], $tag);
            }
            return $this->listPosts($req, $res, $next, $tag);
        }

        if (preg_match('#^/(?P<id>[^/]+)\.html$#', $path, $matches)) {
            return $this->displayPost($matches['id'], $req, $res, $next);
        }

        if (preg_match('#/(?P<feed>atom|rss)\.xml$#', $path, $matches)) {
            return $this->displayFeed($req, $res, $next, $matches['feed']);
        }
        return $this->listPosts($req, $res, $next);
    }

    private function listPosts($req, $res, $next, $tag = null)
    {
        $path  = $req->getOriginalRequest()->getUri()->getPath();
        $page  = $this->getPageFromRequest($req);
        $title = 'Blog Posts';

        if ($tag) {
            $tag   = str_replace(array('+', '%20'), ' ', $tag);
            $posts = $this->mapper->fetchAllByTag($tag);
            $title = 'Tag: ' . $tag;
        } else {
            $posts = $this->mapper->fetchAll();
        }

        $posts->setItemCountPerPage(10);
        if (count($posts) && $page > count($posts)) {
            return $res
                ->withStatus(302)
                ->withHeader('Location', sprintf('%s?page=%d', $path, count($posts)))
                ->end();
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

        $view = [
            'title'      => $title,
            'posts'      => $entries,
            'pagination' => $pagination,
        ];

        if ($tag) {
            $view['tag'] = ['tag' => $tag];
        }

        return $next($req->withAttribute('view', (object) [
            'template' => 'blog.list',
            'model'    => $view,
        ]));
    }

    private function displayPost($id, $req, $res, $next)
    {
        $post = $this->mapper->fetch($id);
        
        if (! $post) {
            return $next('Not found', $res->withStatus(404));
        }

        $post = include $post['path'];
        if (! $post instanceof EntryEntity) {
            return $next('Not found', $res->withStatus(404));
        }

        $original = $req->getOriginalRequest()->getUri()->getPath();
        $path = substr($original, 0, -(strlen($post->getId() . '.html') + 1));
        $post = $this->prepPost($post->getArrayCopy(), $path);

        return $next($req->withAttribute('view', (object) [
            'template' => 'blog.post',
            'model'    => $post,
        ]));
    }

    private function displayFeed($req, $res, $next, $type, $tag = null)
    {
        if ($tag) {
            $path = sprintf('%s/%s.%s.xml', $this->feedPath, str_replace([' ', '%20'], '+', $tag), $type);
        } else {
            $path = sprintf('%s/%s.xml', $this->feedPath, $type);
        }

        if (! file_exists($path)) {
            $res->setStatus(404);
            return $next('Not found');
        }

        return $res
            ->withHeader('Content-Type', sprintf('application/%s+xml', $type))
            ->withBody(new Stream(fopen($path, 'r')))
            ->end();
    }

    private function getPageFromRequest($req)
    {
        $page = isset($req->getQueryParams()['page']) ? $req->getQueryParams()['page'] : 1;
        $page = (int) $page;
        return ($page < 1) ? 1 : $page;
    }

    private function prepPost(array $post, $path)
    {
        return new EntryView($post, $path, $this->disqus);
    }
}
