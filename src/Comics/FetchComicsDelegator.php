<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class FetchComicsDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();
        $provider->listen(ComicsEvent::class, $container->get(FetchComics::class));
        return $provider;
    }
}
