<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class SessionCachePoolFactory
{
    public function __invoke(ContainerInterface $container): SessionCachePool
    {
        return new SessionCachePool($container->get(CacheItemPoolInterface::class));
    }
}
