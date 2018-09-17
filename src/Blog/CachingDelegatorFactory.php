<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

class CachingDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        callable $callback,
        array $options = null
    ) : CachingMiddleware {
        $config = $container->get('config')['blog'];

        return new CachingMiddleware(
            $callback(),
            $container->get(CacheItemPoolInterface::class),
            $config['cache_enabled']
        );
    }
}
