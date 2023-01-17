<?php

declare(strict_types=1);

namespace Mwop\Blog;

use Mezzio\Application;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'blog'         => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplateConfig(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'api'    => [
                'key'          => '',
                'token_header' => 'X-MWOP-NET-BLOG-API-KEY',
            ],
            'db'     => null,
            'disqus' => [
                'developer' => 0,
                'key'       => null,
            ],
            'images' => [
                'openverse' => [
                    'client_id'     => '',
                    'client_secret' => '',
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'factories'  => [
                BlogCachePool::class                       => BlogCachePoolFactory::class,
                'config-blog'                              => ConfigFactory::class,
                'config-blog.api'                          => ConfigFactory::class,
                'config-blog.cache'                        => ConfigFactory::class,
                'config-blog.disqus'                       => ConfigFactory::class,
                Console\FeedGenerator::class               => Console\FeedGeneratorFactory::class,
                Console\TagCloud::class                    => Console\TagCloudFactory::class,
                Console\PostLatestToMastodon::class        => Console\PostLatestToMastodonFactory::class,
                Console\PostToMastodon::class              => Console\PostToMastodonFactory::class,
                Handler\DisplayPostHandler::class          => Handler\DisplayPostHandlerFactory::class,
                Handler\FeedHandler::class                 => Handler\FeedHandlerFactory::class,
                Handler\ListPostsHandler::class            => Handler\ListPostsHandlerFactory::class,
                Handler\SearchHandler::class               => Handler\SearchHandlerFactory::class,
                Handler\PostLatestToMastodonHandler::class => Handler\PostLatestToMastodonHandlerFactory::class,
                Handler\PostToMastodonHandler::class       => Handler\PostToMastodonHandlerFactory::class,
                Images\ApiClient::class                    => Images\ApiClientFactory::class,
                Images\Images::class                       => Images\ImagesFactory::class,
                Images\SearchCommand::class                => Images\SearchCommandFactory::class,
                Mapper\MapperInterface::class              => Mapper\MapperFactory::class,
                Mastodon\PostLatest::class                 => Mastodon\PostLatestFactory::class,
                Mastodon\PostLatestEventListener::class    => Mastodon\PostLatestEventListenerFactory::class,
                Mastodon\Post::class                       => Mastodon\PostFactory::class,
                Mastodon\PostEventListener::class          => Mastodon\PostEventListenerFactory::class,
                Middleware\ValidateAPIKeyMiddleware::class => Middleware\ValidateAPIKeyMiddlewareFactory::class,
            ],
            'invokables' => [
                Console\GenerateSearchData::class => Console\GenerateSearchData::class,
                Console\SeedBlogDatabase::class   => Console\SeedBlogDatabase::class,
            ],
            'delegators' => [
                Application::class                      => [
                    RoutesDelegator::class,
                ],
                AttachableListenerProvider::class       => [
                    Mastodon\PostLatestEventListenerDelegator::class,
                    Mastodon\PostEventListenerDelegator::class,
                ],
                Mastodon\PostLatestEventListener::class => [
                    DeferredServiceListenerDelegator::class,
                ],
                Mastodon\PostEventListener::class       => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'blog' => [__DIR__ . '/templates'],
            ],
        ];
    }
}
