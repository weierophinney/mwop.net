<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class UploadHandlerFactory
{
    public function __invoke(ContainerInterface $container): UploadHandler
    {
        return new UploadHandler(
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
