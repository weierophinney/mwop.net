<?php

declare(strict_types=1);

namespace Mwop\Hooks\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class GitHubAtomHandlerFactory
{
    public function __invoke(ContainerInterface $container): GitHubAtomHandler
    {
        return new GitHubAtomHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(EventDispatcherInterface::class),
        );
    }
}
