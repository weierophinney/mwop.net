<?php

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RedirectAmpPagesMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): RedirectAmpPagesMiddleware
    {
        return new RedirectAmpPagesMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
