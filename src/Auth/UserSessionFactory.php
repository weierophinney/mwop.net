<?php
namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class UserSessionFactory
{
    public function __invoke(ContainerInterface $container) : UserSession
    {
        return new UserSession($container->get('session'));
    }
}
