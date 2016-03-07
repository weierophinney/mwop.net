<?php
namespace Mwop\Blog\Console;

use Mwop\Blog\DisplayPostMiddleware;
use Zend\Expressive\Router\RouterInterface;

class CachePostsFactory
{
    public function __invoke($services)
    {
        return new CachePosts(
            $services->get(DisplayPostMiddleware::class),
            $services->get(RouterInterface::class)
        );
    }
}
