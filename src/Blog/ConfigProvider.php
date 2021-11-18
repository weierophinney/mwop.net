<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mwop\Blog\Handler\TweetLatestHandler;
use Mwop\Blog\Middleware\ValidateAPIKeyMiddleware;
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
            'api'     => [
                'key' => '',
            ],
            'db'      => null,
            'cache'   => [
                'enabled' => false,
            ],
            'disqus'  => [
                'developer' => 0,
                'key'       => null,
            ],
            'twitter' => [
                'consumer_key'        => '',
                'consumer_secret'     => '',
                'access_token'        => '',
                'access_token_secret' => '',
                'logo_path'           => realpath(getcwd()) . '/public/images/favicon/android-chrome-144x144.png',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            // @codingStandardsIgnoreStart
            // phpcs:disable
            'factories' => [
                BlogCachePool::class                            => BlogCachePoolFactory::class,
                'config-blog'                                   => ConfigFactory::class,
                'config-blog.api'                               => ConfigFactory::class,
                'config-blog.cache'                             => ConfigFactory::class,
                'config-blog.disqus'                            => ConfigFactory::class,
                'config-blog.twitter'                           => ConfigFactory::class,
                Console\ClearCache::class                       => Console\ClearCacheFactory::class,
                Console\FeedGenerator::class                    => Console\FeedGeneratorFactory::class,
                Console\TagCloud::class                         => Console\TagCloudFactory::class,
                Console\TweetLatest::class                      => InvokableFactory::class,
                Handler\DisplayPostHandler::class               => Handler\DisplayPostHandlerFactory::class,
                Handler\FeedHandler::class                      => Handler\FeedHandlerFactory::class,
                Handler\ListPostsHandler::class                 => Handler\ListPostsHandlerFactory::class,
                Handler\SearchHandler::class                    => Handler\SearchHandlerFactory::class,
                Handler\TweetLatestHandler::class               => Handler\TweetLatestHandlerFactory::class,
                Listener\CacheBlogPostListener::class           => Listener\CacheBlogPostListenerFactory::class,
                Listener\FetchBlogPostFromCacheListener::class  => Listener\FetchBlogPostFromCacheListenerFactory::class,
                Listener\FetchBlogPostFromMapperListener::class => Listener\FetchBlogPostFromMapperListenerFactory::class,
                Mapper\MapperInterface::class                   => Mapper\MapperFactory::class,
                Middleware\ValidateAPIKeyMiddleware::class      => Middleware\ValidateAPIKeyMiddlewareFactory::class,
                Twitter\TweetLatest::class                      => Twitter\TweetLatestFactory::class,
                Twitter\TweetLatestEventListener::class         => Twitter\TweetLatestEventListenerFactory::class,
                Twitter\TwitterFactory::class                   => Twitter\TwitterFactoryFactory::class,
            ],
            // phpcs:enable
            // @codingStandardsIgnoreEnd
            'invokables' => [
                Console\GenerateSearchData::class => Console\GenerateSearchData::class,
                Console\SeedBlogDatabase::class   => Console\SeedBlogDatabase::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    Listener\FetchBlogPostEventListenersDelegator::class,
                    Twitter\TweetLatestEventListenerDelegator::class,
                ],
            ],
        ];
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
        $app->get($basePath . '/{id:[^/]+}.html', Handler\DisplayPostHandler::class, 'blog.post');
        $app->get($basePath . '/tag/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php');
        $app->get($basePath . '/{tag:php}.xml', Handler\FeedHandler::class, 'blog.feed.php.also');
        $app->get($basePath . '/tag/{tag:[^/]+}/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.tag.feed');
        $app->get($basePath . '/tag/{tag:[^/]+}', Handler\ListPostsHandler::class, 'blog.tag');
        $app->get($basePath . '/{type:atom|rss}.xml', Handler\FeedHandler::class, 'blog.feed');
        $app->get($basePath . '/search[/]', Handler\SearchHandler::class, 'blog.search');

        $app->post($basePath . '/api/tweet/latest', [
            ProblemDetailsMiddleware::class,
            ValidateAPIKeyMiddleware::class,
            TweetLatestHandler::class,
        ], 'blog.tweet.latest');
    }
}
