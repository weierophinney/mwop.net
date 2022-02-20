<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PhotosHandlerFactory
{
    public function __invoke(ContainerInterface $container): PhotosHandler
    {
        $config = $container->get('config-art');
        return new PhotosHandler(
            mapper: $container->get(PhotoMapper::class),
            perPage: $config['per_page'],
            responseFactory: $container->get(ResponseFactoryInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
