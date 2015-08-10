<?php
namespace Mwop\Blog;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $blog = AppFactory::create($services);

        $blog->get('/tag/php.xml', 'Mwop\Blog\FeedMiddleware')
            ->setOptions([
                'values' => [
                    'tag'  => 'php',
                    'type' => 'rss',
                ],
            ]);

        $blog->get('/tag/{tag}/{type}.xml', 'Mwop\Blog\FeedMiddleware')
            ->setOptions([
                'tokens' => [
                    'tag'  => '[^/]+',
                    'type' => '(atom|rss)',
                ],
            ]);

        $blog->get('/tag/{tag}', 'Mwop\Blog\ListPostsMiddleware')
            ->setOptions([
                'tokens' => [
                    'tag'  => '[^/]+',
                ],
            ]);

        $blog->get('/{type}.xml', 'Mwop\Blog\FeedMiddleware')
            ->setOptions([
                'tokens' => [
                    'type'  => '(atom|rss)',
                ],
            ]);

        $blog->get('/{id}.html', 'Mwop\Blog\DisplayPostMiddleware')
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

            $middleware = $services->get('Mwop\Blog\ListPostsMiddleware');
            return $middleware($req, $res, $next);
        });

        return $blog;
    }
}
