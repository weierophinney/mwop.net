<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

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
