<?php
namespace Mwop\Blog;

use Zend\Expressive\AppFactory;
use Zend\Expressive\Router\Route;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $blog = AppFactory::create($services);

        $blog->get('/{id:[^/]+}.html', DisplayPostMiddleware::class, 'blog.post');
        $blog->get('/tag/php.xml', FeedMiddleware::class, 'blog.feed.php');
        $blog->get('/tag/{tag:[^/]+}/{type:atom|rss}.xml', FeedMiddleware::class, 'blog.feed.tag');
        $blog->get('/tag/{tag:[^/]+}', ListPostsMiddleware::class, 'blog.tag');
        $blog->get('/{type:atom|rss}.xml', FeedMiddleware::class, 'blog.feed');

        $blog->pipe('/', function ($req, $res, $next) use ($services) {
            if (strtoupper($req->getMethod()) !== 'GET') {
                $res = $res
                    ->withStatus(405)
                    ->withHeader('Allow', 'GET');
                return $next($req, $res, $next, 405);
            }

            $middleware = $services->get(ListPostsMiddleware::class);
            return $middleware($req, $res, $next);
        });

        return $blog;
    }
}
