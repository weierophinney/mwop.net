<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Webmozart\Assert\Assert;

use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class PsrApiClient implements ApiClient
{
    private string $domain;

    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        string $domain,
    ) {
        Assert::stringNotEmpty($domain);
        $this->domain = $domain;
    }

    public function authenticate(Credentials $credentials): Authorization
    {
        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::OAUTH->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode([
            'client_id'     => $credentials->clientId,
            'client_secret' => $credentials->clientSecret,
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob',
            'grant_type'    => 'client_credentials',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            throw Exception\AuthenticationException::fromResponse($this->domain, $response);
        }

        $json   = $response->getBody();
        $values = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        // phpcs:disable Generic.Files.LineLength.TooLong
        Assert::isArray($values, sprintf('Authentication to %s did not return expected payload', $this->domain));
        Assert::keyExists($values, 'access_token', sprintf('Authentication to %s did not return access_token', $this->domain));
        Assert::stringNotEmpty($values['access_token'], sprintf('Authentication to %s returned invalid access_token', $this->domain));
        Assert::keyExists($values, 'token_type', sprintf('Authentication to %s did not return token_type', $this->domain));
        Assert::stringNotEmpty($values['token_type'], sprintf('Authentication to %s returned invalid access_token', $this->domain));
        // phpcs:enable Generic.Files.LineLength.TooLong

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

        $request->getBody()
            ->write(json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            ));

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            return ApiResult::createFailureFromResponse($response);
        }

        return ApiResult::createSuccessFromResponse($response);
    }

    public function uploadMedia(
        Authorization $auth,
        Media $media,
        ?string $description = null,
        ?Media $thumbnail
    ): ApiResult {
        $streamBuilder = new MultipartStreamBuilder($this->streamFactory);
        $streamBuilder->addResource('file', $media->getStream(), ['filename' => $media->filename]);

        if (is_string($description)) {
            $streamBuilder->addResource('description', 'string', ['Content-Type' => 'text/plain']);
        }

        if ($thumbnail instanceof Media) {
            $streamBuilder->addResource('thumbnail', $thumbnail->getStream(), ['filename' => $thumbnail->filename]);
        }

        $stream   = $streamBuilder->build();
        $boundary = $streamBuilder->getBoundary();

        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::MEDIA->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', sprintf('multipare/form-data; boundary=%s', $boundary))
            ->withHeader('Authorization', sprintf('%s %s', $auth->tokenType, $auth->accessToken))
            ->withBody($stream);

        $response = $this->http->sendRequest($request);

        if ($response->getStatusCode() !== StatusCodeInterface::STATUS_OK) {
            return ApiResult::createFailureFromResponse($response);
        }

        return ApiResult::createSuccessFromResponse($response);
    }
}
