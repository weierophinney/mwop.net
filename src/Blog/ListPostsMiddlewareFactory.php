<?php
namespace Mwop\Blog;

class ListPostsMiddlewareFactory
{
    public function __invoke($services)
    {
        return new ListPostsMiddleware(
            $services->get('Mwop\Blog\Mapper'),
            $services->get('Mwop\Template\TemplateInterface')
        );
    }
}
