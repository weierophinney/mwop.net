<?php
namespace Mwop\Blog\Console;

use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouterInterface;

trait RoutesTrait
{
    private $routes = [
        'blog'               => '/blog[/]',
        'blog.post'          => '/blog/{id:[^/]+}.html',
        'blog.feed.php'      => '/blog/tag/{tag:php}.xml',
        'blog.feed.php.also' => '/blog/{tag:php}.xml',
        'blog.tag.feed'      => '/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml',
        'blog.tag'           => '/blog/tag/{tag:[^/]+}',
        'blog.feed'          => '/blog/{type:atom|rss}.xml',
        'contact'            => '/contact[/]',
        'home'               => '/',
        'resume'             => '/resume',
    ];

    private function seedRoutes(RouterInterface $router)
    {
        $middleware = function () {
        };

        foreach ($this->routes as $name => $path) {
            $router->addRoute(new Route($path, $middleware, ['GET'], $name));
        }

        return $router;
    }
}
