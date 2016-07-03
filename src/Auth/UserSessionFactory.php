<?php
namespace Mwop\Auth;

class UserSessionFactory
{
    public function __invoke($container)
    {
        return new UserSession($container->get('session'));
    }
}
