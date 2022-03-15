<?php

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function array_key_exists;

class CacheMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): CacheMiddleware
    {
        $config = $container->get('config-cache');

        return new CacheMiddleware(
            cache: $container->get('Mwop\App\ResponseCachePool'),
            logger: $container->get(LoggerInterface::class),
            enabled: array_key_exists('enabled', $config) ? (bool) $config['enabled'] : true,
        );
    }
}
