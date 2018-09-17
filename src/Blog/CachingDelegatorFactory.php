<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Expressive\MiddlewareFactory;

class CachingDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        callable $callback,
        array $options = null
    ) : MiddlewareInterface {
        $config = $container->get('config')['blog'] ?? [];
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline([
            new CachingMiddleware(
                $container->get(BlogCachePool::class),
                $config['cache']['enabled'] ?? false
            ),
            $callback(),
        ]);
    }
}
