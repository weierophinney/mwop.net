<?php

declare(strict_types=1);

namespace Mwop\Github;

use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $basePath = '';
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->post($basePath . '/api/hook/github', [
            ProblemDetailsMiddleware::class,
            ValidateWebhookRequestMiddleware::class,
            Handler\AtomHandler::class,
        ], 'api.hook.github');

        return $app;
    }
}
