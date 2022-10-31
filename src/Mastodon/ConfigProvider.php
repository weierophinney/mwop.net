<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'laminas-cli'  => $this->getConsoleConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                Console\FetchMastodonFeed::class => Console\FetchMastodonFeedFactory::class,
                FetchMastodonFeed::class         => FetchMastodonFeedFactory::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'mastodon:fetch' => Console\FetchMastodonFeed::class,
            ]
        ];
    }
}
