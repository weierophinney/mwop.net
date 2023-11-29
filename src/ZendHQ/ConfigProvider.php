<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

use Aws\Middleware;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RouteProviderDelegator::class,
                ],
            ],
            'factories' => [
                Handler\WorkerHandler::class            => Handler\WorkerHandlerFactory::class,
                Middleware\ContentTypeMiddleware::class => InvokableFactory::class,
                Middleware\HostNameMiddleware::class    => InvokableFactory::class,
            ],
        ];
    }
}
