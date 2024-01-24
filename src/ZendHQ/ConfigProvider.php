<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'zendhq:jq:setup-recurring-jobs' => Console\SetupRecurringJobs::class,
            ],
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
            'factories'  => [
                Console\SetupRecurringJobs::class       => Console\SetupRecurringJobsFactory::class,
                Handler\WorkerHandler::class            => Handler\WorkerHandlerFactory::class,
                Middleware\ContentTypeMiddleware::class => InvokableFactory::class,
                Middleware\HostNameMiddleware::class    => InvokableFactory::class,
            ],
        ];
    }
}
