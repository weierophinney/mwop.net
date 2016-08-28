---
id: 2016-05-16-programmatic-expressive
author: matthew
title: 'Programmatic Expressive'
draft: false
public: true
created: '2016-05-16T14:35:00-05:00'
updated: '2016-05-16T14:35:00-05:00'
tags:
    - expressive
    - php
    - psr-7
    - programming
    - 'zend framework'
---
[Enrico](http://www.zimuel.it) just returned from [phpDay](http://2016.phpday.it),
where he spoke about [Expressive](https://zendframework.github.io/zend-expressive)
and the upcoming Zend Framework 3. One piece of feedback he brought back had to
do with how people perceive they should be building Expressive applications:
many think, based on our examples, that it's completely configuration driven!

As it turns out, this is far from the truth; we developed our API to mimic that
of traditional microframeworks, and then built a configuration layer on top of
that to allow making substitutions. However, it's not only possible, but quite
fun, to mix and match the two ideas!

<!--- EXTENDED -->

As an experiment, I took my own website's source code, and made a couple of
tweaks:

- I imported the middleware pipeline from my
  `config/autoload/middleware-pipeline.global.php` file into programmatic
  declarations inside my `public/index.php`.
- I imported the routed middleware definitions from my
  `config/autoload/routes.global.php` file into programmatic declarations inside
  my `public/index.php`.

The bits and pieces to remember:

- Refer to your middleware using fully-qualified class names, just as you would
  in your configuration. This allows Expressive to pull them from the container,
  *which you are still configuring!*
- Order of operations is important when defining the pipeline and defining
  routes. The pipeline and routes can be defined separately, however, and I
  recommend doing so; that way you can look at the overall application pipeline
  separately from the routing definitions..

Here's what I ended up with.

First, my middleware pipeline configuration becomes only a list of dependencies,
to ensure services are wired correctly:

```php
// config/autoload/middleware-pipeline.php
use Mwop\Auth\Middleware as AuthMiddleware;
use Mwop\Auth\MiddlewareFactory as AuthMiddlewareFactory;
use Mwop\Factory\Unauthorized as UnauthorizedFactory;
use Mwop\Redirects;
use Mwop\Unauthorized;
use Mwop\XClacksOverhead;

return [
    'dependencies' => [
        'invokables' => [
            Redirects::class       => Redirects::class,
            XClacksOverhead::class => XClacksOverhead::class,
        ],
        'factories' => [
            AuthMiddleware::class => AuthMiddlewareFactory::class,
            Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
            Unauthorized::class => UnauthorizedFactory::class,
        ],
    ],
];
```

Similarly, the routing configuration is also now only service configuration:

```php
// config/autoload/routes.global.php
use Mwop\Blog;
use Mwop\ComicsPage;
use Mwop\Contact;
use Mwop\Factory;
use Mwop\HomePage;
use Mwop\Job;
use Mwop\ResumePage;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    'dependencies' => [
        'delegators' => [
            Blog\DisplayPostMiddleware::class => [
                Blog\CachingDelegatorFactory::class,
            ],
        ],
        'invokables' => [
            Blog\FeedMiddleware::class           => Blog\FeedMiddleware::class,
            Blog\Console\SeedBlogDatabase::class => Blog\Console\SeedBlogDatabase::class,
            BodyParamsMiddleware::class          => BodyParamsMiddleware::class,
            RouterInterface::class               => FastRouteRouter::class,
        ],
        'factories' => [
            Blog\DisplayPostMiddleware::class => Blog\DisplayPostMiddlewareFactory::class,
            Blog\ListPostsMiddleware::class   => Blog\ListPostsMiddlewareFactory::class,
            Contact\LandingPage::class        => Contact\LandingPageFactory::class,
            Contact\Process::class            => Contact\ProcessFactory::class,
            Contact\ThankYouPage::class       => Contact\ThankYouPageFactory::class,
            ComicsPage::class                 => Factory\ComicsPage::class,
            HomePage::class                   => Factory\PageFactory::class,
            Job\GithubFeed::class             => Job\GithubFeedFactory::class,
            ResumePage::class                 => Factory\PageFactory::class,
            'Mwop\OfflinePage'                => Factory\PageFactory::class,
        ],
    ],
];
```

Finally, let's look at the `public/index.php`. As noted earlier, Expressive
defines a similar API to other microframeworks. This means that you can call
things like `$app->get()`, `$app->post()`, etc. with a route, the middleware to
execute, and, in the case of Expressive, the route name (which is used for URI
generation within the application). Here's what it looks like when done:

```php
// public/index.php
namespace Mwop;

use Zend\Expressive\Application;
use Zend\Expressive\Helper;

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

// Piped middleware
$app->pipe(XClacksOverhead::class);
$app->pipe(Redirects::class);
$app->pipe('/auth', Auth\Middleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(Unauthorized::class);

// Routed middleware

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

// Zend Server jobs
$app->post('/jobs/clear-cache', Job\ClearCache::class, 'job.clear-cache');
$app->post('/jobs/comics', Job\Comics::class, 'job.comics');
$app->post('/jobs/github-feed', Job\GithubFeed::class, 'job.github-feed');

$app->run();
```

This approach provides a nice middleground between defining the middleware
inline:

```php
$app->get('/', function ($request, $response, $next) {
    // ... 
}, 'home');
```

and the straight configuration approach:

```php
    'routes' => [
        [
            'path'            => '/',
            'middleware'      => HomePage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'home',
        ],

        / * ... */
```

It loses, however, some flexibility: with the configuration-driven approach, we
can easily define some routes or pipeline middleware that only execute in
development, and ensure the order in which they occur &mdash; something not easy
to do with the programmatic approach.

The main point in this exercise, however, is to demonstrate that Expressive
allows you to *choose your own approach*, which is the guiding principle behind
the project.
