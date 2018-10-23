<?php

namespace Mwop\OAuth2;

use Psr\Container\ContainerInterface;

class ProviderFactoryFactory
{
    public function __invoke(ContainerInterface $container) : ProviderFactory
    {
        return new ProviderFactory($container);
    }
}
