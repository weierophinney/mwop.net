<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Psr\Container\ContainerInterface;
use Mwop\Blog\Mapper\MapperInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container) : FeedGenerator
    {
        return new FeedGenerator(
            $container->get(MapperInterface::class),
            $container->get(RouterInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(ServerUrlHelper::class),
            realpath(getcwd()) . '/data/blog/authors/'
        );
    }
}
