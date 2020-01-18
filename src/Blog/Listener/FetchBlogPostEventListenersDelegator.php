<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\FetchBlogPostEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class FetchBlogPostEventListenersDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        $provider = $factory();
        $provider->listen(FetchBlogPostEvent::class, $container->get(FetchBlogPostFromCacheListener::class));
        $provider->listen(FetchBlogPostEvent::class, $container->get(FetchBlogPostFromMapperListener::class));
        $provider->listen(FetchBlogPostEvent::class, $container->get(CacheBlogPostListener::class));
        return $provider;
    }
}
