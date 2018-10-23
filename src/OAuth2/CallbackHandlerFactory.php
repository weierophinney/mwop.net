<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class CallbackHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CallbackHandler
    {
        $config = $container->get('config');

        return new CallbackHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(ProviderFactory::class),
            $config['debug'] ?? false
        );
    }
}
