<?php
namespace Mwop\Blog;

use Mwop\Blog\Mapper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ListPostsMiddlewareFactory
{
    public function __invoke($container)
    {
        return new ListPostsMiddleware(
            $container->get(Mapper::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
