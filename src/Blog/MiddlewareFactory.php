<?php
namespace Mwop\Blog;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('renderer')
        );
    }
}
