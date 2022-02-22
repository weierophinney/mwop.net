<?php

declare(strict_types=1);

namespace Mwop\Art;

use Psr\Container\ContainerInterface;

class PhotoStorageFactory
{
    public function __invoke(ContainerInterface $container): PhotoStorage
    {
        return new PhotoStorage(
            images: $container->get(__NAMESPACE__ . '\Storage\Images'),
            thumbnails: $container->get(__NAMESPACE__ . '\Storage\Thumbnails'),
        );
    }
}
