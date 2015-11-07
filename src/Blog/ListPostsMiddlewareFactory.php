<?php
namespace Mwop\Blog;

use Mwop\Blog\Mapper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ListPostsMiddlewareFactory
{
    public function __invoke($services)
    {
        return new ListPostsMiddleware(
            $services->get(Mapper::class),
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class)
        );
    }
}
