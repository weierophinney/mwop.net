<?php

namespace Mwop\Discourse;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback) : Application
    {
        $app = $callback();
        $app->post(
            '/discourse/testroom',
            LoggerAction::class,
            'discourse'
        );
        return $app;
    }
}
