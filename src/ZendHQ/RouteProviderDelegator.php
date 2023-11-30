<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Psr\Container\ContainerInterface;

class RouteProviderDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $factory): Application
    {
        /** @var Application $app */
        $app = $factory();

        $app->post('/jq/worker', [
            ProblemDetailsMiddleware::class,
            Middleware\HostNameMiddleware::class,
            Middleware\ContentTypeMiddleware::class,
            Handler\WorkerHandler::class,
        ], 'jq.worker');

        return $app;
    }
}
