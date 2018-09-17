<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Mwop\Blog\BlogCachePool;
use Psr\Container\ContainerInterface;

class ClearCacheFactory
{
    public function __invoke(ContainerInterface $container) : ClearCache
    {
        return new ClearCache(
            $container->get(BlogCachePool::class)
        );
    }
}
