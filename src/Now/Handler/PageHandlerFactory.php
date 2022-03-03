<?php

declare(strict_types=1);

namespace Mwop\Now\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PageHandlerFactory
{
    public function __invoke(ContainerInterface $container): PageHandler
    {
        return new PageHandler(
            archives: $container->get('Mwop\Now\Archives'),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
            fs: $container->get('Mwop\Now\NowAndThenFilesystem'),
        );
    }
}
