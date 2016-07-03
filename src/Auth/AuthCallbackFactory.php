<?php
namespace Mwop\Auth;

class AuthCallbackFactory
{
    public function __invoke($container)
    {
        $config = $container->get('config');
        $config = $config['opauth'];
        return new AuthCallback($config, $container->get('session'));
    }
}
