<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DebugResourceOwner implements ResourceOwnerInterface
{
    public const USER_ID = 'USER';

    public function getId(): string
    {
        return self::USER_ID;
    }

    public function toArray(): array
    {
        return [
            'id' => self::USER_ID,
        ];
    }
}
