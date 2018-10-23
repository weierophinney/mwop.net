<?php

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DebugResourceOwner implements ResourceOwnerInterface
{
    public const USER_ID = 'USER';

    /**
     * @return string
     */
    public function getId()
    {
        return self::USER_ID;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => self::USER_ID,
        ];
    }
}
