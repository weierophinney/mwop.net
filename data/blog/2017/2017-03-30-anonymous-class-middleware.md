---
id: 2017-03-30-anonymous-class-middleware
author: matthew
title: 'Using Anonymous Classes to Write Middleware'
draft: false
public: true
created: '2017-03-30T13:20:00-05:00'
updated: '2017-03-30T13:20:00-05:00'
tags:
    - php
    - programming
    - psr-7
    - psr-15
---

I faced an interesting question recently with regards to middleware: What
happens when we go from a convention-based to a contract-based approach when
programming?

Convention-based approaches usually allow for
[duck-typing](https://en.wikipedia.org/wiki/Duck_typing); with middleware, it
means you can write [PHP callables](http://php.net/language.types.callable)
&mdash; usually [closures](http://php.net/closure) &mdash; and just expect them
to work.

Contract-based approaches use _interfaces_. I think you can see where this is
going.

<!--- EXTENDED -->

## PSR-7 Middleware

When [PSR-7](http://www.php-fig.org/psr/psr-7/) was introduced, a number of
middleware microframeworks adopted a common signature for middleware:

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next
) : ResponseInterface
```

where `$next` had the following signature:

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function (
    ServerRequestInterface $request,
    ResponseInterface $response
) : ResponseInterface
```

This approach meant that you could wire middleware using closures, which makes
for a nice, succinct, programmatic interface:

```php
// Examples are using zend-stratigility
use Zend\Diactoros\Response\TextResponse;
use Zend\Stratigility\MiddlewarePipe;

$pipeline = new MiddlewarePipe();

$pipeline->pipe(function ($request, $response, callable $next) {
    $response = $next($request, $response);
    return $response->withHeader('X-ClacksOverhead', 'GNU Terry Pratchett');
});

$pipeline->pipe(function ($request, $response, callable $next) {
    return new TextResponse('Hello world!');
});
```

Easy-peasey!

This convention-based approach was easy to write for, because there was no need
to create discrete classes. You _could_, but it wasn't strictly necessary. Just
throw any PHP callable at it, and profit.

(I'll note that some libraries, such as
[Stratigility](https://docs.zendframework.com/zend-stratigility), codified at
least the middleware via an interface as well, though implementation of the
interface was strictly optional.)

The **big** problem, however, is that it can lead to subtle errors:

- what happens if you expect _more_ arguments than the middleware dispatcher
  provides?
- what happens if you expect _different_ arguments and/or argument types than
  the middleware dispatcher provides?
- what happens if your middleware returns something unexpected?

Essentially, a convention-based approach has no [type
safety](https://en.wikipedia.org/wiki/Type_safety), which can lead to a lot of
subtle, unexpected, runtime errors.

## PSR-15 Middleware

The proposed [PSR-15 (HTTP Server Middleware)](https://github.com/php-fig/fig-standards/tree/10bb43b1802c0427f8a4a5d1e6a84da83fa7724d/proposed/http-middleware)
is _not_ convention-based, and instead proposes two _interfaces_:

```php
namespace Interop\Http\ServerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * Docblock annotations, because PHP 5.6 compatibility
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate);
}

interface DelegateInterface
{
    /**
     * Docblock annotations, because PHP 5.6 compatibility
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request);
}
```

This leads to type safety: if you typehint on these interfaces (and, typically,
for middleware dispatchers, you're only concerned with the
`MiddlewareInterface`), you know that PHP will have your back with regards to
invalid middleware.

However, this also means that for any given middleware, _you **must** create a
class_!

Well, that makes things more difficult, doesn't it!

Or does it?

## Anonymous classes

Starting in PHP 7, we now have the ability to declare [anonymous
classes](http://php.net/language.oop5.anonymous). These are similar to closures,
which can be thought of as _anonymous functions_ (though with quite a lot more
semantics and functionality!), applied at the class level.

Interestingly, anonymous classes in PHP allow for:

- Extension
- Interface implementation
- Trait composition

In other words, they behave just like any standard class declaration.

Let's adapt our previous pipeline to use PSR-15 instead. (We'll continue using
Stratigility, as, since version 2, it supports the proposed PSR-15 specification.)

```php
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\TextResponse;
use Zend\Stratigility\MiddlewarePipe;

$pipeline = new MiddlewarePipe();

$pipeline->pipe(new class implements MiddlewareInterface {
    public function process (ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);
        return $response->withHeader('X-ClacksOverhead', 'GNU Terry Pratchett');
    }
});

$pipeline->pipe(new class implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return new TextResponse('Hello world!');
    }
});
```

While there's slightly more verbiage &mdash; what were essentially our anonymous
functions previously are now wrapped in a class, adding a couple lines to each
&mdash; the result is not terribly onerous, and gives us important
_type-safety_. Our middleware runner no longer has to _assume_ that any
middleware piped to it is correctly defined, but can instead _know_, as it can
enforce a typehint.

The approach is also useful to IDEs, which can now properly typehint arguments,
and let us know when the contract is being violated.

## What about closures?

A _closure_ in PHP allows you to _close over_ or _bind_ variables in the current
scope to the anonymous function. As an example, if I want to create logging
middleware, I might do the following:

```php
// Where $log is a PSR-3 logger:
use Zend\Diactoros\Response\EmptyResponse;

$pipeline->pipe(function ($request, $response, callable $next) use ($log) {
    try {
        $response = $next($request, $response);
        return $response;
    } catch (Throwable $e) {
    }

    $log->error(sprintf(
        '[%d] (%s) %s',
        $e->getCode(),
        get_class($e),
        $e->getMessage()
    ), ['exception' => $e]);

    return new EmptyResponse(500);
});
```

How would I accomplish this with an anonymous class?

Anonymous classes let you pass arguments during declaration that are then passed
to the constructor. As such, you _bind_ variables from the current scope into
the class typically as _class properties_:

```php
// Where $log is a PSR-3 logger:
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\EmptyResponse;

$pipeline->pipe(new class($log) implements MiddlewareInterface {
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        try {
            $response = $delegate->process($request);
            return $response;
        } catch (Throwable $e) {
        }

        $this->log->error(sprintf(
            '[%d] (%s) %s',
            $e->getCode(),
            get_class($e),
            $e->getMessage()
        ), ['exception' => $e]);

        return new EmptyResponse(500);
    }
});
```

This approach gives you added type-safety: if `$log` is of a different type,
you'll know when that middleware is created, as PHP will raise a fatal error.

Another thing I like about this approach is it allows me to prototype classes
before I write them formally. I can start seeing what the re-use possibilities
are, what arguments I might need, and more. Because the syntax for anonymous
classes is identical to declared classes, I can later extract it to a named
class by simply cutting the definition and pasting it into a file of its own.

So, don't let the PSR-15 interfaces stop you! Start using anonymous classes for
your own middleware prototypes!
