<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class TweetPostEventListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(TweetPostEvent::class, $container->get(TweetPostEventListener::class));
        return $provider;
    }
}
