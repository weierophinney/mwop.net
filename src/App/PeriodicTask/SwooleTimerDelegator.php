<?php

declare(strict_types=1);

namespace Mwop\App\PeriodicTask;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;
use Swoole\Timer;

class SwooleTimerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $provider->listen(ServerStartEvent::class, function () use ($container): void {
            Timer::tick(1000 * 60 * 60 * 3, $container->get(FetchComics::class));
        });

        return $provider;
    }
}
