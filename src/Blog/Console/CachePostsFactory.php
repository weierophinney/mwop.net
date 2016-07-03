<?php
namespace Mwop\Blog\Console;

use Mwop\Blog\DisplayPostMiddleware;
use Zend\Expressive\Router\RouterInterface;

class CachePostsFactory
{
    use RoutesTrait;

    public function __invoke($container)
    {
        return new CachePosts(
            $container->get(DisplayPostMiddleware::class),
            $this->seedRoutes($container->get(RouterInterface::class))
        );
    }
}
