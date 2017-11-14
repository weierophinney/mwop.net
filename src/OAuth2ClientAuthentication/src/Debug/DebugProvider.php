<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication\Debug;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class DebugProvider extends AbstractProvider
{
    const AUTHORIZATION_URL = '/auth/debug/authorize';
    const CODE = 'CODE';
    const STATE = 'DEBUG';
    const TOKEN = 'TOKEN';

    /**
     * @return string
     */
    public function getState()
    {
        return self::STATE;
    }

    /**
     * @param array $options
     * @return string
     */
    public function getAuthorizationUrl(array $options = [])
    {
        return self::AUTHORIZATION_URL;
    }

    /**
     * @param string $grant
     * @param array $options
     * @return AccessToken
     */
    public function getAccessToken($grant, array $options = [])
    {
        return new AccessToken([
            'access_token' => self::TOKEN,
        ]);
    }

    /**
     * @param AccessToken $token
     * @return DebugResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        return new DebugResourceOwner();
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    public function getBaseAuthorizationUrl()
    {
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    public function getBaseAccessTokenUrl(array $params)
    {
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    protected function getDefaultScopes()
    {
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
    }

    /**
     * No-op; implemented to fulfill abstract parent class.
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
    }
}
