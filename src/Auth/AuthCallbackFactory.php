<?php
namespace Mwop\Auth;

use Interop\Container\ContainerInterface;

class AuthCallbackFactory
{
    public function __invoke(ContainerInterface $container) : AuthCallback
    {
        $config = $container->get('config');
        $config = $config['opauth'];
        return new AuthCallback($config, $container->get('session'));
    }
}
