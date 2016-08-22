<?php
namespace Mwop\Feed;

use Http\Client\Curl\Client;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;

class HttpPlugClientFactory
{
    public function __invoke() : HttpPlugClient
    {
        return new HttpPlugClient(
            new Client(
                new DiactorosMessageFactory(),
                new DiactorosStreamFactory()
            )
        );
    }
}
