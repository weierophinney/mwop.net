<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

abstract class AbstractPayload
{
    final public function __construct(
        public readonly string $json,
    ) {
    }
}
