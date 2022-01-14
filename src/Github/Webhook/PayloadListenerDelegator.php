<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class PayloadListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(
            Payload::class,
            $container->get(PayloadListener::class),
        );
        return $provider;
    }
}
