<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Mwop\Blog\BlogCachePool;
use Psr\Container\ContainerInterface;

class ClearCacheFactory
{
    public function __invoke(ContainerInterface $container): ClearCache
    {
        return new ClearCache(
            $container->get(BlogCachePool::class)
        );
    }
}
