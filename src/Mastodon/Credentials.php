<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Webmozart\Assert\Assert;

final class Credentials
{
    public readonly string $clientId;
    public readonly string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        Assert::stringNotEmpty($clientId, 'Mastodon client_id MUST NOT be empty');
        Assert::stringNotEmpty($clientSecret, 'Mastodon client_secret MUST NOT be empty');

        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
    }
}
