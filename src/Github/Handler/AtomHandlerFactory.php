<?php

declare(strict_types=1);

namespace Mwop\Github\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class AtomHandlerFactory
{
    public function __invoke(ContainerInterface $container): AtomHandler
    {
        return new AtomHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(EventDispatcherInterface::class),
        );
    }
}
