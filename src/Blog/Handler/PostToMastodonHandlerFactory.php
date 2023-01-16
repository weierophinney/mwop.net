<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PostToMastodonHandlerFactory
{
    public function __invoke(ContainerInterface $container): PostToMastodonHandler
    {
        return new PostToMastodonHandler(
            $container->get(EventDispatcherInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
