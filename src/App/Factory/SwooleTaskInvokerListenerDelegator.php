<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Mezzio\Swoole\Event\RequestEvent;
use Mezzio\Swoole\Event\RequestHandlerRequestListener;
use Mezzio\Swoole\Event\ServerShutdownEvent;
use Mezzio\Swoole\Event\ServerShutdownListener;
use Mezzio\Swoole\Event\ServerStartEvent;
use Mezzio\Swoole\Event\ServerStartListener;
use Mezzio\Swoole\Event\StaticResourceRequestListener;
use Mezzio\Swoole\Event\TaskEvent;
use Mezzio\Swoole\Event\WorkerStartEvent;
use Mezzio\Swoole\Event\WorkerStartListener;
use Mezzio\Swoole\Task\TaskInvokerListener;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class SwooleTaskInvokerListenerDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): AttachableListenerProvider
    {
        $provider = $factory();

        $provider->listen(ServerStartEvent::class, $container->get(ServerStartListener::class));
        $provider->listen(WorkerStartEvent::class, $container->get(WorkerStartListener::class));
        $provider->listen(RequestEvent::class, $container->get(StaticResourceRequestListener::class));
        $provider->listen(RequestEvent::class, $container->get(RequestHandlerRequestListener::class));
        $provider->listen(ServerShutdownEvent::class, $container->get(ServerShutdownListener::class));
        $provider->listen(TaskEvent::class, $container->get(TaskInvokerListener::class));

        return $provider;
    }
}
