<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Webmozart\Assert\Assert;

final class Credentials
{
    public readonly string $accessToken;

    public function __construct(string $accessToken)
    {
        Assert::stringNotEmpty($accessToken, 'Mastodon access_token MUST NOT be empty');
        $this->accessToken     = $accessToken;
    }
}
