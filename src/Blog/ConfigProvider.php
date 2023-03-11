<?php

declare(strict_types=1);

namespace Mwop\Blog;

use Mezzio\Application;
use Mwop\App\Middleware\CacheMiddleware;
use Phly\ConfigFactory\ConfigFactory;

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
            'api'     => [
                'key'          => '',
                'token_header' => 'X-MWOP-NET-BLOG-API-KEY',
            ],
            'db'      => null,
            'disqus'  => [
                'developer' => 0,
                'key'       => null,
            ],
            'images'  => [
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
                Handler\DisplayPostHandler::class          => Handler\DisplayPostHandlerFactory::class,
                Handler\FeedHandler::class                 => Handler\FeedHandlerFactory::class,
                Handler\ListPostsHandler::class            => Handler\ListPostsHandlerFactory::class,
                Handler\SearchHandler::class               => Handler\SearchHandlerFactory::class,
                Images\ApiClient::class                    => Images\ApiClientFactory::class,
                Images\Images::class                       => Images\ImagesFactory::class,
                Images\SearchCommand::class                => Images\SearchCommandFactory::class,
                Mapper\MapperInterface::class              => Mapper\MapperFactory::class,
                Middleware\ValidateAPIKeyMiddleware::class => Middleware\ValidateAPIKeyMiddlewareFactory::class,
            ],
            'invokables' => [
                Console\GenerateSearchData::class => Console\GenerateSearchData::class,
                Console\SeedBlogDatabase::class   => Console\SeedBlogDatabase::class,
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

    public function registerRoutes(Application $app, string $basePath = '/blog'): void
    {
        $app->get($basePath . '[/]', Handler\ListPostsHandler::class, 'blog');
        $app->get("{$basePath}/{id:[^/]+}.html", [
            CacheMiddleware::class,
            Handler\DisplayPostHandler::class,
        ], 'blog.post');
        $app->get($basePath . '/tag/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php');
        $app->get($basePath . '/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php.also');
        $app->get($basePath . '/tag/{tag:[^/]+}/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.tag.feed');
        $app->get($basePath . '/tag/{tag:[^/]+}', Handler\ListPostsHandler::class, 'blog.tag');
        $app->get($basePath . '/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.feed');
        $app->get($basePath . '/search[/]', Handler\SearchHandler::class, 'blog.search');
    }
}
