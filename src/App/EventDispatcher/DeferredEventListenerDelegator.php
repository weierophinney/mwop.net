<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

final class DeferredEventListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        callable $factory,
    ): AttachableListenerProvider {
        $provider = $factory(); 
        assert($provider instanceof AttachableListenerProvider);

        $listener = $container->get(DeferredEventListener::class);
        assert(is_callable($listener));

        $provider->listen(DeferredEvent::class, $listener);

        return $provider;
    }
}
