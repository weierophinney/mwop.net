<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogPostEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class BlogPostEventListenersDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ) : AttachableListenerProvider {
        $provider = $factory();
        $provider->listen(BlogPostEvent::class, $container->get(FetchBlogPostFromCacheListener::class));
        $provider->listen(BlogPostEvent::class, $container->get(FetchBlogPostFromMapperListener::class));
        $provider->listen(BlogPostEvent::class, $container->get(CacheBlogPostListener::class));
        return $provider;
    }
}
