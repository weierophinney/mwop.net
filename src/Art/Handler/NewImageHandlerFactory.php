<?php

declare(strict_types=1);

namespace Mwop\Art\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class NewImageHandlerFactory
{
    public function __invoke(ContainerInterface $container): NewImageHandler
    {
        return new NewImageHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(EventDispatcherInterface::class),
        );
    }
}
