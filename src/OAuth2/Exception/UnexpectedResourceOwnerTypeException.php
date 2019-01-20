<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Exception;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use RuntimeException;

class UnexpectedResourceOwnerTypeException extends RuntimeException
{
    public static function forResourceOwner(ResourceOwnerInterface $resourceOwner) : self
    {
        return new self(sprintf(
            'Unable to obtain a username from authenticated user; received unknown %s type "%s", '
            . 'which does not implement either a getEmail() or getNickname() method.',
            ResourceOwnerInterface::class,
            get_class($resourceOwner)
        ));
    }
}
