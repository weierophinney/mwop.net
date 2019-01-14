<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Phly\Swoole\TaskWorker\QueueableListener;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;

class CacheListenerFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        return new QueueableListener(
            $container->get(HttpServer::class),
            new CacheListener($container->get(BlogCachePool::class))
        );
    }
}
