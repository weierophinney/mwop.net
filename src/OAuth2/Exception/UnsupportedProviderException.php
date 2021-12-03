<?php

declare(strict_types=1);

namespace Mwop\OAuth2\Exception;

use RuntimeException;

use function implode;
use function sprintf;

final class UnsupportedProviderException extends RuntimeException implements ExceptionInterface
{
    public static function forProvider(string $provider, array $knownProviders): self
    {
        return new self(sprintf(
            'Unsupported OAuth2 provider "%s"; must be one of: %s',
            $provider,
            implode(', ', $knownProviders)
        ));
    }
}
