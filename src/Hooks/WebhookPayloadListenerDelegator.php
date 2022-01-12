<?php

declare(strict_types=1);

namespace Mwop\Hooks;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class WebhookPayloadListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(WebhookPayload::class, $container->get(WebhookPayloadListener::class));
        return $provider;
    }
}
