<?php
namespace Mwop\User;

class AuthCallbackFactory
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['opauth'];
        return new AuthCallback($config, $services->get('session'));
    }
}
