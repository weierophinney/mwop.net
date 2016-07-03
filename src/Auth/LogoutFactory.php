<?php
namespace Mwop\Auth;

class LogoutFactory
{
    public function __invoke($container)
    {
        return new Logout(
            $container->get('session')
        );
    }
}
