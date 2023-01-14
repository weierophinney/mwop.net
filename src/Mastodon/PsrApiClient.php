<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Webmozart\Assert\Assert;

final class PsrApiClient implements ApiClient
{
    private string $domain;

    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
        string $domain,
    ) {
        Assert::stringNotEmpty($domain);
        $this->domain = $domain;
    }

    public function authenticate(string $clientId, string $clientSecret): Authorization
    {
        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::OAUTH->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob',
            'grant_type'    => 'client_credentials',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            throw Exception\AuthenticationException::fromResponse($this->domain, $response);
        }

        $json   = $response->getBody();
        $values = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        Assert::isArray($values, sprintf(
            'Authentication to %s did not return expected payload',
            $this->domain,
        ));
        Assert::keyExists($values, 'access_token', sprintf('Authentication to %s did not return access_token', $this->domain));
        Assert::stringNotEmpty($values['access_token'], sprintf('Authentication to %s returned invalid access_token', $this->domain));
        Assert::keyExists($values, 'token_type', sprintf('Authentication to %s did not return token_type', $this->domain));
        Assert::stringNotEmpty($values['token_type'], sprintf('Authentication to %s returned invalid access_token', $this->domain));

        return new Authorization($values['access_token'], $values['token_type']);
    }

    public function createStatus(Authorization $auth, Status $status): ApiResult
    {
        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::STATUS->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', sprintf('%s %s', $auth->tokenType, $auth->accessToken));

        if (is_string($status->idempotencyKey)) {
            $request = $request->withHeader('Idempotency-Key', $status->idempotencyKey);
        }

        $data = [
            'status'     => $status->status,
            'visibility' => $status->visibility->value,
            'language'   => $status->language,
        ];

        if (is_array($status->mediaIds)) {
            Assert::allStringNotEmpty($status->mediaIds, 'Media IDs in status MUST all be string identifiers');
            $data['media_ids'] = $status->mediaIds;
        }

        $request->getBody()->write(json_encode($data , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            return ApiResult::createFailureFromResponse($response);
        }

        return ApiResult::createSuccessFromResponse($response);
    }

    public function uploadMedia(Authorization $auth, Media $media, ?string $description = null, ?Media $thumbnail): ApiResult
    {
        throw new \Exception('Method not implemented');
    }
}
