<?php

declare(strict_types=1);

namespace Mwop\Contact;

use Mezzio\Application;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $basePath = '/contact';
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->get($basePath . '[/]', [
            Handler\ContactPageHandler::class,
        ], 'contact');

        return $app;
    }
}
