<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Mwop\Art\Storage\PhotoRetrieval;
use Psr\Container\ContainerInterface;

class ImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): ImageHandler
    {
        return new ImageHandler(
            photos: $container->get(PhotoRetrieval::class),
        );
    }
}
