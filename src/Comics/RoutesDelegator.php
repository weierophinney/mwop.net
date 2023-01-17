<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->get('/comics', [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            Handler\ComicsPageHandler::class,
        ], 'comics');

        return $app;
    }
}
