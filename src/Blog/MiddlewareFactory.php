<?php
namespace Mwop\Blog;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get('Mwop\Blog\EngineMiddleware'),
            $services->get('Mwop\Blog\CachingMiddleware')
        );
    }
}
