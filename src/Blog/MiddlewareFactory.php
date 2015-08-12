<?php
namespace Mwop\Blog;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $blog = AppFactory::create($services);

        $blog->get('/tag/php.xml', FeedMiddleware::class)
            ->setOptions([
                'values' => [
                    'tag'  => 'php',
                    'type' => 'rss',
                ],
            ]);

        $blog->get('/tag/{tag}/{type}.xml', FeedMiddleware::class)
            ->setOptions([
                'tokens' => [
                    'tag'  => '[^/]+',
                    'type' => '(atom|rss)',
                ],
            ]);

        $blog->get('/tag/{tag}', ListPostsMiddleware::class)
            ->setOptions([
                'tokens' => [
                    'tag'  => '[^/]+',
                ],
            ]);

        $blog->get('/{type}.xml', FeedMiddleware::class)
            ->setOptions([
                'tokens' => [
                    'type'  => '(atom|rss)',
                ],
            ]);

        $blog->get('/{id}.html', DisplayPostMiddleware::class)
            ->setOptions([
                'tokens' => [
                    'id'  => '[^/]',
                ],
            ]);

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
