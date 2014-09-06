<?php
namespace Mwop\Auth;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get(__NAMESPACE__ . '\Auth'),
            $services->get(__NAMESPACE__ . '\AuthCallback'),
            $services->get(__NAMESPACE__ . '\Logout')
        );
    }
}
