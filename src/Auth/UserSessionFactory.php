<?php
namespace Mwop\Auth;

class UserSessionFactory
{
    public function __invoke($services)
    {
        return new UserSession($services->get('session'));
    }
}
