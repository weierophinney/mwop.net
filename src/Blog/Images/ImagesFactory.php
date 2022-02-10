<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ImagesFactory
{
    public function __invoke(ContainerInterface $container): Images
    {
        return new Images(
            $container->get(ApiClient::class),
            $container->get(RequestFactoryInterface::class),
        );
    }
}
