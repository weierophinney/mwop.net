<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Mwop\CacheSessionPersistence;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CacheSessionPersistenceFactory
{
    public function __invoke(ContainerInterface $container) : CacheSessionPersistence
    {
        $config = $container->get('config')['session'] ?? [];
        return new CacheSessionPersistence(
            $container->get(CacheItemPoolInterface::class),
            $container->get(\Psr\Log\LoggerInterface::class),
            $config['cookie-name'] ?? 'MWOPSESS',
            $config['cookie-path'] ?? '/'
        );
    }
}
