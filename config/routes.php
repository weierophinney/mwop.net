<?php

declare(strict_types=1);

namespace Mwop;

// General pages
return function (
    \Mezzio\Application $app,
    \Mezzio\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    // App routes
    (new App\ConfigProvider())->registerRoutes($app);

    // Register an app-level search route that maps to the blog search handler
    $app->get('/search[/]', Blog\Handler\SearchHandler::class, 'search');

    // Blog routes
    (new Blog\ConfigProvider())->registerRoutes($app, '/blog');

    // Contact form
    (new Contact\ConfigProvider())->registerRoutes($app, '/contact');

    // OAuth2 routes
    (new OAuth2\ConfigProvider())->registerRoutes($app, '/auth');
};
