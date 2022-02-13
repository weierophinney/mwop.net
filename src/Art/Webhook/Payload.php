<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

class Payload
{
    public function __construct(
        public readonly string $json
    ) {
    }
}
