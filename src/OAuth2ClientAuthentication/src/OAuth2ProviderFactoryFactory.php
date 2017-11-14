<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Psr\Container\ContainerInterface;

class OAuth2ProviderFactoryFactory
{
    public function __invoke(ContainerInterface $container) : OAuth2ProviderFactory
    {
        return new OAuth2ProviderFactory($container);
    }
}
