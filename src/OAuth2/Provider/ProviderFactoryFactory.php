<?php

declare(strict_types=1);

namespace Mwop\OAuth2\Provider;

use Psr\Container\ContainerInterface;

class ProviderFactoryFactory
{
    public function __invoke(ContainerInterface $container): ProviderFactory
    {
        return new ProviderFactory($container);
    }
}
