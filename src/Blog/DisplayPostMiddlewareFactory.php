<?php
namespace Mwop\Blog;

class DisplayPostMiddlewareFactory
{
    public function __invoke($services)
    {
        return new DisplayPostMiddleware(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('Mwop\Template\TemplateInterface'),
            $services->get('Config')['blog']['disqus']
        );
    }
}
