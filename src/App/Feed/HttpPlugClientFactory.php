<?php

declare(strict_types=1);

namespace Mwop\App\Feed;

use Http\Client\Curl\Client;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class HttpPlugClientFactory
{
    public function __invoke(ContainerInterface $container): HttpPlugClient
    {
        return new HttpPlugClient(
            new Client(
                new DiactorosMessageFactory(),
                new DiactorosStreamFactory()
            ),
            $container->get(RequestFactoryInterface::class)
        );
    }
}
