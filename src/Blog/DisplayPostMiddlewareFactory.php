<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Interop\Container\ContainerInterface;
use Mwop\Blog\Mapper;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Router\RouterInterface;

class DisplayPostMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : DisplayPostMiddleware
    {
        return new DisplayPostMiddleware(
            $container->get(Mapper::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class),
            $container->get('config')['blog']['disqus']
        );
    }
}
