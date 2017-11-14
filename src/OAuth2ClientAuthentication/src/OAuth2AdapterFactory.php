<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Psr\Container\ContainerInterface;

class OAuth2AdapterFactory
{
    public function __invoke(ContainerInterface $container) : OAuth2Adapter
    {
        return new OAuth2Adapter(
            $container->get(OAuth2ProviderFactory::class),
            $container->get(UnauthorizedResponseFactory::class),
            $container->get(RedirectResponseFactory::class)
        );
    }
}
