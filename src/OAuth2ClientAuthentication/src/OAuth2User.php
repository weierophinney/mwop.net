<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Zend\Expressive\Authentication\UserInterface;

class OAuth2User implements UserInterface
{
    /** @var string */
    private $username;

    /** @var array */
    private $userData;

    public function __construct(string $username, array $userData)
    {
        $this->username = $username;
        $this->userData = $userData;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function getUserRole() : string
    {
        return $this->userData['role'] ?? '';
    }

    public function getUserData() : array
    {
        return $this->userData;
    }
}
