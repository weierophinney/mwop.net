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
            'factories'  => [
                ClearResponseCache::class => ClearResponseCacheFactory::class,
                FetchMastodonFeed::class  => FetchMastodonFeedFactory::class,
            ],
            'invokables' => [
                ClearStaticCache::class => ClearStaticCache::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'cache:clear-static'   => ClearStaticCache::class,
                'cache:clear-response' => ClearResposneCache::class,
                'mastodon:fetch'       => FetchMastodonFeed::class,
            ],
        ];
    }
}
