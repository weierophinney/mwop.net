<?php

declare(strict_types=1);

namespace Mwop;

use Psr\Container\ContainerInterface;

class SetHostNameMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : SetHostNameMiddleware
    {
        return new SetHostNameMiddleware();
    }
}
