<?php

declare(strict_types=1);

namespace Mwop;

use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

// General pages
return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/comics', [
        SessionMiddleware::class,
        OAuth2\Middleware\CheckAuthenticationMiddleware::class,
        App\Handler\ComicsPageHandler::class,
    ], 'comics');
    $app->get('/resume', App\Handler\ResumePageHandler::class, 'resume');

    // OAuth2 routes
    (new OAuth2\ConfigProvider())->registerRoutes('/auth', $app);

    // Blog routes
    (new Blog\ConfigProvider())->registerRoutes('/blog', $app);

    // Register an app-level search route that maps to the blog search handler as well
    $app->get('/search[/]', Blog\Handler\SearchHandler::class, 'search');

    // Logout
    // Session middleware is already registered in the pipeline for all /auth routes
    $app->get('/logout', [
        SessionMiddleware::class,
        LogoutHandler::class
    ], 'logout');

    // Contact form
    $app->get('/contact[/]', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        Contact\Handler\DisplayContactFormHandler::class,
    ], 'contact');
    $app->post('/contact/process', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        Contact\Handler\ProcessContactFormHandler::class,
    ], 'contact.process');
    $app->get('/contact/thank-you', Contact\Handler\DisplayThankYouHandler::class, 'contact.thank-you');
};
