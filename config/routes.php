<?php

declare(strict_types=1);

namespace Mwop;

// General pages
return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    // App routes
    (new App\ConfigProvider())->registerRoutes($app);

    // Register an app-level search route that maps to the blog search handler
    $app->get('/search[/]', Blog\Handler\SearchHandler::class, 'search');

    // OAuth2 routes
    (new OAuth2\ConfigProvider())->registerRoutes('/auth', $app);

    // Blog routes
    (new Blog\ConfigProvider())->registerRoutes('/blog', $app);

    // Contact form
    (new Contact\ConfigProvider())->registerRoutes('/contact', $app);
};
