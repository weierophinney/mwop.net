<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Container\ContainerInterface;
use Zend\Stratigility\MiddlewarePipe;

class CachingDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        callable $callback,
        array $options = null
    ) : MiddlewarePipe {
        $config = $container->get('config')['blog'] ?? [];

        $pipeline = new MiddlewarePipe();
        $pipeline->pipe(new CachingMiddleware(
            $container->get(BlogCachePool::class),
            $config['cache']['enabled'] ?? false
        ));
        $pipeline->pipe($callback());

        return $pipeline;
    }
}
