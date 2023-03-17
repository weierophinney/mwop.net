<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Cache\Adapter\Predis\PredisCachePool;
use Predis\Client;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

use function assert;

class CachePoolFactory
{
    public function __invoke(ContainerInterface $container): CacheItemPoolInterface
    {
        $predis = $container->get(Client::class);
        assert($predis instanceof Client);

        return new PredisCachePool($predis);
    }
}
