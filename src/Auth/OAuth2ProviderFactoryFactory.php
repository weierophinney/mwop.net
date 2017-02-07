<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class OAuth2ProviderFactoryFactory
{
    public function __invoke(ContainerInterface $container) : OAuth2ProviderFactory
    {
        return new OAuth2ProviderFactory($container);
    }
}
