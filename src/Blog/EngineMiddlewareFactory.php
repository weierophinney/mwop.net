<?php
namespace Mwop\Blog;

class EngineMiddlewareFactory
{
    public function __invoke($services)
    {
        return new EngineMiddleware(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('renderer')
        );
    }
}
