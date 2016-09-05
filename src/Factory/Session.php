<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Aura\Session\Session as AuraSession;
use Aura\Session\SessionFactory;
use Interop\Container\ContainerInterface;

class Session
{
    public function __invoke() : AuraSession
    {
        $factory = new SessionFactory;
        return $factory->newInstance($_COOKIE);
    }
}
