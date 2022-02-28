<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

class NewImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): NewImageHandler
    {
        return new NewImageHandler(
            responseFactory: $container->get(ResponseFactoryInterface::class),
            dispatcher: $container->get(EventDispatcherInterface::class),
            logger: $container->get(LoggerInterface::class),
        );
    }
}
