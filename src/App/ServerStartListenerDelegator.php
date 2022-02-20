<?php

declare(strict_types=1);

namespace Mwop\App;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class ServerStartListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $provider->listen(ServerStartEvent::class, new ServerStartListener());

        return $provider;
    }
}
