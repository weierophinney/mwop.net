<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class AuthCallbackFactory
{
    public function __invoke(ContainerInterface $container) : AuthCallback
    {
        $config = $container->get('config');
        $config = $config['opauth'];
        return new AuthCallback($config, $container->get('session'));
    }
}
