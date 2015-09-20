---
id: 2015-07-28-on-psr7-headers
author: matthew
title: 'On PSR7 and HTTP Headers'
draft: false
public: true
created: '2015-07-28T09:00:00-05:00'
updated: '2015-07-28T09:00:00-05:00'
tags:
    - http
    - php
    - programming
    - psr-7
---
Yesterday, a question tagged `#psr7` on Twitter caught my eye:

> #psr7 `Request::getHeader($name)` return array of single string instead of strings in #Slim3? cc: @codeguy pic.twitter.com/ifA9hCKAPs
>
> [@feryardiant](https://twitter.com/feryardiant) ([tweet](https://twitter.com/feryardiant/status/624705995097247744))

The image linked provides the following details:

> When I call `$request->getHeader('Accept')` for example, I was expected that I'll get something like this:
>
> ```php
> Array(
>     [0] => text/html,
>     [1] => application/xhtml+xml,
>     [2] => application/xml,
> )
> ```
>       
> but, in reallity I got this:
>
> ```php
> Array(
>     [0] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
> )
> ```
>       
> Is it correct?

In this post, I'll explain why the behavior observed is correct, as well as
shed a light on a few details of header handling in
[PSR-7](http://www.php-fig.org/psr/psr-7/).

<!--- EXTENDED -->

Headers in PSR-7
----------------

When creating the PSR-7 specification, we had to juggle a fair number of
details from the various HTTP specifications. Headers are one area that is
particularly difficult, due to the flexibility and ambiguity in the
specification.

The root of the ambiguity is that headers are allowed to have multiple values.
Headers *may* have multiple values, but it's up to the specification for any
given header.

Additionally how multiple values are represented is up to the given header. The
HTTP specifications allow using multiple invocations for the same header:

```http
X-Foo-Bar: baz
X-Foo-Bar: bat
```

The above would mean that the `X-Foo-Bar` header has two values, `baz` and
`bat`. Assuming the header allows multiple values at all; if it doesn't, then
it has a single value, and the last representation wins (`bat`, if you're
paying attention).

The other way to represent multiple values is using a separator. The
specifications indicate that if you want to have multiple values in the same
header line, you `should` use a comma (`,`) as a separator. However, you `may`
use any other separator you want. The `SetCookie` header is a prime example of
a header allowing multiple values that uses a completely different separator
(semicolon)!

So, to summarize:

- A header may or may not allow multiple values.
- Headers may be emitted more than once. If a header allows multiple values,
  then its value is the aggregate of each representation. If the header only
  allows one value, the last representation is the canonical value for that
  header.
- Headers may use a separator character in a single line in order to separate
  multiple values. That character is suggested to be a comma, but it can vary
  per-header.

The other big ambiguity in the specification is that the specification is
*extensible*, and specifically allows for *custom* headers.

This means that any general-purpose code representing HTTP, such as PSR-7,
cannot possibly know the entire ruleset governing all possible HTTP messages,
as it cannot know all potential header types, including whether they allow
multiple values or not.

With these two facts in mind — headers *may* have multiple values, and *custom*
headers are allowed — we made the following decisions with PSR-7:

### All headers are collections

All headers are assumed to have multiple values. This gives consistency of
usage, and puts the onus of knowing the semantics of any given header to the
consumer.

For that reason, the most basic access for a given header, `getHeader($name)`,
returns an array. That array can have the following values:

- It can be empty; this means the header was not, or will not be, present in
  the representation.
- A single string value.
- More than one string value.

### Naive Concatenation

Since the majority of headers only allow single values, and since most existing
libraries that parse headers only accept strings, we provided another method,
`getHeaderLine($name)`. This method guarantees return of a string:

- If the header has no values, the string will be empty.
- Otherwise, it concatenates the values using a comma.

We chose *not* to provide an argument indicating the separator to use, as the
specification only indicates commas as separators, but also to reduce
complexity of implementations. If you want to use a different separator, you
can do so yourself using `implode($separator, $message->getHeader($name))`.

### No Parsing

Because separator characters vary per-header, and because different headers
have different specifications regarding how to interpret the data, and because
the specification allows custom headers we cannot code for in a general-purpose
library, we decided that PSR-7 implementations *must not* parse header values
provided to them.

Practically this has two effects:

- For incoming requests, even if a header allows multiple comma-separated
  values, implementations must leave them intact. This ensures no data-loss.
- For complex values, you must pass them to a parser to decompose and interpret
  them.

The rule also has another motivation: to provide a semantic for emitting
headers with multiple values as either a single line or as multiple lines. If
all values are concatenated in a single line, the client or server can assume
that the message should be sent or was received with the header as a single
line, while an array of multiple lines would indicate multiple header lines.
This allows the *consumer* to decide how the header should be represented!

### Ramifications

The path we chose has some interesting ramifications. First, we ended up with a
highly consistent API. There's no ambiguity in terms of what data types I can
expect when I call `getHeader()` or `getHeaderLine()`. Second, I can be assured
that there has been no data loss once I have the results of one of those
operations; no process has attempted to parse the value and potentially alter
it.

The flip side is the Twitter comment from earlier. Let's look at that again.

Breaking it Down
----------------

Let's revisit what the author received from a `getHeader('Accept')` call:

```php
Array(
    [0] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
)
```

The `Accept` header allows multiple values, but expects them as a single
comma-concatenated string. Contrary to what the author expected, the above
represents the following values:

```php
[
    'text/html',
    'application/xhtml+xml',
    'application/xml;q=0.9',
    'image/web',
    '*/*;q=0.8',
]
```

Note that the values include the `;q=*` notations inline! The `Accept` header
separates values with commas, but each value can have additional key/value
attributes separated by semicolons as well.

Why isn't the above what we get from `getHeader()`? It goes back to the last
rule I mentioned regarding PSR-7 header treatment: **no parsing**. The Accept
header specification indicates that multiple values should be on the same line,
separated by commas, and that's precisely how browsers send it to the server;
PSR-7 takes the line as-is and sets it as the sole value in the array.

Recommendations
---------------

The above example provides another good lesson: Complex values should have
dedicated parsers. PSR-7 literally only deals with the low-level details of an
HTTP message, and provides no interpretation of it. Some header values, such as
the `Accept` header, require dedicated parsers to make sense of the value.

What does the value indicate?

- The client prefers `text/html`, `application/xhtml+xml`, and `image/webp`
  representations when possible; if any of those three are available, they are
  preferred *in that order*
- If none of the above are available, the next representation preferred is `application/xml`.
- Any other representation may be returned otherwise.

How do I know this? By reading the [Accept header specification](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html).
Which is ridiculously complex. And for which a number of libraries are already
written, *which can accept the Accept header value, parse it, and return the
priority queue for you*. **PSR-7 acts as the data source for such libraries,
but does no parsing itself.**

Fin
---

Hopefully, this post has demystified how PSR-7 represents and handles HTTP
headers. PSR-7 was designed to mirror the extensibility of the HTTP
specifications, provide consistency of usage, and data integrity.

One specific recommendation we made in the metadocument was that any processing
of headers be delegated to dedicated libraries. I'm hoping to see more of these
spring up as we see PSR-7 adoption ramp up.
