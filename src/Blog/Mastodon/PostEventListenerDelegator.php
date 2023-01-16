<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class PostEventListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(PostEvent::class, $container->get(PostEventListener::class));
        return $provider;
    }
}
