<?php

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;

class ClearResponseCacheFactory
{
    public function __invoke(ContainerInterface $container): ClearResponseCache
    {
        return new ClearResponseCache(
            cache: $container->get('Mwop\App\ResponseCachePool'),
        );
    }
}
