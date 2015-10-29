---
id: 250-Simple-Interfaces-and-Micro-MVCs
author: matthew
title: 'Simple Interfaces and Micro MVCs'
draft: false
public: true
created: '2010-12-22T18:07:04-05:00'
updated: '2011-02-24T09:54:06-05:00'
tags:
    0: php
    2: 'zend framework'
---
My job is great: I get to play with technology and code most days. My job is
also hard: how does one balance both functionality and usability in programming
interfaces?

I've been working, with [Ralph Schindler](http://ralphschindler.com), on a
[set of proposals](http://bit.ly/zf2mvcprops) around the
[Zend Framework](http://framework.zend.com/) 2.0 MVC layer, specifically the
"C", or "Controller" portion of the triad. There are a ton of requirements we're
trying to juggle, from making the code approachable to newcomers all the way to
making the code as extensible as possible for the radical performance tuning
developers out there.

<!--- EXTENDED -->

One interface I've been toying with is inspired by two very different sources.
The first is PHP's own [SoapServer API](http://php.net/SoapServer) (which we use
already in our various server components); the other was a discussion I had with
[Fabien Potencier](http://fabien.potencier.org) (of Symfony fame) a couple years
ago, where he said the goal of Symfony 2 would be "to transform a request into a
response."

What I've come up with right now is the following:

```php
interface Dispatchable
{
    /**
     * @return Response
     */
    public function dispatch(Request $request);
}
```

I can hear some of you ZF folks saying already, "Really, that's all you've come
up with so far?" Here's why I think it may be remarkable:

> ***It makes it trivially simple to do a ZF1 style MVC, incorporate server
> endpoints as controllers, or to write your own micro MVC.***

The idea is that this interface (and the Request/Response interfaces) become the
basic building blocks for both a standard ZF MVC implementation, or your own
custom MVC implementation.

Which is where the subject of micro MVCs finally becomes relevant.

Micro MVCs
----------

A little over a year ago, with PHP 5.3 finally releasing, I started seeing a
number of "micro MVC frameworks" popping up; seriously, for a while there, it
seemed like every other day, [phpdeveloper](http://phpdeveloper.org/) was
posting a new one every other day.

Micro MVCs are quite interesting. If you consider the bulk of the websites you
encounter, they really only consist of a few pages, and a smattering of actual
functionality that requires things like form handling or models. As such, using
a full-blown MVC such as ZF, Symfony, even CodeIgniter, seems crazy. A micro MVC
addresses simultaneously the issues of simplification and expressiveness; the
point is to get the work done as quickly as possible, preferably with as few
lines as possible.

In looking at many of these micro MVC frameworks, I noted a few things:

- Most were either using regex for routing, or a lightweight router such as
  [Horde Routes](http://dev.horde.org/routes/) to route the request.
- Most were utilizing closures and/or currying to then map the routing results
  to "actions".

So I whipped up a little something using the above `Dispatchable` interface, to
see what I might be able to do.

```php
use Zend\Stdlib\Dispatchable,
    Zend\Http\Response as HttpResponse,
    Fig\Request,
    Fig\Response;

class Dispatcher implements Dispatchable
{
    protected $controllers;

    public function attach($spec, $callback = null)
    {
        if (is_array($spec) || $spec instanceof \Traversable) {
            foreach ($spec as $controller => $callback) {
                $this->attach($controller, $callback);
            }
            return $this;
        }

        if (!is_scalar($spec)) {
            throw new \InvalidArgumentException('Spec must be scalar or traversable');
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Callback must be callable');
        }

        $this->controllers[$spec] = $callback;
        return $this;
    }

    /**
     * Dispatch a request
     * 
     * @param  Request $request 
     * @return Response
     */
    public function dispatch(Request $request)
    {
        if (!$controller = $request->getMetadata('controller')) {
            return new PageNotFoundResponse( '<h1>Page not found</h1>' );
        }

        if (!array_key_exists($controller, $this->controllers)) {
            return new PageNotFoundResponse('<h1>Page not found</h1>');
        }

        $handler  = $this->controllers[$controller];
        $response = $handler($request);

        if (is_string($response)) {
            return new HttpResponse($response);
        }
        if (!is_object($response)) {
            return new ApplicationErrorResponse('<h1>An error occurred</h1>');
        }
        if (!$response instanceof Response) {
            if (!method_exists($response, '__toString')) {
                return new ApplicationErrorResponse('<h1>An error occurred</h1>');
            }
            return new HttpResponse($response->__toString());
        }
        return $response;
    }
}
```

Don't worry about the various objects referenced; the main thing to understand
is that it's using those same building blocks I referred to earlier: `Request`,
`Response`, `Dispatchable`. In action, it looks like this:

```php
use Zend\Controller\Router,
    Zend\Http\Request;

$request = new Request;

$router = new Router;
/*
 * Configure some routes here. We'll assume we've somehow configured routes
 * mapping the following controllers:
 * - homepage
 * - foo
 * - rest
 * - foobar
 */
$router->route($request);

$dispatcher = new Dispatcher();
$dispatcher
->attach('homepage', function($request) {
    // Simply returning a string:
    return '<h1>Welcome</h1> <p>Welcometo our site!</p>';
})
->attach('foo', function($request) {
    // Simply returning a string:
    return '<h1>Foo!</h1>';
})
->attach('rest', function($request) {
    // Example of a "REST" service...
    switch ($request->getMethod()) {
        case 'GET':
            if (!$id = $request->query('id', false)) {
                // We have a "list operation"...
                // Assume we somehow grab the list and create a response
                return $response;
            }
            // We have an ID -- fetch it and return the page
            break;
        case 'POST':
            // Create document and return a response
            break;
        case 'PUT':
            if (!$id = $request->query('id', false)) {
                // No ID in the query string means no document!
                // Return a failure response
            }
            // We have an ID -- fetch and update from PUT params, and
            // return a response
            break;
        case 'DELETE':
            if (!$id = $request->query('id', false)) {
                // No ID in the query string means no document!
                // Return a failure response
            }
            // We have an ID -- delete, and // return a response
            break;
        default:
            return new ApplicationErrorResponse('Unknown Method');
            break;
    }
})
->attach('foobar', function($request) {
    // Curry in controllers to allow them to be lazy-loaded, and to ensure we 
    // get a response object back (Dispatcher will take care of that).
    $controller = new FooBarController();
    return $controller->dispatch($request);
});

$response = $dispatcher->dispatch($request);
$response->emit();
```

It's dead simple: we attach named callbacks to the `Dispatcher`. The `Dispatcher`
checks to see if the `Router` found a controller name in the `Request`, and, if it
did and a callback for it exists, executes it. If it gets a string, we use that
as the content; an exception triggers an `ApplicationErrorResponse`, and if we
get a `Response` object back, we just use it.

While I did the `Dispatcher` configuration/setup in the same script, it could have
been done as an include file to simplify that script endpoint.

The point is that the interface definitions made this really, really easy to
come up with and implement in a matter of minutes.

*I'm not sure if this will end up being in ZF2; even if it isn't, it still meets
the goal I set out at the start of this post: balancing usability with
flexibility.*

[Discuss!](http://bit.ly/zf2mvcprops)

#### Updates

- **2011-02-24**: Fixed first class declaration example to use "implements" instead of "extends"
