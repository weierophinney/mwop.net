---
id: 2015-01-08-on-http-middleware-and-psr-7
author: matthew
title: 'On HTTP, Middleware, and PSR-7'
draft: false
public: true
created: '2015-01-08T17:15:00-06:00'
updated: '2015-01-09T15:55:00-06:00'
tags:
    - http
    - middleware
    - php
    - programming
    - psr-7
---
As I've surveyed the successes and failures of ZF1 and ZF2, I've started
considering how we can address usability: how do we make the framework more
approachable?

One concept I've been researching a ton lately is *middleware*. Middleware
exists in a mature form in Ruby (via [Rack](https://rack.github.io)), Python
(via [WSGI](https://www.python.org/dev/peps/pep-0333/)), and Node (via
[Connect](https://github.com/senchalabs/connect) /
[ExpressJS](http://expressjs.com)); just about every language has some
exemplar. Even PHP has some examples already, in
[StackPHP](http://stackphp.com) and [Slim Framework](http://www.slimframework.com).

The basic concept of middleware can be summed up in a single method signature:

```javascript
function (request, response) { }
```

The idea is that objects, hashes, or structs representing the HTTP request and
HTTP response are passed to a callable, which does something with them. You
compose these in a number of ways to build an application.

<!--- EXTENDED -->

In Rack and StackPHP, you use objects, and pass middleware to other middleware:

```php
// This is pseudocode, and does not 1:1 represent any specific project:
class Action
{
    private $middleware;

    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    public function __invoke($request, $response)
    {
        // do something before

        call_user_func($this->middleware, $request, $response);

        // do something after
    }
}
```

In Connect, and, by extension, ExpressJS, instead of injecting the object, you
pass an additional callable, `next`, to the middleware function, which it can
invoke if desired:

```php
// This is pseudocode, and does not 1:1 represent any specific project:
class Action
{
    public function __invoke($request, $response, callable $next = null)
    {
        // do something before

        if ($next) {
            $next();
        }

        // do something after
    }
}
```

There are other patterns as well, but these are the two most prevalent. The
basic idea is the same: receive a request and response, do something with them,
and optionally tell the invoking process to do more.

What I like about the concept of middleware is that I can explain it succinctly
in such a way that another developer can understand it immediately. This is one
reason why middleware has thrived in these other languages: it's approachable
by developers from a wide-range of experience levels.

(Interesting side-note: Symfony 2 and Zend Framework 2 actually both implement
similar patterns — Symfony in its `HttpKernelInterface` and ZF2 in its
`DispatchableInterface`.)

However, middleware can only exist when there are good HTTP request and
response abstractions. In fact, I'd argue that middleware naturally evolves
when those abstractions are present already. Languages with good middleware
implementations have good HTTP abstractions.

PHP does not.

"But PHP was built for the web!" I hear many of you say. True. But more
specifically, it was built with [Common Gateway Interface](http://en.wikipedia.org/wiki/Common_Gateway_Interface)
(CGI) in mind. CGI is a way for the web server to offload the incoming request to a
script; in the early days, it actually would set a whole bunch of environment
variables, and your script would pull from those in order to get input and
return a response. This evolved into PHP's Server APIs (SAPI) — `mod_php` in
Apache, the php-fpm/FastCGI SAPI, etc. — and that data is present in PHP's
`$_SERVER` superglobal. PHP also tacked on other superglobals such as `$_GET`,
`$_POST`, and `$_COOKIE` to simplify getting the most common input data. But
PHP stopped there, at version 4.1.0 (!).

What this means is that PHP developers are left with a ton of work to do to get
at what should be the most common aspects of HTTP:

- You must analyze the `SCHEME`, `HTTP_X_FORWARDED_PROTO`, `HOST`,
  `SERVER_NAME`, `SERVER_ADDR`, `REQUEST_URI`, `UNENCODED_URL`,
  `HTTP_X_ORIGINAL_URL`, `ORIG_PATH_INFO`, and `QUERY_STRING` elements of the
  `$_SERVER` superglobal elements in order to fully and accurately determine
  the request URI in a cross-platform way. (Bonus points if you know why!)
- Headers are also in `$_SERVER`, with prefixes of `HTTP_`… unless they have to
  do with the various `Content-Type*` headers.
- Until 5.6, `php://input`, which stores the raw message content, is
  *read-once*, which means if multiple handlers need to inspect it, you must
  cache it — which poses problems if the cache is not known to all handlers.

When it comes to the response, as PHP developers, we have to learn that output
buffering exists and how to work with it. Why? Because if any content is sent
by the output buffer to the client before a header is sent, then PHP silently
discards the header. Good developers learn how things like `display_errors` and
`error_reporting` can affect output buffering, how to nest output buffers, and
more — and that's even when they're aggregating content to emit at once!

My point is that PHP's HTTP "abstractions", because they focus on the CGI
specification, and not HTTP messages, actually create a lot of work for PHP
developers. The abstractions present in Rack, WSGI, Node, and others are often
cleaner and more immediately usable (particularly
[Node's](http://nodejs.org/api/http.html), in my opinion).

***We need good HTTP abstractions to simplify web development for PHP
developers.***

Good HTTP abstractions will *also* create an ecosystem in which middleware can evolve.

As such, I've been working with the [Framework Interoperability Group](http://www.php-fig.org)
(FIG) since September to help finalize a set of standard HTTP message
interfaces so that we can create an ecosystem in which PHP developers can
create re-usable middleware that they can share. (The new proposal has the
designation
[PSR-7](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md).)

To me, this is the logical implication of Composer: *the ability to package
re-usable web-focussed widgets that can be composed into applications*.

In other words, we'll no longer write Zend Framework or Symfony or Laravel or
framework-flavor-of-the-day applications or
modules/bundles/packages/what-have-you. We'll write middleware that solves a
discrete problem, potentially using other third-party libraries, and then
compose them into our applications — whether those are integrated into a
framework or otherwise.

What this means is that we might compose middlewares that accomplish discrete
functionality in order to build up our website:

```php
$app = new MiddlewareRunner();
$app->add('/contact', new ContactFormMiddleware());
$app->add('/forum', new ForumMiddleware());
$app->add('/blog', new BlogMiddleware());
$app->add('/store', new EcommerceMiddleware());
$app->run($request, $response);
```

Another use case would be to use middlewares that provide runtime aspects that
affect the behavior of our application as a whole. As an example, consider an
API engine, where you might have middleware for each behavior you want to
implement:

```php
$app = new MiddlewareRunner();
$app->add(new Versioning());
$app->add(new Router());
$app->add(new Authentication());
$app->add(new Options());
$app->add(new Authorization());
$app->add(new Accepts());
$app->add(new ContentType());
$app->add(new Parser());
$app->add(new Params());
$app->add(new Query());
$app->add(new Body());
$app->add(new Dispatcher());
$app->add(new ProblemHandler());
$app->run($request, $response);
```

If I wanted to add my own authorization, I can look at the above, find the line
where that happens, and change it to use my own middleware. In other words,
*middleware can enable usability and composition for users*.

On top of that, in my experiments, well-written middleware and smart middleware
runners can also lead to incredible *performance*. You can typically stop
execution whenever you want by no longer calling `next()`, or by skipping the
decorated middleware, or by returning a response (depending on the middleware
runner architecture), and most well-written middleware will do pre-emptive
checks so that it exits (or calls `next()`) early if it has nothing to do based
on the current request. Couple this with good architectural practices like
dependency injection and lazy-loading, and you can actually address each of
usability, performance, and maintainability in your projects — not a bad coup!

(Caveat: as with any application architecture, you can also shoot yourself in
the foot; middleware is not a silver bullet or a guarantee.)

Fin
---

Too often, I feel as PHP developers we focus on the tools we use, and forget
that we're working in an HTTP-centric ecosystem. PHP doesn't help us, in that
regard. Additionally, I think we focus too much on our frameworks, and not
enough on how what we write could be useful across the entire PHP ecosystem.

If PSR-7 is ratified, I think we have a strong foot forward towards building
framework-agnostic web-focused components that have real re-use capabilities —
not just re-use within our chosen framework fiefdoms.

I'm working to do that, and I think we're getting close to a vote. If you're
interested in PSR-7, I urge you to take a look at the proposal:

- [https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md)

the current related pull requests and issues:

- [https://github.com/php-fig/fig-standards/issues?q=is%3Aopen+PSR-7](https://github.com/php-fig/fig-standards/issues?q=is%3Aopen+PSR-7)

and any discussion prefixed with `[PSR-7]` in the php-fig mailing list:

- https://groups.google.com/forum/\#!searchin/php-fig/subject\$3Apsr-7%7Csort:date

I've also created a prototype implementation of PSR-7:

- [https://github.com/phly/http](https://github.com/phly/http)

and a port of Connect to PHP using it:

- [https://github.com/phly/conduit](https://github.com/phly/conduit)

Join me in developing HTTP-centric PHP!

#### Updates

- Removed some inflammatory verbiage and rephrased a few areas to emphasize
  that the focus on CGI is the primary problem I want to address in PHP today.
