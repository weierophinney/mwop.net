<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Phly\RedisTaskQueue\Mapper\Mapper;

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
            'delegators' => [
                AttachableListenerProvider::class => [
                    FetchMastodonFeedDelegator::class,
                ],
                Mapper::class                     => [
                    PostMapperDelegator::class,
                ],
            ],
            'factories'  => [
                Console\FetchMastodonFeed::class => Console\FetchMastodonFeedFactory::class,
                Feed::class                      => FeedFactory::class,
                FetchMastodonFeed::class         => FetchMastodonFeedFactory::class,
                FetchMastodonFeedListener::class => FetchMastodonFeedListenerFactory::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'mastodon:fetch' => Console\FetchMastodonFeed::class,
            ],
        ];
    }
}
