<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Middleware\NotFoundHandler;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class DisplayPostHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayPostHandler
    {
        return new DisplayPostHandler(
            mapper: $container->get(MapperInterface::class),
            template: $container->get(TemplateRendererInterface::class),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            notFoundHandler: $container->get(NotFoundHandler::class),
            disqus: $container->get('config-blog.disqus'),
        );
    }
}
