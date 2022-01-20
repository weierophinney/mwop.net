<?php

declare(strict_types=1);

namespace Mwop\Cron;

use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'cron'         => $this->getCronConfig(),
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getCronConfig(): array
    {
        return [
            'jobs' => [],
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'delegators' => [
                AttachableListenerProvider::class => [
                    CronDelegator::class,
                ],
            ],
            'factories'  => [
                'config-cron' => ConfigFactory::class,
            ],
        ];
    }
}
