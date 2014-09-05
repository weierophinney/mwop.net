<?php
namespace Mwop\Factory;

use Aura\Session\SessionFactory;

class Session
{
    public function __invoke($services)
    {
        $factory = new SessionFactory;
        return $factory->newInstance($_COOKIE);
    }
}
