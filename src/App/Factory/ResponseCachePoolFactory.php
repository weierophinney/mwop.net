<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Cache\Namespaced\NamespacedCachePool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class ResponseCachePoolFactory
{
    public function __invoke(ContainerInterface $container): NamespacedCachePool
    {
        return new NamespacedCachePool(
            cachePool: $container->get(CacheItemPoolInterface::class),
            namespace: 'response',
        );
    }
}
