<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use Psr\Container\ContainerInterface;

class PhotoInfoCommandFactory
{
    public function __invoke(ContainerInterface $container): PhotoInfoCommand
    {
        return new PhotoInfoCommand($container->get(Photos::class));
    }
}
