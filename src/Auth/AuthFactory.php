<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class AuthFactory
{
    public function __invoke(ContainerInterface $container) : Auth
    {
        return new Auth(
            $container->get(OAuth2ProviderFactory::class),
            $container->get('Mwop\UnauthorizedResponseFactory')
        );
    }
}
