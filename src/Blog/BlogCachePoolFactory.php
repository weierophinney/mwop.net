<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class BlogCachePoolFactory
{
    public function __invoke(ContainerInterface $container): BlogCachePool
    {
        return new BlogCachePool($container->get(CacheItemPoolInterface::class));
    }
}
