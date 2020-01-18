<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Factory;

use Cache\Adapter\Predis\PredisCachePool;
use Predis\Client;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CachePoolFactory
{
    public function __invoke(ContainerInterface $container): CacheItemPoolInterface
    {
        $config               = $container->get('config-cache');
        $connectionParameters = $config['connection-parameters']; // required
        $clientOptions        = $config['client-options'] ?? []; // optional
        return new PredisCachePool(new Client($connectionParameters, $clientOptions));
    }
}
