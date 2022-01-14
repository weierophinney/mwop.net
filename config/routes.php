<?php

declare(strict_types=1);

namespace Mwop;

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

// General pages
return function (
    Application $app,
    MiddlewareFactory $factory,
    ContainerInterface $container
): void {
    // App routes
    (new App\ConfigProvider())->registerRoutes($app);

    // Register an app-level search route that maps to the blog search handler
    $app->get('/search[/]', Blog\Handler\SearchHandler::class, 'search');

    // Blog routes
    (new Blog\ConfigProvider())->registerRoutes($app, '/blog');

    // Contact form
    (new Contact\ConfigProvider())->registerRoutes($app, '/contact');

    // Github (webhooks)
    (new Github\ConfigProvider())->registerRoutes($app);
};
