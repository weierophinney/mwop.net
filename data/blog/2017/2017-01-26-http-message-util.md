---
id: 2017-01-26-http-message-util
author: matthew
title: 'PSR-7 Request and Method Utilities'
draft: false
public: true
created: '2017-01-26T13:50:00-05:00'
updated: '2017-01-26T13:50:00-05:00'
tags:
    - php
    - programming
    - psr-7
---

We all know the standard HTTP request methods and status codes, right? Or do we?

We definitely know whether or not they should be integers or strings, and/or how
string values should be normalized, right?

And our IDEs can _totally_ autocomplete them, right?

Oh, that's not the case?

<!--- EXTENDED -->

Some time ago, a few folks floated the idea of creating a utility repository
related to the [PSR-7](http://www.php-fig.org/psr/psr-7/)
[psr/http-message](https://github.com/php-fig/http-message) package, but
containing some useful bits such as constants for HTTP request methods and
status codes.

Six months ago, we released it... but didn't publicize it. I remembered that
fact today while writing some unit tests that were utilizing the package, and
thought I'd finally write it up.

The package is [fig/http-message-util](https://github.com/php-fig/http-message-util),
and is available via Composer and Packagist:

```bash
$ composer require fig/http-message-util
```

It provides two interfaces:

- `Fig\Http\Message\RequestMethodInterface`, containing constants for HTTP
  request method values.
- `Fig\Http\Message\StatusCodeInterface`, containing constants for HTTP status
  code values.

The constants are prefixed with `METHOD_` and `STATUS_`, respectively, and use
the standard names as presented in the various IETF specifications that
originally define them.

As an example, I could write middleware that looks like this:

```php
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\EmptyResponse;

class RequestMethodNegotiation implements MiddlewareInterface
{
    private $alwaysAllowed = [
        RequestMethod::METHOD_HEAD,
        RequestMethod::METHOD_OPTIONS,
    ];

    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $path = $request->getUri()->getPath();
        if (! isset($this->map[$path])) {
            return $delegate->process($request);
        }

        $method = $request->getMethod();
        if (in_array($method, $this->alwaysAllowed, true)) {
            // Always allowed
            return $delegate->process($request);
        }

        if (in_array($method, $this->map[$path], true)) {
            // In map; proceed
            return $delegate->process($request);
        }

        // Not allowed!
        return new EmptyResponse(StatusCode::STATUS_METHOD_NOT_ALLOWED, [
            'Allow' => implode(',', $this->map[$path]);
        ]);
    }
}
```

The things to notice in the above are:

- `$alwaysAllowed` uses the `RequestMethodInterface` constants in order to
  provide a list of always allowed HTTP methods; it doesn't use strings, which
  are prone to typos.

- When a dis-allowed method is encountered, we use a `StatusCodeInterface`
  constant to provide the status. This allows us to use code completion,
  but also signify the _intent_ of the code. Integer values are great, but
  unless you have all the status codes memorized, it's often easy to forget
  what they _mean_.

The other thing to notice is that I alias the interfaces to shorter names.
We require interfaces to have the `Interface` suffix in FIG, but in situations
like these, I don't particularly care that the constants are defined in an
_interface_; I just want to consume them. This is one of the reasons PHP
supports aliasing.

If you're not already using this package, and use PSR-7 middleware, I highly
recommend checking the package out!
