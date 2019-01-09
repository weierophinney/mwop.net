<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CacheListenerFactory
{
    public function __invoke(ContainerInterface $container) : CacheListener
    {
        return new CacheListener(
            $container->get(BlogCachePool::class)
        );
    }
}
