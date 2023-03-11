<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Predis\Client;
use Psr\Container\ContainerInterface;

final class PredisClientFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        $config               = $container->get('config-redis');
        $connectionParameters = $config['connection-parameters']; // required
        $clientOptions        = $config['client-options'] ?? []; // optional
        return new Client($connectionParameters, $clientOptions);
    }
}
