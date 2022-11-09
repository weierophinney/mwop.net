<?php

declare(strict_types=1);

namespace Mwop\ActivityPub;

use Mezzio\Application;
use Psr\Container\ContainerInterface;

class RouteProviderDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        /** @var Application $app */
        $app = $factory();

        $app->get('/.well-known/webfinger', Handler\WebfingerHandler::class, 'activity-pub.webfinger');

        return $app;
    }
}
