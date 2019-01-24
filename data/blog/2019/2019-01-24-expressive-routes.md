---
id: 2019-01-24-expressive-routes
author: matthew
title: 'Registering Module-Specific Routes in Expressive'
draft: false
public: true
created: '2019-01-24T11:30:00-06:00'
updated: '2019-01-24T11:30:00-06:00'
tags:
    - php
    - programming
    - expressive
    - psr-11
---

In [Expressive](https://getexpressive.org), we have standardized on a file named
`config/routes.php` to contain all your route registrations. A typical file
might look something like this:

```php
declare(strict_types=1);

use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    $app->get('/', App\HomePageHandler::class, 'home');

    $app->get('/contact', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        App\Contact\ContactPageHandler::class
    ], 'contact');
    $app->post('/contact', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        App\Contact\ProcessContactRequestHandler::class
    ]);
    $app->get(
        '/contact/thank-you',
        App\Contact\ThankYouHandler::class,
        'contact.done'
    );

    $app->get(
        '/blog[/]',
        App\Blog\Handler\LandingPageHandler::class,
        'blog'
    );
    $app->get('/blog/{id:[^/]+\.html', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        App\Blog\Handler\BlogPostHandler::class,
    ], 'blog.post');
    $app->post('/blog/comment/{id:[^/]+\.html', [
        SessionMiddleware::class,
        CsrfMiddleware::class,
        App\Blog\Handler\ProcessBlogCommentHandler::class,
    ], 'blog.comment');
}
```

and so on.

These files can get _really_ long, and organizing them becomes imperative.

<!--- EXTENDED -->

## Using Delegator Factories

One way we have recommended to make these files simpler is to use [delegator
factories](https://docs.zendframework.com/zend-expressive/v3/features/container/delegator-factories/)
registered with the `Zend\Expressive\Application` class to add routes. That
looks something like this:

```php
namespace App\Blog;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

class RoutesDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) : Application {
        /** @var Application $app */
        $app = $callback();

        $app->get(
            '/blog[/]',
            App\Blog\Handler\LandingPageHandler::class,
            'blog'
        );
        $app->get('/blog/{id:[^/]+\.html', [
            SessionMiddleware::class,
            CsrfMiddleware::class,
            Handler\BlogPostHandler::class,
        ], 'blog.post');
        $app->post('/blog/comment/{id:[^/]+\.html', [
            SessionMiddleware::class,
            CsrfMiddleware::class,
            Handler\ProcessBlogCommentHandler::class,
        ], 'blog.comment');

        return $app;
    }
}
```

You would then register this as a delegator factory somewhere in your
configuration:

```php
use App\Blog\RoutesDelegator;
use Zend\Expressive\Application;

return [
    'dependencies' => [
        'delegators' => [
            Application::class => [
                RoutesDelegator::class,
            ],
        ],
    ],
];
```

Delegator factories run after the service has been created for the first time,
but before it has been returned by the container. They allow you to interact
with the service before it's returned; you can configure it futher, add
listeners, use it to configure other services, or even use them to replace the
instance with an alternative. In this example, we're opting to _configure_ the
`Application` class further by registering routes with it.

[We've even written this approach up in our documentation.](https://docs.zendframework.com/zend-expressive/v3/cookbook/autowiring-routes-and-pipelines/)

So far, so good. But it means discovering where routes are registered becomes
more difficult. You now have to look in each of:

- `config/routes.php`
- Each file in `config/autoload/`:
  - looking for delegators attached to the `Application` class,
  - and then checking those to see if they register routes.
- In `config/config.php` to identify `ConfigProvider` classes, and then:
  - looking for delegators attached to the `Application` class,
  - and then checking those to see if they register routes.

The larger your application gets, the more work this becomes. Your
`config/routes.php` becomes way more readable, but it becomes far harder to find
all your routes.

## One-off Functions

In examining this problem for the upteenth time this week, I stumbled upon a
solution that is initially acceptable to me, finally.

What I've done is as follows:

- I've created a function in my `ConfigProvider` that accepts the `Application`
  instance and any other arguments I want to pass to it, and which registers
  routes with the instance.
- I call that function within my `config/routes.php`.

Building on the example above, the `ConfigProvider` for the `App\Blog` module
now has the following method:

```php
namespace App\Blog;

use Zend\Expressive\Application;
use Zend\Expressive\Csrf\CsrfMiddleware;
use Zend\Expressive\Session\SessionMiddleware;

class ConfigProvider
{
    public function __invoke() : array
    {
        /* ... */
    }

    public function registerRoutes(
        Application $app,
        string $basePath = '/blog'
    ) : void {
        $app->get(
            $basePath . '[/]',
            App\Blog\Handler\LandingPageHandler::class,
            'blog'
        );
        $app->get($basePath . '/{id:[^/]+\.html', [
            SessionMiddleware::class,
            CsrfMiddleware::class,
            Handler\BlogPostHandler::class,
        ], 'blog.post');
        $app->post($basePath . '/comment/{id:[^/]+\.html', [
            SessionMiddleware::class,
            CsrfMiddleware::class,
            Handler\ProcessBlogCommentHandler::class,
        ], 'blog.comment');
    }
}
```

Within my `config/routes.php`, I can create a temporary instance and call the
method:

```php
declare(strict_types=1);

return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    (new \App\Blog\ConfigProvider())->registerRoutes($app);
}
```

This approach eliminates the problems of using delegator factories:

- There's a clear indication that a given class method registers routes.
- I can then look directly at that method to determine what they are.

One thing I like about this approach is that it allows me to keep the routes
close to the code that handles them (i.e., within each module), while still
giving me control over their registration at the application level.

What strategies have you tried?
