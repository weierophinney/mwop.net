<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use function json_encode;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class DiscoveredAccountResult implements AccountResult
{
    public function __construct(
        private Account $account,
    ) {
    }

    public function getStatus(): int
    {
        return 200;
    }

    public function getContentType(): string
    {
        return 'application/jrd+json';
    }

    public function getContent(): string
    {
        return json_encode(
            flags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            value: $this->account,
        );
    }
}
