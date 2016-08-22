<?php
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
