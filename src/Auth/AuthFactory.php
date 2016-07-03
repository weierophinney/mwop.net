<?php
namespace Mwop\Auth;

class AuthFactory
{
    public function __invoke($container)
    {
        $config = $container->get('config');
        $config = $config['opauth'];
        return new Auth($config, $container->get('session'));
    }
}
