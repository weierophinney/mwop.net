<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Handler;

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Mwop\ActivityPub\Webfinger\AccountMap;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class WebfingerHandlerFactory
{
    public function __invoke(ContainerInterface $container): WebfingerHandler
    {
        return new WebfingerHandler(
            accountMap: new AccountMap(),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            problemFactory: $container->get(ProblemDetailsResponseFactory::class),
        );
    }
}
