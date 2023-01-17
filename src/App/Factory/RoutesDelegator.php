<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Session\SessionMiddleware;
use Mwop\App\Handler;
use Mwop\App\Middleware;
use Mwop\Blog\Handler\SearchHandler;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $app = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->get('/', Handler\HomePageHandler::class, 'home');
        $app->get('/resume', [
            Middleware\CacheMiddleware::class,
            Handler\ResumePageHandler::class,
        ], 'resume');
        $app->get('/privacy-policy', [
            Middleware\CacheMiddleware::class,
            Handler\PrivacyPolicyPageHandler::class,
        ], 'privacy-policy');
        $app->get('/api/ping', Handler\PingHandler::class, 'api.ping');

        // Admin
        $adminMiddleware = [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
        ];
        $app->get(
            '/admin',
            [...$adminMiddleware, Handler\AdminPageHandler::class],
            'admin'
        );
        $app->get(
            '/admin/clear-response-cache',
            [...$adminMiddleware, Handler\ClearResponseCacheHandler::class],
            'admin.clear-response-cache'
        );

        // Authentication
        $app->get('/login', [
            SessionMiddleware::class,
            Handler\LoginHandler::class,
        ], 'login');
        $app->post('/login', [
            SessionMiddleware::class,
            Handler\LoginHandler::class,
        ]);

        $app->get('/logout', [
            SessionMiddleware::class,
            Handler\LogoutHandler::class,
        ], 'logout');

        // Register an app-level search route that maps to the blog search handler
        $app->get('/search[/]', SearchHandler::class, 'search');

        return $app;
    }
}
