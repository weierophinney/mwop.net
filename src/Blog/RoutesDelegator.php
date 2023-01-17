<?php

declare(strict_types=1);

namespace Mwop\Blog;

use Mezzio\Application;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mwop\App\Middleware\CacheMiddleware;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $basePath = '/blog';
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

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

        $app->post($basePath . '/api/mastodon/latest', [
            ProblemDetailsMiddleware::class,
            Middleware\ValidateAPIKeyMiddleware::class,
            Handler\PostLatestToMastodonHandler::class,
        ], 'blog.mastodon.latest');
        $app->post($basePath . '/api/mastodon/post', [
            ProblemDetailsMiddleware::class,
            Middleware\ValidateAPIKeyMiddleware::class,
            BodyParamsMiddleware::class,
            Handler\PostToMastodonHandler::class,
        ], 'blog.mastodon.post');

        return $app;
    }
}
