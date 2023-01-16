<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PostLatestToMastodonHandlerFactory
{
    public function __invoke(ContainerInterface $container): PostLatestToMastodonHandler
    {
        return new PostLatestToMastodonHandler(
            $container->get(EventDispatcherInterface::class),
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
