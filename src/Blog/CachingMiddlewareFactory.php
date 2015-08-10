<?php
namespace Mwop\Blog;

class CachingMiddlewareFactory
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['blog'];

        return new CachingMiddleware(
            $config['cache_path'],
            $config['cache_enabled']
        );
    }
}
