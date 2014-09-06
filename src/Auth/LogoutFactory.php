<?php
namespace Mwop\Auth;

class LogoutFactory
{
    public function __invoke($services)
    {
        return new Logout(
            $services->get('session')
        );
    }
}
