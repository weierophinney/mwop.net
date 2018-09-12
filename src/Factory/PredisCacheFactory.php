<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Cache\Adapter\Predis\PredisCachePool;
use Predis\Client;
use Psr\Container\ContainerInterface;

class PredisCacheFactory
{
    public function __invoke(ContainerInterface $container) : PredisCachePool
    {
        $config = $container->get('config')['cache'];
        $connectionParameters = $config['connection-parameters']; // required
        $clientOptions = $config['client-options'] ?? []; // optional
        return new PredisCachePool(new Client($connectionParameters, $clientOptions));
    }
}
