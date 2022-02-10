<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Http\Client\HttpClient;
use Laminas\Diactoros\Request\Serializer as RequestSerializer;
use Laminas\Diactoros\Response\Serializer;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ApiClient implements HttpClient
{
    private const URI_AUTH   = 'https://api.openverse.engineering/v1/auth_tokens/token/';

    private ?string $accessToken = null;
    private ?string $tokenType = null;

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private HttpClient $http,
        private string $clientId,
        private string $clientSecret,
    ) {
    }
        
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (null === $this->accessToken) {
            $this->authenticate();
        }

        return $this->http->sendRequest(
            $request
                ->withHeader('Authorization', sprintf('%s %s', $this->tokenType, $this->accessToken))
                ->withHeader('Accept', 'application/json')
        );
    }

    private function authenticate(): void
    {
        $payload = sprintf(
            'grant_type=client_credentials&client_id=%s&client_secret=%s',
            $this->clientId,
            $this->clientSecret,
        );

        $request = $this->requestFactory
            ->createRequest('POST', self::URI_AUTH)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Accept', 'application/json');

        $request->getBody()->write($payload);

        $response = $this->http->sendRequest($request);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException(sprintf(
                "Failed to authenticate with status %d:\n%s\n\n%s",
                $response->getStatusCode(),
                RequestSerializer::toString($request),
                Serializer::toString($response),
                // $response->getBody()->__toString(),
            ));
        }

        $data = json_decode($response->getBody()->__toString(), true, flags: JSON_THROW_ON_ERROR);
        if (! isset($data['access_token'])) {
            throw new RuntimeException(sprintf(
                'Authentication payload is missing access_token: %s',
                Serializer::toString($response),
                // $response->getBody()->__toString(),
            ));
        }

        $this->accessToken = $data['access_token'];
        $this->tokenType   = $data['token_type'];
    }
}
