<?php
namespace Mwop\Blog\Console;

use Mwop\Blog\DisplayPostMiddleware;
use Zend\Expressive\Router\RouterInterface;

class CachePostsFactory
{
    use RoutesTrait;

    public function __invoke($services)
    {
        return new CachePosts(
            $services->get(DisplayPostMiddleware::class),
            $this->seedRoutes($services->get(RouterInterface::class))
        );
    }
}
