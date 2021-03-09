<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Middleware\NotFoundHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class DisplayPostHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayPostHandler
    {
        return new DisplayPostHandler(
            dispatcher: $container->get(EventDispatcherInterface::class),
            template: $container->get(TemplateRendererInterface::class),
            notFoundHandler: $container->get(NotFoundHandler::class),
            disqus: $container->get('config-blog.disqus'),
        );
    }
}
