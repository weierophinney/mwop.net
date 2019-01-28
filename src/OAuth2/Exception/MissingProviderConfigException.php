<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Exception;

use RuntimeException;

use function sprintf;

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
