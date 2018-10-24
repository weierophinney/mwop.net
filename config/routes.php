<?php

declare(strict_types=1);

namespace Mwop;

use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Flash\FlashMessageMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

// General pages
return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    $app->get('/', HomePage::class, 'home');
    $app->get('/comics', [
        SessionMiddleware::class,
        OAuth2\CheckAuthenticationMiddleware::class,
        ComicsPage::class,
    ], 'comics');
    $app->get('/resume', ResumePage::class, 'resume');

    // OAuth2 authentication response
    $app->get('/auth/{provider:debug|github|google}/oauth2callback', [
        SessionMiddleware::class,
        OAuth2\CallbackHandler::class,
    ]);

    // OAuth2 authentication request
    $app->get('/auth/{provider:debug|github|google}', [
        SessionMiddleware::class,
        OAuth2\RequestAuthenticationHandler::class,
    ]);

    // Blog
    $app->get('/blog[/]', Blog\ListPostsHandler::class, 'blog');
    $app->get('/blog/{id:[^/]+}.html', Blog\DisplayPostHandler::class, 'blog.post');
    $app->get('/blog/tag/{tag:php}.xml', Blog\FeedHandler::class, 'blog.feed.php');
    $app->get('/blog/{tag:php}.xml', Blog\FeedHandler::class, 'blog.feed.php.also');
    $app->get('/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml', Blog\FeedHandler::class, 'blog.tag.feed');
    $app->get('/blog/tag/{tag:[^/]+}', Blog\ListPostsHandler::class, 'blog.tag');
    $app->get('/blog/{type:atom|rss}.xml', Blog\FeedHandler::class, 'blog.feed');
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
