<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class ListPostsHandlerFactory
{
    public function __invoke(ContainerInterface $container): ListPostsHandler
    {
        return new ListPostsHandler(
            mapper: $container->get(MapperInterface::class),
            template: $container->get(TemplateRendererInterface::class),
            router: $container->get(RouterInterface::class),
        );
    }
}
