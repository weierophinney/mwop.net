<?php

declare(strict_types=1);

namespace Mwop\Cron;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class CronDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $config   = $container->get('config-cron');
        $crontab  = (new ConfigParser())($config['jobs'] ?? [], $container->get(LoggerInterface::class));

        if (0 === $crontab->count()) {
            return $provider;
        }

        $provider->listen(
            ServerStartEvent::class,
            function (ServerStartEvent $event) use ($container, $crontab): void {
                // Pull the dispatcher from within the listener to prevent race conditions
                $dispatcher = new Dispatcher(
                    eventDispatcher: $container->get(EventDispatcherInterface::class),
                    crontab: $crontab,
                    logger: $container->get(LoggerInterface::class),
                );

                // Run every minute
                $event->getServer()->tick(1000 * 60, $dispatcher);
            },
        );

        return $provider;
    }
}
