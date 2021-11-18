<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class TweetLatestEventListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(TweetLatestEvent::class, $container->get(TweetLatestEventListener::class));
        return $provider;
    }
}
