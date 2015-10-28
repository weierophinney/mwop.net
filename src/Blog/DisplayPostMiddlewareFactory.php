<?php
namespace Mwop\Blog;

use Mwop\Blog\Mapper;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Router\RouterInterface;

class DisplayPostMiddlewareFactory
{
    public function __invoke($services)
    {
        return new DisplayPostMiddleware(
            $services->get(Mapper::class),
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class),
            $services->get('config')['blog']['disqus']
        );
    }
}
