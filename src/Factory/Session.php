<?php
namespace Mwop\Factory;

use Aura\Session\SessionFactory;

class Session
{
    public function __invoke()
    {
        $factory = new SessionFactory;
        return $factory->newInstance($_COOKIE);
    }
}
