<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Interop\Container\ContainerInterface;
use Mwop\Blog\Mapper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ListPostsMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : ListPostsMiddleware
    {
        return new ListPostsMiddleware(
            $container->get(Mapper::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
