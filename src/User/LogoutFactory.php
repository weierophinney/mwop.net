<?php
namespace Mwop\User;

class LogoutFactory
{
    public function __invoke($services)
    {
        return new Logout(
            $services->get('session')
        );
    }
}
