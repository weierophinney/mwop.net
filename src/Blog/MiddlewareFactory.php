<?php
namespace Mwop\Blog;

use Zend\Expressive\AppFactory;
use Zend\Expressive\Router\Route;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $blog = AppFactory::create($services);

        $route = new Route('/{id}.html', DisplayPostMiddleware::class, [ 'GET' ]);
        $route->setOptions([
            'tokens' => [
                'id'  => '[^/]+',
            ],
        ]);
        $blog->route($route);

        $route = new Route('/tag/php.xml', FeedMiddleware::class, [ 'GET' ]);
        $route->setOptions([
            'values' => [
                'tag'  => 'php',
                'type' => 'rss',
            ],
        ]);
        $blog->route($route);

        $route = new Route('/tag/{tag}/{type}.xml', FeedMiddleware::class, [ 'GET' ]);
        $route->setOptions([
            'tokens' => [
                'tag'  => '[^/]+',
                'type' => '(atom|rss)',
            ],
        ]);
        $blog->route($route);

        $route = new Route('/tag/{tag}', ListPostsMiddleware::class, [ 'GET' ]);
        $route->setOptions([
            'tokens' => [
                'tag'  => '[^/]+',
            ],
        ]);
        $blog->route($route);

        $route = new Route('/{type}.xml', FeedMiddleware::class, [ 'GET' ]);
        $route->setOptions([
            'tokens' => [
                'type'  => '(atom|rss)',
            ],
        ]);
        $blog->route($route);

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
