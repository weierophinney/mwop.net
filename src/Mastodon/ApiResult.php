<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function is_object;
use function json_decode;
use function stripos;

use const JSON_THROW_ON_ERROR;

final class ApiResult
{
    private function __construct(
        private readonly bool $status,
        public readonly ?ResponseInterface $response,
        public readonly ?Throwable $error,
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->status;
    }

    public function getResponseObject(): ?object
    {
        if (! $this->response instanceof ResponseInterface) {
            return null;
        }

        $contentType = $this->response->getHeaderLine('Content-Type');
        if (stripos($contentType, 'json') === false) {
            return null;
        }

        try {
            $decoded = json_decode((string) $this->response->getBody(), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (! is_object($decoded)) {
            return null;
        }

        return $decoded;
    }
}
