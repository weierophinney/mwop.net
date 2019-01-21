<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Util;

use Phly\Swoole\TaskWorker\QueueableListener;
use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;

class QueueableListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ) : object {
        $listener = $factory();
        if (! is_callable($listener)) {
            return $listener;
        }

        return new QueueableListener(
            $container->get(HttpServer::class),
            $listener
        );
    }
}
