<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use function json_encode;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class AccountNotFoundResult implements AccountResult
{
    public function __construct(
        private readonly string $account,
    ) {
    }

    public function getStatus(): int
    {
        return 404;
    }

    public function getContentType(): string
    {
        return 'application/problem+json';
    }

    public function getContent(): string
    {
        return json_encode(
            flags: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            value: [
                'title'  => 'Account Unknown',
                'type'   => 'https://mwop.net/api/problem/account-unknown',
                'status' => $this->getStatus(),
                'detail' => "Unable to find information for account {$this->account}",
            ],
        );
    }
}
