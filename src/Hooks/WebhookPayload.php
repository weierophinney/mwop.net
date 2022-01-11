<?php

declare(strict_types=1);

namespace Mwop\Hooks;

class WebhookPayload
{
    public function __construct(
        public readonly string $payload
    ) {
    }
}
