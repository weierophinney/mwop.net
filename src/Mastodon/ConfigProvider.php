<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'laminas-cli'  => $this->getConsoleConfig(),
            'mastodon'     => $this->getConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'delegators' => [
                AttachableListenerProvider::class => [
                    FetchMastodonFeedDelegator::class,
                ],
                FetchMastodonFeedListener::class  => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                ApiClient::class                 => PsrApiClientFactory::class,
                Console\FetchMastodonFeed::class => Console\FetchMastodonFeedFactory::class,
                Credentials::class               => CredentialsFactory::class,
                Feed::class                      => FeedFactory::class,
                FetchMastodonFeed::class         => FetchMastodonFeedFactory::class,
                FetchMastodonFeedListener::class => FetchMastodonFeedListenerFactory::class,
                MediaFactory::class              => MediaFactoryFactory::class,
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

    public function getConfig(): array
    {
        return [
            'domain'       => '',
            'access_token' => '',
        ];
    }
}
