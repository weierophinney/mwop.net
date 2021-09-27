<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PingHandlerFactory
{
    public function __invoke(ContainerInterface $container): PingHandler
    {
        return new PingHandler(
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
