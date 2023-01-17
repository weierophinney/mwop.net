<?php

declare(strict_types=1);

namespace Mwop\Now;

use Mezzio\Application;
use Mwop\App\Middleware\CacheMiddleware;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->get('/now', [
            CacheMiddleware::class,
            Handler\PageHandler::class,
        ], 'now');

        $app->get('/then/{when:\d{4}-\d{2}}', [
            CacheMiddleware::class,
            Handler\PageHandler::class,
        ], 'now.then');

        return $app;
    }
}
