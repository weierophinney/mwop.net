<?php

namespace Mwop\OAuth2\Exception;

use RuntimeException;

class MissingProviderConfigException extends RuntimeException implements ExceptionInterface
{
    public static function forProvider(string $provider) : self
    {
        return new self(sprintf(
            'No configuration found for OAuth2 provider "%s"; please provide it via '
            . 'the config key oauth2clientauthentication.%s',
            $provider,
            $provider
        ));
    }
}
