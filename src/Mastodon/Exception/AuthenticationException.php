<?php

declare(strict_types=1);

namespace Mwop\Mastodon\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class AuthenticationException extends RuntimeException
{
    private string $response;

    public static function fromResponse(string $domain, ResponseInterface $response): self
    {
        $instance = new self(
            sprintf('Authentication to Mastodon instance %s failed', $domain),
            $response->getStatusCode(),
        );
        $instance->response = $response->getBody()->__toString();

        return $instance;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
