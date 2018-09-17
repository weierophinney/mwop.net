<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Mwop\SessionCachePool;
use Predis\Client;
use Psr\Container\ContainerInterface;

class SessionCachePoolFactory
{
    public function __invoke(ContainerInterface $container) : SessionCachePool
    {
        $config = $container->get('config')['session']['cache'];
        $connectionParameters = $config['connection-parameters']; // required
        $clientOptions = $config['client-options'] ?? []; // optional
        return new SessionCachePool(new Client($connectionParameters, $clientOptions));
    }
}
