<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

use function getcwd;
use function realpath;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FeedGenerator
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
