<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class CacheListenerDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory) : AttachableListenerProvider
    {
        $provider = $factory();
        $provider->listen(
            CacheBlogPostEvent::class,
            $container->get(CacheListener::class)
        );
        return $provider;
    }
}
