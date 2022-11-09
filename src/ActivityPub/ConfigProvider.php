<?php

declare(strict_types=1);

namespace Mwop\ActivityPub;

use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories'  => [
                Handler\WebfingerHandler::class => Handler\WebfingerHandlerFactory::class,
            ],
            'delegators' => [
                Application::class => [
                    RouteProviderDelegator::class,
                ],
            ],
        ];
    }
}
