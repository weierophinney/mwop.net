<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Mwop\CacheSessionPersistence;
use Mwop\SessionCachePool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CacheSessionPersistenceFactory
{
    public function __invoke(ContainerInterface $container) : CacheSessionPersistence
    {
        $config = $container->get('config')['session'] ?? [];
        return new CacheSessionPersistence(
            $container->get(SessionCachePool::class),
            $config['cookie-name'] ?? 'MWOPSESS',
            $config['cookie-path'] ?? '/'
        );
    }
}
