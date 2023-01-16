<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class PostLatestEventListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(PostLatestEvent::class, $container->get(PostLatestEventListener::class));
        return $provider;
    }
}
