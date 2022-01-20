<?php

declare(strict_types=1);

namespace Mwop\Console;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [
                ClearCache::class => ClearCache::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'clear-cache' => ClearCache::class,
            ],
        ];
    }
}
