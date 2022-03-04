<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ClearResponseCacheHandlerFactory
{
    public function __invoke(ContainerInterface $container): ClearResponseCacheHandler
    {
        return new ClearResponseCacheHandler(
            cache: $container->get('Mwop\App\ResponseCachePool'),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
