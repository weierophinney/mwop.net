<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class LogoutFactory
{
    public function __invoke(ContainerInterface $container) : Logout
    {
        return new Logout(
            $container->get('session')
        );
    }
}
