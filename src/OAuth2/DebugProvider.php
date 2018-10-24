<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class DebugProvider extends AbstractProvider
{
    public const AUTHORIZATION_URL = '/auth/debug/oauth2callback';
    public const CODE = 'CODE';
    public const STATE = 'DEBUG';
    public const TOKEN = 'TOKEN';

    /**
     * @var string
     */
    private $authorizationUrl;

    public function __construct(array $options = [])
    {
        $this->authorizationUrl = $options['authorization_url'] ?? self::AUTHORIZATION_URL;
    }

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
        return $this->authorizationUrl;
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
