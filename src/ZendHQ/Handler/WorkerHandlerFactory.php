<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class WorkerHandlerFactory
{
    public function __invoke(ContainerInterface $container) : WorkerHandler
    {
        return new WorkerHandler(
            dispatcher: $container->get(EventDispatcherInterface::class),
            responseFactory: $container->get(ResponseFactoryInterface::class),
        );
    }
}
