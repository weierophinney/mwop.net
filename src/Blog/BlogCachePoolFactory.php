<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Predis\Client;
use Psr\Container\ContainerInterface;

class BlogCachePoolFactory
{
    public function __invoke(ContainerInterface $container) : BlogCachePool
    {
        $config = $container->get('config')['blog']['cache'] ?? [];
        $connectionParameters = $config['connection-parameters']; // required
        $clientOptions = $config['client-options'] ?? []; // optional
        return new BlogCachePool(new Client($connectionParameters, $clientOptions));
    }
}
