<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication\Debug;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DebugResourceOwner implements ResourceOwnerInterface
{
    const USER_ID = 'USER';

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
