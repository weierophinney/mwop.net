---
id: 2015-01-26-psr-7-by-example
author: matthew
title: 'PSR-7 By Example'
draft: false
public: true
created: '2015-01-26T09:20:00-06:00'
updated: '2017-02-21T08:50:00-06:00'
tags:
    - http
    - php
    - programming
    - psr-7
---
[PSR-7](http://www.php-fig.org/psr/psr-7) is now accepted!!!

~~I'm still hearing some grumbles both of "simplify!" *and* "not far enough!"
so I'm writing this posts to demonstrate usage of the currently published
interfaces, and to illustrate both the ease of use and the completeness and
robustness they offer.~~

First, though I want to clarify what PSR-7 is attempting.

<!--- EXTENDED -->

HTTP Messages
-------------

HTTP messages are relatively simple, which is why the protocol has succeeded
over the years. All messages have the following structure:

```http
<message line>
Header: value
Another-Header: value

Message body
```

Headers are key/value pairs. The keys are case insensitive. Values are strings.
The same header type may be emitted multiple times, in which case (typically)
the values are considered as a list; in most cases, these can also be expressed
by concatenating the values with comma delimiters.

The message body is a string, but typically handled by servers and clients as a
stream in order to conserve memory and processing overhead. This is incredibly
important when you transmit large data sets, and particularly when transmitting
files. As an example, PHP natively represents the incoming request body as the
stream `php://input`, and uses output buffers — a form of stream — to return a
response.

The message line is what differentiates a request from a response.

The message line of a request is called the request line, and has the following
format:

```http
METHOD request-target HTTP/VERSION
```

`METHOD` indicates the operation requested: GET, POST, PUT, PATCH, DELETE,
OPTIONS, HEAD, etc. The `VERSION` is typically 1.0 or 1.1 (usually 1.1 in
modern web clients). The `request-target` is where things get complex.

A request target can be one of four different forms:

- `origin-form`, which is the path and query string (if present) of the URI.
- `absolute-form`, which is an absolute URI.
- `authority-form`, which is the authority portion of the uri (`user-info`, if
  present; `host`; and `port`, if non-standard).
- `asterisk-form`, which is the string `*`.

Typically, an HTTP client will use the scheme and authority from a URI to make
the connection to the HTTP server, and then pass an origin-form target in the
transmitted HTTP request message. However, it's perfectly valid to send the
absolute URI as well. authority-form is typically only used with CONNECT
requests, which are usually performed when working with a proxy server.
asterisk-form is used with OPTIONS requests to get general capabilities of a
web server.

In short, there's a lot of moving parts in the request-target.

Now, to make things more complicated, when we look at URIs, we have the
following:

```
<scheme>://<authority>[/<path>][?<query string>]
```

The scheme, when doing HTTP requests, will be one of `http` or `https`. The
path is a well-known format as well. But what about authority?

```text
[user-info@]host[:port]
```

The authority *always* contains the host, which can be a domain name or an IP
address. The port is optional, and only needs to be included if it's
non-standard for the current scheme (or if the scheme is unknown). user-info is
of the form:

```text
user[:pass]
```

where password is optional. In fact, in current specifications, the
recommendation is to never include the password in a URI, to force prompting
for the value by the client.

The query string is a set of key-value pairs delimited by ampersands:

```text
?foo=bar&baz&quz=1
```

Depending on the language implementation, it can also model lists and hashes:

```text
?sort[]=ASC&sort[]=date&filter[product]=name
```

PHP will parse the above to:

```php
[
    'sort' => [
        'ASC',
        'date'
    ],
    'filter' => [
        'product' => 'name'
    ],
]
```

So, as if the request-target was not complex enough, URIs also present a fair
amount of moving parts!

Fortunately, responses are simpler. The response line looks like this:

```http
HTTP/VERSION <status>[ <reason>]
```

The `VERSION` is as stated earlier — usually 1.0 or 1.1, and typically 1.1. The
`status` code is an integer from 100—599 inclusive; usually the `reason` phrase
will be standard for a given status code.

That's the birds-eye view of HTTP messages. Let's look at how PSR-7 currently
models them.

Message Headers
---------------

Message header names are case insensitive. Unfortunately, most languages and
libraries do some sort of normalization that makes consumption difficult. As an
example, PHP has them in `$_SERVER` as all-caps, prefixed with `HTTP_`, and
substituting `_` for `-` (this is to conform with the [Common Gateway
Interface](http://www.w3.org/CGI/) (CGI) specification).

PSR-7 simplifies access to the headers by providing an object-oriented layer on
top of them:

```php
// Returns an empty array if not found:
$header = $message->getHeader('Accept');

// Returns an empty string if not found:
$header = $message->getHeaderLine('Accept');

// Test for a header:
if (! $message->hasHeader('Accept')) {
}

// If the header has multiple values, fetch them
// as an array:
$values = $message->getHeader('X-Foo');

// Or as a comma-separated string:
$values = $message->getHeaderLine('X-Foo');
```

All of the above work regardless of the case you specify for the header;
`accept`, `ACCEPT`, or even `aCCePt` would all have been valid header names and
received the same result.

PSR-7 stipulates that fetching all headers will return a structure in the form:

```php
/* Returns the following structure:
    [
        'Header' => [
            'value1'
            'value2'
        ]
    ]
 */
foreach ($message->getHeaders() as $header => $values) {
}
```

By specifying the structure to return, consumers know exactly what to expect,
and can process headers in a uniform manner — regardless of the
implementation.

But what about when you want to add headers to a message — for instance, to
create a request to pass to an HTTP client?

The messages in PSR-7 are modeled as [value objects](http://en.wikipedia.org/wiki/Value_object);
this means that any change to state is essentially a different value. So,
assigning a header will result in a new message instance:

```php
$new = $message->withHeader('Location', 'http://example.com');
```

If you are only interested in the updated value, you can just re-assign it:

```php
$message = $message->withHeader('Location', 'http://example.com');
```

If you want to append another value to a header that may already be present,
you can also do that:

```php
$message = $message->withAddedHeader('X-Foo', 'bar');
```

Or even remove a header:

```php
$message = $message->withoutHeader('X-Foo');
```

Message Bodies
--------------

As noted above, message bodies are usually treated as streams for performance
reasons. This is particularly important when you're transmitting files over
HTTP, as you don't want to use up all available memory to your current process.
Most HTTP message implementations I've surveyed forget this or try to hack it
on after-the-fact (yes, even ZF2 is guilty of this!). If you need more
convincing, or just more background on why this is a good idea, [Michael Dowling blogged about the rationale to use streams in PSR-7](http://mtdowling.com/blog/2014/07/03/a-case-for-higher-level-php-streams/)
last summer.

Accordingly, message bodies in PSR-7 are modeled as
[streams](https://github.com/php-fig/http-message/blog/master/src/StreamInterface.php).

"But that's too hard for the 80% use case of using a string!" is the most
common argument we hear on the list about this aspect of the proposal. Well,
then, consider this:

```php
$body = new Stream('php://temp');
$body->write('Here is the content for my message!');
```

> The above example, and all concrete examples of messages in this post will be
> using [phly/http](https://github.com/phly/http), a library I've written that
> tracks the progress of PSR-7. In this case, `Stream` implements
> `StreamInterface`.

Essentially, you get a slim, object oriented interface to the body that allows
you to append to it, read it, and more. Want to replace it? Create a new
message body and update your HTTP message:

```php
$message = $message->withBody(new Stream('php://temp'));
```

My point is that while the concept of streams may be hard to wrap your head
around, the actual implementation and usage is not.

One benefit to having the `StreamInterface` in PSR-7 is that it provides
flexibility for a number of different patterns. As an example, you could create
a "callback" implementation that on a `read()` or `getContents()` operation
delegates to a callback to return the message content (Drupal, in particular,
uses this pattern). Or an "iterator" implementation that uses any `Traversable`
to return and/or aggregate content. The point is, you can get creative with the
interface in order to accomplish a variety of different patterns for modeling
the message body, and you are not restricted to simply strings or files.

The `StreamInterface` exposes the subset of stream operations that will be of
most use with HTTP message bodies; it is by no means comprehensive, but it
covers a large set of possible operations.

I'm personally a fan of using `php://temp` streams, as they are in-memory
unless they grow too big — at which point they write to temp files on disk. The
approach can be quite performant.

Responses
---------

So far, I've looked at features common to any message. I'm now going to look at
responses in particular.

A response has a status code and a reason phrase:

```php
$status = $response->getStatusCode();
$reason = $response->getReasonPhrase();
```

That's pretty easy to remember. Now, what if I'm *building* a response?

Reason phrases are considered optional — but also specific to the status code
being set. As such, the only response-specific mutator is `withStatus()`:

```php
$response = $response->withStatus(418, "I'm a teapot");
```

> Again, messages are modeled as value objects; a change to any value results
> in a new instance, which needs to be assigned. In most cases, you'll just
> reassign the current instance.

Requests
--------

Requests contain the following:

- Method.
- URI/request-target.

The latter provides a bit of a challenge to model. In likely 99% of use cases,
we'll be seeing an origin-form request-target — in other words, something that
looks like a URI. However, we still need to accommodate other request-target
types. As such, the request interface does the following:

- It composes a `UriInterface` instance, which models the URI itself
- It provides two methods around request-targets: `getRequestTarget()`, which
  will return the request target, and calculate it if not present (using the
  composed URI to return an origin-form, or to return a "/" if no URI is
  composed or it does not have a path); and `withRequestTarget()`, to create a
  new instance with a specific request target.

This latter allows you to address the non-origin-form requests targets when
needed — while keeping the URI information present in the request, which you
may need for establishing HTTP client connections.

Let's get the method and URI from the request:

```php
$method = $request->getMethod();
$uri    = $request->getUri();
```

`$uri` in this case will be an instance of the `UriInterface`, and allows you
to introspect the URI:

```php
// URI parts:
$scheme    = $uri->getScheme();
$userInfo  = $uri->getUserInfo();
$host      = $uri->getHost();
$port      = $uri->getPort();
$path      = $uri->getPath();
$query     = $uri->getQuery();     // the query STRING
$authority = $uri->getAuthority(); // [user-info@]host[:port]
```

Just like the HTTP messages, URIs are treated as value objects, as changing any
portion of a URI changes its value; as such, mutator operations return a new
instance:

```php
$uri = $uri
    ->withScheme('http')
    ->withHost('example.com')
    ->withPath('/foo/bar')
    ->withQuery('?baz=bat');
```

Because changing the URI means a new instance, if you want the changes
reflected in your request, you'll need to notify the request; and, as with any
message, if you need to change the method or URI in your request instance, use
the `with` methods:

```php
$request = $request
    ->withMethod('POST')
    ->withUri($uri->withPath('/api/user'));
```

Typically for requests, you want the `Host` header to match the value in the
URI. As such, by default, `withUri()` will also set the `Host` header on the
returned instance based on the value present in the URI. If you want to keep
the original value, the method takes an optional second argument,
`$preserveHost`, which, if set to a boolean `true` value, will do exactly what
it says.

Server-Side requests
--------------------

Server-side requests have some slightly different concerns than a standard HTTP
request message. PHP's Server API (SAPI) does a number of things for us
normally that, as PHP developers, we've come to rely on:

- Deserialization of query string arguments (`$_GET`).
- Deserialization of urlencoded form data submitted via POST (`$_POST`).
- Deserialization of cookies (`$_COOKIE`).
- Identification and handling of file uploads (`$_FILES`).
- Encapsulation of CGI/SAPI parameters (`$_SERVER`).

Query string arguments, form data, and cookies can be discovered from other
aspects of the request, but it's convenient to have them already parsed for us.
That said, there are cases where we may want to manipulate those values:

- For APIs, the data may be in XML or JSON, and may be submitted over methods
  other than POST. As such, we'll need to deserialize the data — and then
  re-inject it into the request.
- Many frameworks are now encrypting cookies — which means that they need to be
  decrypted, and re-injected into the request.

So, PSR-7 offers another interface, `ServerRequestInterface`, which extends the
base `RequestInterface`, and offers features around these values:

```php
$query   = $request->getQueryParams();
$body    = $request->getBodyParams();
$cookies = $request->getCookieParams();
$files   = $request->getUploadedFiles();
$server  = $request->getServerParams();
```

Let's say you are writing an API, and want to accept JSON requests; doing so
might look like the following:

```php
$accept = $request->getHeader('Accept');
if (! $accept || ! preg_match('#^application/([^+\s]+\+)?json#', $accept)) {
    $response->getBody()->write(json_encode([
        'status' => 406,
        'detail' => 'This API can only provide JSON representations',
    ]));
    emit($response
        ->withStatus(406, 'Not Acceptable')
        ->withHeader('Content-Type', 'application/problem+json')
    );
    exit();
}

$body = (string) $request->getBody();
$request = $request
    ->withBodyParams(json_decode($body));
```

The above demonstrates several features. First, it shows retrieving a request
header, and branching logic based on that header. Second, it shows populating a
response object in the case of an error. (`emit()` is a hypothetical function
that would take the response object and emit headers and content.) Finally, it
shows retrieving the body, deserializing it, and re-injecting the request.

`with` methods exist for each of the various input types available to
`ServerRequestInterface` instances.

### Uploaded Files

Uploaded files seem like they should be relatively straight-forward: just use
`$_FILES`, right? Wrong.

- In cases where you have arrays of uploads (for example, when you're using
  JavaScript to allow people to incrementally upload more files), `$_FILES`
  generates a very different structure.
- In non-SAPI environments (for example, testing, or when using
  [ReactPHP](http://reactphp.org)), `$_FILES` is not populated.
- Even in SAPI environments, if you're not in a `POST` request, `$_FILES` is
  not populated

PSR-7 smooths this over by having uploaded files represented as a tree of
`UploadedFileInterface` instances. This interface defines methods for
introspecting the upload (for example, the filename associated, the media type,
and the size), but also provides some behavior:

- `getStream()` will return a `StreamInterface` instance, allowing you to
  manipulate the upload as a stream; this can be useful to stream it to a CDN,
  for instance.
- `moveTo()` allows you to move the upload — after validating it, of course! —
  to another location; on SAPI environments, this will use
  `move_uploaded_file()`, ensuring proper garbage cleanup by PHP.

Practically speaking, it means you can interact with file uploads very simply:

```php
// Single upload:
$avatar = $request->getFileUploads()['avatar']; // UploadedFileInterface!

// Arrays of uploads:
$icon1 = $request->getFileUploads()['profile']['icons'][0]; // UploadedFileInterface!
$icon2 = $request->getFileUploads()['profile']['icons'][1]; // UploadedFileInterface!

// Move an uploaded file:
$icon1->moveTo('data/uploads/icons/', UUID::idv4() . '.png');

// Stream an uploaded file:
// Note: StreamRegister is a fictional utility for creating a PHP stream 
//       wrapper from a StreamInterface instance.
stream_copy_to_stream(StreamRegister($avatar->getStream()), $s3Stream);
```

### Attributes

Another feature of server-side requests are "attributes." These are intended
for storing values that are computed from the current request. A common use
case is for storing the results of routing (decomposing the URI to key/value
pairs).

The `attributes` API includes:

- `getAttribute($name, $default = null)` to retrieve a single named attribute, and return a default value if the attribute is not present.
- `getAttributes()` to retrieve the entire set of attributes currently stored.
- `withAttribute($name, $value)` to return a new `ServerRequestInterface` instance that composes the given attribute.
- `withoutAttribute()` to return a new `ServerRequestInterface` instance that does not compose the given attribute.

As an example, let's use the [Aura Router](https://github.com/auraphp/Aura.Router)
with our request instance:

```php
use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;

$router = new Router(
    new RouteCollection(new RouteFactory()),
    new Generator()
);

$path  = $request->getUri()->getPath();
$route = $router->match($path, $request->getServerParams());
foreach ($route->params as $param => $value) {
    $request = $request->withAttribute($param, $value);
}
```

The request instance, in this case, is used to marshal data to feed to the
router, and then the results of routing are used to seed the request instance.

Use Cases
---------

Now that you've had a whirlwind tour of the various components of PSR-7, let's
turn to some concrete use cases.

### Clients

The editor prior to myself on PSR-7, [Michael Dowling](http://mtdowling.com),
is the author of the popular HTTP client [Guzzle](http://guzzlephp.org) — so
it's a perfectly natural leap that PSR-7 will benefit HTTP clients. Let's
consider how.

First, it means that developers will have a unified message interface to use
for making requests; they can pass PSR-7 request instances to a client, and
will get PSR-7 response instances in return.

```php
$response = $client->send($request);
```

Because messages and URIs are modeled as value objects, it also means that
developers can create base instances of requests and URIs, and create discrete
requests and URIs from them:

```php
$baseUri     = new Uri('https://api.example.com');
$baseRequest = (new Request())
    ->withUri($baseUri)
    ->withHeader('Authorization', $apiToken);

while ($action = $queue->dequeue()) {
    // New response instance! Only contains
    // URI and Authorization header from base.
    $request = $baseRequest
        ->withMethod($action->method)
        ->withUri($baseUri->withPath($action->path)); // new URI!

    foreach ($action->headers as $header => $value) {
        // The base request WILL NOT receive these headers, ensuring subsequent
        // requests only compose the headers they need!
        $request = $request->withHeader($header, $value);
    }
    
    $response = $client->send($request);
    $status   = $response->getStatusCode();
    if (! in_array($status, range(200, 204))) {
        // Request failed!
        break;
    }

    // Grab the data!
    $data->enqueue(json_decode((string) $response->getBody()));
}
```

What PSR-7 provides is a standard way to interact with the requests you send
with the client, and the responses you receive. By implementing value objects,
we enable some interesting use cases with regards to simplifying the "reset
request" pattern — changing the request always results in a new instance,
allowing us to have a base instance with a known state that we can always build
from.

### Middleware

I won't go too much into this, as [I've already done so before](https://mwop.net/blog/2015-01-08-on-http-middleware-and-psr-7.html).
The basic idea, however, is this:

```php
function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next = null
) {
}
```

The function accepts the two HTTP messages, and does something with them —
which might include delegating to the "next" middleware available. Middleware
usually returns a response.

Another pattern often used is the "lambda" pattern (thanks to [Larry Garfield](http://www.garfieldtech.com/)
for coining this term on the mailing list!):

```php
/* response = */ function (ServerRequestInterface $request) {
    /* ... */
    return $response;
}
```

In lambda middleware, you compose one into another:

```php
$inner = function (ServerRequestInterface $request) {
    /* ... */
    return $response;
};
$outer = function (ServerRequestInterface $request) use ($inner) {
    /* ... */
    $response = $inner($request);
    /* ... */
    return $response;
};
$response = $outer($request);
```

And then there's the pattern popularized by Rack and WSGI, in which the each
middleware is an object, and is passed to the outer:

```php
class Command
{
    private $wrapped;

    public function __construct(callable $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        // manipulate the request, maybe
        $new = $request->withAttribute('foo', 'bar');

        // delegate to the middleware we wrap:
        $result = ($this->wrapped)($new, $response);

        // see if we got a response
        if ($result instanceof ResponseInterface) {
            $response = $result;
        }

        // manipulate the response before returning
        return $reponse->withHeader('X-Foo', 'Bar');
    }
}
```

The idea behind middleware is that it's composable, and follows a standard,
predictable pattern with predictable behavior. It's a great way to write
re-usable web components.

### Frameworks

One thing frameworks have been providing for many years is… HTTP message
abstraction. PSR-7 aims to provide a common set of interfaces so that
frameworks can use the same set of abstractions. This will enable developers to
write re-usable, framework-agnostic web components that frameworks can consume
— or, at least, that's what *I* would like to see!

Consider Zend Framework 2: it defines the interface
`Zend\Stdlib\DispatchableInterface` which is the base interface for any
controller you want to use in the framework:

```php
use Zend\Http\RequestInterface;
use Zend\Http\ResponseInterface;

interface DispatchableInterface
{
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response
    );
}
```

This actually looks a lot like the middleware examples above; the only real
difference is that it consumes framework-specific HTTP message implementations.
What if, instead, it could consume PSR-7?

Most implementations of HTTP messages in frameworks are mutable, which means
that you can change the state of the message at any time. This can lead to
problems at times, particularly if you have assumptions about message state
that are no longer true. The other problem, though, is tracking down when state
changes.

PSR-7's messages are value objects. As such, you would need to notify the
application somehow of any change to one of the messages. This becomes more
explicit, and thus easier to trace in your code (both with step debuggers as
well as static analysis tools).

As an example, if ZF2 were updated to use PSR-7 messages, developers would need
to notify the `MvcEvent` of any changes they want propagated to later
consumers:

```php
// Inside a controller:
$request  = $request->withAttribute('foo', 'bar');
$response = $response->withHeader('X-Foo', 'bar');

$event = $this->getEvent();
$event->setRequest($request)
      ->setResponse($response);
```

The above code makes it explicit that we are changing application state.

Having value objects makes simpler one specific practice: the idea of
dispatching "sub-requests" or implementing Hierarchical MVC (HMVC). In these
cases, you can create new requests based on the current without altering it,
*ensuring the application state remains unchanged.*

Essentially, for many frameworks, dropping in the PSR-7 messages will lead to
portable abstractions that can be used across frameworks, and make it possible
to consume generic middleware relatively easily. To adopt the messages,
however, will likely require some minor changes such as the above when
developers need to modify the messages for use with the application state.

Resources
---------

Hopefully, you're starting to see the benefits PSR-7 will provide: a unified,
complete abstraction around HTTP messages. Further, the abstraction can be used
for either side of the HTTP transaction — whether you're sending requests via
an HTTP client, or handling a server-side request.

The PSR-7 specification is not yet final, but what I've outlined above is not
likely to undergo significant change before putting it forth for a vote. If you
want more details, you can read the specification:

- [https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md)

I also encourage you to read the metadocument for the proposal, as it describes
the goals, design decisions, and results of the (endless) debates over the past
two years:

- [https://github.com/php-fig/fig-standards/blob/master/proposed/http-message-meta.md](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message-meta.md)

The actual interfaces are published as the package `psr/http-message`, which
you can install via composer. It is always updated at the same time as the
proposal.

I've created a library, `phly/http`, which provides concrete implementations of
the proposed interfaces; I update it whenever I update the proposal. It, too,
is installable via composer.

Finally, if you want to play with middleware based on PSR-7, you have a couple
of options:

- [phly/conduit](https://github.com/phly/conduit), a port of Sencha's [Connect](https://github.com/senchalabs/connect) to PHP using `phly/http` and `psr/http-message` as its foundation.
- [Stacker](https://github.com/Crell/stacker), a [StackPHP](http://stackphp.com)-like implementation written by Larry Garfield.

I'm looking forward to passage of PSR-7; I think it will enable a whole new
breed of PHP applications.

#### Updates

- *2015-01-28*: Updated post to reflect psr/http-message 0.8.0. That version
  renamed `UriTargetInterface` to `UriInterface`, and modified it such that it
  now only models URIs. `RequestInterface` was modified to add the methods
  `getRequestTarget()` and `withRequestTarget()`, which allow simpler and
  better flexibility around non-origin-form request-targets. The post was
  updated to reflect these changes.
- *2015-01-29*: Fixed links to FIG proposal/meta document.
- *2015-02-01*: Corrected description of lambda middleware, and noted the last middleware pattern is that popularized by Rack and WSGI.
- *2015-05-04*: Updated to ensure it follows the interfaces as outlined at the end of the second Review period of PSR-7 (psr/http-message 0.11.0); added section on file uploads.
- *2015-05-18*: PSR-7 is now accepted!
- *2017-02-21*: Corrected examples to use status 406 for "Not Acceptable" (instead of 405, which is actually "Method Not Allowed").
