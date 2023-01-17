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
use function json_encode;
use function sprintf;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class PsrApiClient implements ApiClient
{
    private const VALID_MEDIA_UPLOAD_STATUS = [
        StatusCodeInterface::STATUS_OK,
        StatusCodeInterface::STATUS_ACCEPTED,
    ];

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

    public function createStatus(Credentials $credentials, Status $status): ApiResult
    {
        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::STATUS->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', sprintf('Bearer %s', $credentials->accessToken));

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
        Credentials $credentials,
        Media $media,
        ?string $description = null,
    ): ApiResult {
        $streamBuilder = new MultipartStreamBuilder($this->streamFactory);
        $streamBuilder->addResource('file', $media->getStream(), ['filename' => $media->filename]);

        if (is_string($description)) {
            $streamBuilder->addResource('description', 'string', ['Content-Type' => 'text/plain']);
        }

        $stream   = $streamBuilder->build();
        $boundary = $streamBuilder->getBoundary();

        $request = $this->requestFactory
            ->createRequest(
                RequestMethodInterface::METHOD_POST,
                sprintf('https://%s%s', $this->domain, ApiPath::MEDIA->value),
            )
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', sprintf('multipart/form-data; boundary=%s', $boundary))
            ->withHeader('Authorization', sprintf('Bearer %s', $credentials->accessToken))
            ->withBody($stream);

        $response = $this->http->sendRequest($request);

        if (! in_array($response->getStatusCode(), self::VALID_MEDIA_UPLOAD_STATUS, true)) {
            return ApiResult::createFailureFromResponse($response);
        }

        return ApiResult::createSuccessFromResponse($response);
    }
}
