<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class FetchMastodonFeedDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(PostEvent::class, $container->get(FetchMastodonFeedListener::class));
        return $provider;
    }
}
