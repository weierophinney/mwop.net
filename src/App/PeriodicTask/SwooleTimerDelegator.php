<?php

declare(strict_types=1);

namespace Mwop\App\PeriodicTask;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
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

        $provider->listen(ComicsEvent::class, $container->get(FetchComics::class));
        $provider->listen(ServerStartEvent::class, function () use ($container): void {
            // Pull the dispatcher from within the listener to prevent race conditions
            $dispatcher = $container->get(EventDispatcherInterface::class);

            // Fetch comics every 3 hours
            Timer::tick(1000 * 60 * 60 * 3, function () use ($dispatcher) {
                /** @var EventDispatcherInterface $dispatcher */
                $dispatcher->dispatch(new ComicsEvent());
            });
        });

        return $provider;
    }
}
