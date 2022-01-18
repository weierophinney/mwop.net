<?php

declare(strict_types=1);

namespace Mwop\Feed\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RssHandlerFactory
{
    public function __invoke(ContainerInterface $container): RssHandler
    {
        return new RssHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(EventDispatcherInterface::class),
        );
    }
}
