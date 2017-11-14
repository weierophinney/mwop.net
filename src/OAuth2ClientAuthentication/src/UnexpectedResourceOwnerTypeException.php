<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use RuntimeException;
use Zend\Expressive\Authentication\UserInterface;

class UnexpectedResourceOwnerTypeException extends RuntimeException
{
    public static function forResourceOwner(ResourceOwnerInterface $resourceOwner) : self
    {
        return new self(sprintf(
            'Unable to create %s instance; received unknown %s type "%s", '
            . 'which does not implement either a getEmail() or getNickname() method.',
            UserInterface::class,
            ResourceOwnerInterface::class,
            get_class($resourceOwner)
        ));
    }
}
