<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Http\Discovery\HttpClientDiscovery;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ApiClientFactory
{
    public function __invoke(ContainerInterface $container): ApiClient
    {
        $config = $container->get('config-blog');

        return new ApiClient(
            $container->get(RequestFactoryInterface::class),
            HttpClientDiscovery::find(),
            $config['images']['openverse']['client_id'],
            $config['images']['openverse']['client_secret'],
        );
    }
}
