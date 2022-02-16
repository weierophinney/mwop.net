<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mwop\Art\PhotoStorage;
use Psr\Container\ContainerInterface;

class ImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): ImageHandler
    {
        return new ImageHandler(
            photos: $container->get(PhotoStorage::class),
        );
    }
}
