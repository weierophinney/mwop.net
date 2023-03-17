<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

final class DeferredEvent
{
    public function __construct(
        public readonly object $wrappedEvent,
    ) {
    }
}
