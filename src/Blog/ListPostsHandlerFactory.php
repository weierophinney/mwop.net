<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ListPostsHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ListPostsHandler
    {
        return new ListPostsHandler(
            $container->get(__NAMESPACE__ . '\Mapper'),
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
