<?php
namespace Mwop\Blog;

class EngineMiddlewareFactory
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['blog'];
        return new EngineMiddleware(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('renderer'),
            $config['disqus']
        );
    }
}
