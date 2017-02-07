<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Zend\Expressive\Application;
use Zend\Expressive\Helper;
use Zend\Stratigility\Middleware\OriginalMessages;

error_reporting(E_ALL & ~E_USER_DEPRECATED);

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

$container = require 'config/container.php';
$app       = $container->get(Application::class);

/* Piped middleware */

$app->pipe(OriginalMessages::class);
$app->pipe(ErrorHandler::class);
$app->pipe(ContentSecurityPolicy::class);
$app->pipe(XClacksOverhead::class);
$app->pipe(XPoweredBy::class);
$app->pipe(Redirects::class);
$app->pipe('/auth', Auth\Middleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(NotFound::class);

/* Routed middleware */

// General pages
$app->get('/', HomePage::class, 'home');
$app->get('/comics', ComicsPage::class, 'comics');
$app->get('/offline', OfflinePage::class, 'offline');
$app->get('/resume', ResumePage::class, 'resume');

// Blog
$app->get('/blog[/]', Blog\ListPostsMiddleware::class, 'blog');
$app->get('/blog/{id:[^/]+}.html', Blog\DisplayPostMiddleware::class, 'blog.post');
$app->get('/blog/tag/{tag:php}.xml', Blog\FeedMiddleware::class, 'blog.feed.php');
$app->get('/blog/{tag:php}.xml', Blog\FeedMiddleware::class, 'blog.feed.php.also');
$app->get('/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml', Blog\FeedMiddleware::class, 'blog.tag.feed');
$app->get('/blog/tag/{tag:[^/]+}', Blog\ListPostsMiddleware::class, 'blog.tag');
$app->get('/blog/{type:atom|rss}.xml', Blog\FeedMiddleware::class, 'blog.feed');

// Contact form
$app->get('/contact[/]', Contact\LandingPage::class, 'contact');
$app->post('/contact/process', Contact\Process::class, 'contact.process');
$app->get('/contact/thank-you', Contact\ThankYouPage::class, 'contact.thank-your');

$app->run();
