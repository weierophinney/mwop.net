<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Container\ContainerInterface;

class PhotoHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PhotoHandler
    {
        return new PhotoHandler(
            mapper: $container->get(PhotoMapper::class),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
