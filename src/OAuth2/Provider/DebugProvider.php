<?php

declare(strict_types=1);

namespace Mwop\OAuth2\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mwop\OAuth2\DebugResourceOwner;
use Psr\Http\Message\ResponseInterface;

class DebugProvider extends AbstractProvider
{
    public const AUTHORIZATION_URL = '/auth/debug/oauth2callback';
    public const CODE              = 'CODE';
    public const STATE             = 'DEBUG';
    public const TOKEN             = 'TOKEN';

    private string $authorizationUrl;

    public function __construct(array $options = [])
    {
        $this->authorizationUrl = $options['authorization_url'] ?? self::AUTHORIZATION_URL;
    }

    public function getState(): string
    {
        return self::STATE;
    }

    public function getAuthorizationUrl(array $options = []): string
    {
        return $this->authorizationUrl;
    }

    /**
     * @param string $grant
     */
    public function getAccessToken($grant, array $options = []): AccessToken
    {
        return new AccessToken([
            'access_token' => self::TOKEN,
        ]);
    }

    public function getResourceOwner(AccessToken $token): DebugResourceOwner
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
     *
     * @param array|string $data Parsed response data
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
