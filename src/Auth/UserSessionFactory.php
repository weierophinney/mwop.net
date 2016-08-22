<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class UserSessionFactory
{
    public function __invoke(ContainerInterface $container) : UserSession
    {
        return new UserSession($container->get('session'));
    }
}
