<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;

class HttpClientFactory
{
    public function __invoke(): ClientInterface
    {
        return Psr18ClientDiscovery::find();
    }
}

