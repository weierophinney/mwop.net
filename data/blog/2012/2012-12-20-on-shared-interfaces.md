---
id: 2012-12-20-on-shared-interfaces
author: matthew
title: 'On php-fig and Shared Interfaces'
draft: false
public: true
created: '2012-12-20T14:23:00-06:00'
updated: '2012-12-20T14:23:00-06:00'
tags:
    - php
    - oop
---
This is a post I've been meaning to write for a long time, and one requested of
me personally by [Evert Pot](http://www.rooftopsolutions.nl/blog/) during the
Dutch PHP Conference in June 2012. It details some observations I have of
php-fig, and hopefully will serve as a record of why I'm not directly
participating any longer.

I was a founding member of the [Framework Interoperability Group](http://www.php-fig.org/),
now called "php-fig". I was one of around a dozen folks who sat around a table
in 2009 in Chicago during php|tek and started discussions about what we could
all do to make it possible to work better together between our projects, and
make it simpler for users to pick and choose from our projects in order to
build the solutions to their own problems.

The first "standard" that came from this was
[PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md),
which promoted a standard class naming convention that uses a 1:1 relationship
between the namespace and/or vendor prefix and the directory hierarchy, and the
class name and the filename in which it lives. To this day, there are both
those who hail this as a great step forward for cooperation, and simultaneously
others who feel it's a terrible practice.

And then nothing, for years. But a little over a year ago, there was a new push
by a number of folks wanting to do more. Paul Jones did a remarkable job of
spearheading the next
[two](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
[standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md),
which centered around coding style. Again, just like with PSR-0, we had both
those feeling it was a huge step forward, and those who loathe the direction.

What was interesting, though, was that once we started seeing some new energy
and momentum, it seemed that *everyone* wanted a say. And we started getting
dozens of folks a week asking to be voting members, and new proposal after new
proposal. Whether or not somebody likes an existing standard, they want to have
backing for a standard they propose.

And this is when we started seeing proposals surface for shared interfaces,
first around caching, and now around logging (though the latter is the first up
for vote).

<!--- EXTENDED -->

Shared Interfaces
-----------------

The idea around shared interfaces is simple: if we can come to a consensus on
the basic interface for a common application task, libraries and frameworks can
typehint on that shared interface, allowing developers to drop in the
implementation of their choosing — or even a standard, reference
implementation. The goal is to prevent Not Invented Here (NIH) syndrome, as
well as to make it simpler to re-use components between one library and
another. As an example, if you're using Framework A, and it has a caching
library, and you're consuming ORM B, you'd be able to pass the same cache
object to the ORM as you use in the framework.

Great goals, really.

But I'm not sure I buy into them.

Problems
--------

First, I agree that NIH is a problem.

Second, I *also* think there's space for *multiple implementations* of any
given component. Often there are different approaches that different authors
will take: one might focus on performance, another on having multiple adapters
for providing different capabilities, etc. Sometimes having a different
background will present different problem areas you want to resolve. As such,
having multiple implementations can be a very good thing; developers can look
at what each provides, and determine which solves the particular issues
presented in the current project.

Because of this latter point, I have my reservations about shared interfaces.

What if a particular approach requires deviating from the shared interface in
order to accomplish its goals? Additionally, in order to keep the greatest
amount of compatibility between projects, shared interfaces tend to be so
generic that specific implementations require developers to do a ton of manual
type checking and munging of parameters, leading to more overhead, more
difficulty testing and maintaining, and more difficulty documenting and
understanding.

As an example, consider the following (made up) signature for a log method:

```php
public function log($message, array $context = null);
```

What if your library supports an idea of priorities? Where would that
information go in the above signature — and would that differ between
libraries — would one library use the key for a completely different purpose?
What about logging objects — the signature doesn't say you can't, but how
would I know if a specific implementation supports it, and won't blow up if I
do pass one? Why must the `$context` be an array — why won't any `Traversable`
or `ArrayAccess` object work?

Basically, by being overly generic, the signature becomes a liability for those
implementing the interface; it prevents meaningful interoperability and leads
to splintering implementations.

*(Please note: the above is completely fictional and has no bearing on current
proposed or accepted standards. It is a thought exercise only.)*

Furthermore, if a given project writes their own implementation of a component,
and it has specialized features, why would they want to typehint on a generic,
shared interface that doesn't implement those features? This would be
counter-intuitive, as the project would then need to either check on additional
interfaces for the specialized capabilities, duck-type, etc. — all of which
make for more maintenance and code.

In summary, my primary problem with the idea of shared interfaces is that I
feel there is always room for new thinking and ideas in any given problem
space, and that this thinking should not be restricted by what already exists.
Secondarily, I feel that it's okay for a given project to be selective about
what capabilities it requires for its internal consumption and consistency, and
should not limit itself to a standardized interface.

But, but, SHARING
-----------------

*Remember, the first point I made was that I think NIH is a problem.* How do I
reconcile that with a firm stance against shared interfaces?

Easy: [bridges](http://en.wikipedia.org/wiki/Bridge_pattern) and/or
[adapters](http://en.wikipedia.org/wiki/Adapter_pattern).

Let's go back to that example of Framework A, its caching library, and working
with ORM B.

Let's assume that ORM B defines an interface for caching, and let's say it
looks like this:

```php
interface CacheInterface
{
    public function set($key, $data);
    public function has($key);
    public function get($key);
}
```

Furthermore, we'll assume that the expected parameter values and return types
are documented.

What we as a consumer of both Framework A and ORM B can do is build an
*implementation* of `CacheInterface` that accepts a cache instance from
Framework A, and proxies the various interface methods to that instance.

```php
class FrameworkACache implements CacheInterface
{
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function set($key, $data)
    {
        $item = new CacheItem($key, $data);
        $this->cache->setItem($item);
    }

    public function has($key)
    {
        return $this->cache->exists($key);
    }

    public function get($key)
    {
        $item = $this->cache->getItem($key);
        return $item->getData();
    }
}
```

Assuming your code is well-decoupled, and you're using some sort of Inversion
of Control container, you can likely create a factory for your ORM that will
grab the above class, with the cache injected, and inject it into the ORM
instance. Yes, it's a bit more work, but it's difficult to question the end
result: shared caching between the framework and the ORM — and no need for
shared interfaces, nor any need to sacrifice features within the framework or
the ORM.

Sharing is good, developing solutions is better
-----------------------------------------------

I think the core idea of the php-fig group is sound: *let's all start thinking
about how we can make it easier to operate with each other*. That said, my
thoughts on how to accomplish that goal have changed significantly, and boil
down to:

- Use naming conventions that will reduce collisions (i.e., use per-project
  vendor prefixes/namespaces)
- Use semantic versioning
- Keep your installation packages segregated
- Have a simple, discoverable way to autoload
- Provide interfaces for anything that could benefit from alternate implementations
  Don't write code that has side-effects in the global namespace (including
  altering PHP settings or superglobals)

Following these principals, you can play nice with each other, while still
fostering innovative and differentiating solutions to shared problems.
