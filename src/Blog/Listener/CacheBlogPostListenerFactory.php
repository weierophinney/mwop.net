<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogCachePool;
use Phly\Swoole\TaskWorker\QueueableListener;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;

class CacheBlogPostListenerFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        $config  = $container->get('config')['blog'] ?? [];

        return new QueueableListener(
            $container->get(HttpServer::class),
            new CacheBlogPostListener(
                $container->get(BlogCachePool::class),
                $config['cache']['enabled'] ?? false
            )
        );
    }
}
