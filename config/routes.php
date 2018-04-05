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
    $app->get('/', HomePage::class, 'home');
    $app->get('/comics', ComicsPage::class, 'comics');
    $app->get('/offline', OfflinePage::class, 'offline');
    $app->get('/resume', ResumePage::class, 'resume');

    // Blog
    $app->get('/blog[/]', Blog\ListPostsHandler::class, 'blog');
    $app->get('/blog/{id:[^/]+}.html', Blog\DisplayPostMiddleware::class, 'blog.post');
    $app->get('/blog/tag/{tag:php}.xml', Blog\FeedMiddleware::class, 'blog.feed.php');
    $app->get('/blog/{tag:php}.xml', Blog\FeedMiddleware::class, 'blog.feed.php.also');
    $app->get('/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml', Blog\FeedMiddleware::class, 'blog.tag.feed');
    $app->get('/blog/tag/{tag:[^/]+}', Blog\ListPostsHandler::class, 'blog.tag');
    $app->get('/blog/{type:atom|rss}.xml', Blog\FeedMiddleware::class, 'blog.feed');
    $app->get('/search[/]', Blog\SearchHandler::class, 'search');

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
        Contact\LandingPage::class,
    ], 'contact');
    $app->post('/contact/process', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        Contact\Process::class,
    ], 'contact.process');
    $app->get('/contact/thank-you', Contact\ThankYouPage::class, 'contact.thank-you');
};
