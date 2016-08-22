<?php
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
