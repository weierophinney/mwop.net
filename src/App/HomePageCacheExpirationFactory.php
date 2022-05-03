<?php

declare(strict_types=1);

namespace Mwop\App;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class HomePageCacheExpirationFactory
{
    public function __invoke(ContainerInterface $container): HomePageCacheExpiration
    {
        return new HomePageCacheExpiration(
            cache: $container->get(CacheItemPoolInterface::class),
        );
    }
}
