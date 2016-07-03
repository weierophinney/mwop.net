<?php
namespace Mwop\Blog;

use Mwop\Blog\Mapper;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Router\RouterInterface;

class DisplayPostMiddlewareFactory
{
    public function __invoke($container)
    {
        return new DisplayPostMiddleware(
            $container->get(Mapper::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class),
            $container->get('config')['blog']['disqus']
        );
    }
}
