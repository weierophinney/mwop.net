<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogCachePool;
use Psr\Container\ContainerInterface;

class FetchBlogPostFromCacheListenerFactory
{
    public function __invoke(ContainerInterface $container): FetchBlogPostFromCacheListener
    {
        $config = $container->get('config-blog.cache');

        return new FetchBlogPostFromCacheListener(
            cache: $container->get(BlogCachePool::class),
            enabled: $config['enabled'] ?? false,
        );
    }
}
