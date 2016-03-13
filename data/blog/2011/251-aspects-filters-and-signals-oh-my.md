---
id: 251-aspects-filters-and-signals-oh-my
author: matthew
title: 'Aspects, Filters, and Signals, Oh, My!'
draft: false
public: true
created: '2011-01-10T09:30:00-05:00'
updated: '2011-01-14T08:53:52-05:00'
tags:
    0: php
    1: oop
    3: symfony
    4: 'zend framework'
    5: 'zeta components'
---
Last month, during [PHP Advent](http://phpadvent.org), [gwoo](http://ohloh.net/accounts/gwoo) wrote an interesting post on [Aspect-Oriented Design](http://phpadvent.org/2010/aspect-oriented-design-by-garrett-woodworth), or Aspect Oriented Programming (AOP) as it is more commonly known. The article got me to thinking, and revisiting what I know about AOP, Intercepting Filters, and Signal Slots -- in particular, what use cases I see for them, what the state of current PHP offerings are, and where the future may lie.

But first, some background is probably in order, as this is a jargon-heavy post.

<!--- EXTENDED -->

Aspect Oriented Programming
---------------------------

I was first introduced to AOP in 2006 via an
[April 2006 php|architect article by Dmitry Sheiko](http://www.phparch.com/magazine/2006-2/april/).
That article detailed adding calls at various places in a method where you might
want to hook into functionality — for instance, to log, cache, etc. Expanding
on this, I considered other possibilities: manipulating incoming arguments,
validation, ACL checks, implementing write-through caching strategies, and more.
The approach is daunting, however; typical, naive implementations lead to a lot
of boiler-plate code:

```php
interface Listener
{
    public function notify($signal, $argv = null);
}

class Foo
{
    protected $listeners;

    public function attach($signal, Listener $listener)
    {
        $this->listeners[$signal][] = $listener;
    }

    public function doSomething($arg1, $arg2)
    {
        foreach ($this->listeners as $listener) {
            $listener->notify('preDoSomething', func_get_args());
        }
        
        // do some work
        
        foreach ($this->listeners as $listener) {
            $listener->notify('postDoSomething', $result);
        }
    }
}
```

The article didn't go into any real details on how you might short-circuit the
filters or handle return values from them, and aspect handling itself was not
detailed completely. As such, the code begins to add up, particularly if many
classes and/or methods implement the functionality.

Intercepting Filters
--------------------

A similar concept to AOP is the idea of
[Intercepting](http://java.sun.com/blueprints/corej2eepatterns/Patterns/InterceptingFilter.html)
[Filters](http://msdn.microsoft.com/en-us/library/ff647251.aspx). Like AOP, the
idea is to separate cross-cutting concerns such as logging, debugging, and more
from the actual logic the component exposes. The difference is that typically
Intercepting Filters are language independent, have a standard implementation in
a given framework, and can be re-used. The approach gwoo used in his post falls
more under this category.

[Lithium](http://lithify.me/), a PHP 5.3 framework and the reference for gwoo's
article, has a very intriguing approach. Instead of calling the filters
explicitly within the body of the code, they suggest that the body of the code
simply becomes one of the filters, via a closure:

```php
Dispatcher::applyFilter('run', function ($self, $params, $chain) {
    // do something...
    return $chain->next($self, $params, $chain);
});
```

In Lithium, each filter is responsible for calling the next (each filter
receives the chain as its third and final argument); as soon as one doesn't call
`next()`, execution is stopped, and the result returned (or at least that's how
I read the source). You can call the chain either before or after the code you
want to execute in each filter; placement will determine whether it's a pre- or
a post-filter. The approach answers a number of the concerns I outlined
previously — namely, standardization of approach, and the ability to
short-circuit execution.

The above example defines a filter that will run when the `run()` method of the
`Dispatcher` class is executed. `$self` will typically be the object instance,
`$params` an array of the parameters passed to the method, and `$chain` as
described above. The method itself will execute any filters — typically with
something like this:

```php
use lithium/core/Object as BaseObject;

class Foo extends BaseObject
{
    public function doSomething($with, $these, $args)
    {
        $params = compact('with', 'these', 'args');
        
        return $this->_filter(__METHOD__, $params, function ($self, $params) {
            // do the actual work here
            return $result;
        });
    }
}
```

(The `_filter()` method is defined in `lithium\core\Object`, and basically
passes a local, static chain of filters to Lithium's `Filter` class for
execution. `applyFilter()` from the previous example statically adds a callback
under the named method to the chain.)

This solution is elegant — but I see some limitations:

- First and foremost, I'm not particularly fond of the filtering functionality
  being via static methods on a single class; it introduces a hard-coded, hidden
  dependency. This means you cannot provide alternate filtering functionality
  without extending the class *consuming* the filters, nor without either
  extending the base filters implementation should you wish to provide a
  compatible API (for instance, to introduce an implementation that understands
  priorities).

  Additionally, the easiest way to implement filtering in Lithium is by
  extending the `lithium\core\Object` class — I could find no examples
  elsewhere in the documentation that showed how you would compose the
  `Filters` implementation in your own objects. As such, the easiest way to
  compose filters is now via inheritance, which seems to be
  counter-productive to the whole rationale behind filtering, to my thinking.
- Second, the approach of making the body of the calling method a closure makes
  it difficult to create non-public helper methods. Inside the filter, you're no
  longer in the scope of the object, losing the semantics that tie the various
  metadata and functionality of the object together. (The Lithium docs provide
  illustrations of how to accomplish this, but they require extra work, and a
  keen understanding of how references work in PHP.)
- Third, it's sometimes useful to have access to the return results of *all* the
  filters (not just the last executed); you may want to aggregate them in some
  way, or branch logic based on the various returns.
- Fourth, it's sometimes useful to have multiple call points within the main
  code. As an example, for many caching strategies, you'd check first to see if
  you have a cache hit, and return immediatly if found; otherwise, you'd execute
  the code, and cache the result prior to returning it. This might be possible
  in Lithium with constructs like this:

  ```php
  Filters::add('SomeClass::doSomething', function ($method, $self, $params) {
      if (null !== ($content = cache_hit($params))) {
          return $content;
      }
      $content = Filters::next($method, $self, $params);
      return $content;
  });
  ```

  However, if you have several filters such as this, the order then becomes
  paramount, and that introduces new complexities.

  Another example would be with façade methods, where you may wish to introduce
  filters before and after each method call:

  ```php
  public function doSomeWorkflow($message)
  {
      $this->somePrivateMethod($message);
      $this->nextPrivateMethod($message);
      $this->lastPrivateMethod($message);
  }
  ```

  (I can already hear [Nate](http://nateabele.com/) saying "make those all
  filters!" or "filter each method!" — but that's the problem with simple
  examples - they can't always express the nuances of a use case.)

- Fifth, it's useful to be able to attach callbacks that are not aware of the
  chain. For instance, you may have code you've already written that works
  perfectly fine in a standalone situation — e.g., a logger — and you simply
  want to add it to the chain. In the Lithium paradigm, you'd need to
  [curry](http://en.wikipedia.org/wiki/Currying) the calls in, instead of simply
  using the existing method:

  ```php
  // This:
  SomeClass::applyFilter('doSomething', function ($self, $params, $chain) use ($log) {
      $log->info($params['message'];
      $chain->next($self, $params, $chain);
  });
  // VS:
  SomeClass::signals()->connect('doSomething', $log, 'info');
  ```

  Related to this, I personally dislike aggregating the filter parameters into a
  single associative array. I don't like having to test for the existence of
  parameters, and would much rather PHP tell me if I'm missing required
  parameters or if any fail typehints. That said, doing so provides a consistent
  API when filtering.

All in all, however, the approach Lithium provides is very good; it just doesn't
completely suit my tastes or use cases.

Signal Slots
------------

Interestingly, the capabilities I need are not far from what Lithium provides —
in fact, I'd argue that the Intercepting Filters of Lithium are actually
probably more akin to another pattern, [Signal Slots](http://en.wikipedia.org/wiki/Signals_and_slots).

With Signal Slots, your code emits *signals* (Lithium does this — it emits the
name of the method being called); any handler, or *slot* (*filters* in Lithium),
connected to the signal is then executed.

As such, you typically have some sort of signal "manager" object (the `Filters`
class in Lithium) that aggregates signals and attached slots; this manager is
then composed into the object emitting signals. For those of you familiar with
events in JavaScript or other event-driven languages, this should sound quite
familiar.

Such an approach looks like this:

```php
class Foo
{
    protected $signals;

    public function signals(SignalSlot $signals = null)
    {
        if (null === $signals) {
            // No argument? make sure we have a signal manager
            if (null === $this->signals) {
                $this->signals = new Signals(); // SignalSlot implementation
            }
        } else {
            // Compose in an instance of a signal manager
            $this->signals = $signals;
        }
        return $this->signals;
    }

    public function doSomething($with, $these, $args)
    {
        $this->signals()->emit('doSomething.pre', $this, $with, $these, $args);
        
        // do some work
        $this->signals()->emit('doSomething.during', $this, $with, $these, $args);

        // do some more work
        // This time, pass the result
        $this->signals()->emit('doSomething.post', $this, $result, $with, $these, $args);
        return $result;
    }
}

$f = new Foo();
$f->signals()->connect('doSomething.pre', $log, 'info');
$f->signals()->connect('doSomething.during', $validator, 'isValid');
$f->signals()->connect('doSomething.post', $indexer, 'index');
```

Basically, a `SignalSlot` provides an object in which signals and their attached
slots are aggregated. This allows having a single manager for multiple signals
(which is similar to how Lithium's `Filters` class works), while also providing
a way to emit multiple signals from a single procedure. Additionally, since it
is simply an object, you can compose it in to classes that may emit signals —
without requiring inheritance.

This is the basic approach of the [ZF2 SignalSlot implementation](https://github.com/zendframework/zf2/tree/master/library/Zend/SignalSlot),
as well as that found in [Symfony 2's Event Dispatcher](http://components.symfony-project.org/event-dispatcher/)
and [Zeta Components' SignalSlot component](http://incubator.apache.org/zetacomponents/documentation/trunk/SignalSlot/tutorial.html).

Both Symfony 2's Event Dispatcher and ZF2's `SignalSlot` component build in the
ability to short-circuit, Symfony via a `notifyUntil()` method, and ZF2 via an
`emitUntil` method. With ZF2, each time a signal is emitted, a
`ResponseCollection` is returned by the manager, containing an aggregate of all
slot responses. Calling `emitUntil()` will short-circuit execution of remaining
slots if a given slot returns a response that validates against given criteria;
at this point, the collection is marked as "stopped", and you can pull the
"last" response and return it:

```php
$responses = $this->signals()->emitUntil(function($response) {
    return ($response instanceof SpecificResultType);
}, 'doSomething.pre', $this, $with, $these, $args);
if ($responses->stopped()) {
    return $responses->last();
}
```

This introduces extra code in the method emitting the signals — but meets the
criteria that no given slot need be aware of the chain.

The Signal Slot approach actually supports paradigms similar to those
illustrated in Lithium. For instance, I can make my method body a slot:

```php
class Foo
{
    protected $handlers = array();

    // ... skip signals composition ...
    
    public function doSomething($with, $these, $args)
    {
        $params = compact('with', 'these', 'args');
        
        // connect() returns a signal handler (slot); store it so that we only
        // ever attach it once...
        if (isset($this->handlers[__FUNCTION__])) {
            $this->handlers[__FUNCTION__] = $this->signals()->connect(__FUNCTION__, function($self, $params) {
                // do the work here!
            });
        }
        
        // Emit the signal, and return the last result
        return $this->signals()->emit(__FUNCTION__, $this, $params)->last();
    }
}
```

Concerns
--------

Using Signal Slots and Intercepting Filters is not without its concerns, nor is
any given implementation perfect.

- Zeta Components does a fantastic job of handling signal slots. However, you
  cannot short-circuit execution, nor introspect return values. It does offer
  two features neither ZF2 nor Symfony 2 offer (at this time): the ability to
  *statically* connect slots to signals, allowing you to wire without having an
  existing instance, nor even caring what object might emit the signal; and the
  ability to add a priority to slots, which allows you to alter the execution
  order.
- Lithium does a nice job of providing good standards (signals are method names;
  parameters are predictable for all handlers), but at the price of some
  flexibility (static implementation with no interface for alternate
  implementations; no ability to re-use existing methods and functions with
  differing signatures without currying).
- Symfony 2 offers short-ciruiting and flexibility in callbacks, but requires
  that you create an event object to pass to the Event Dispatcher, making the
  usage slightly more verbose, and offers no standardization of signal naming.
- ZF2's `SignalSlots` offer similar benefits (and drawbacks) to Symfony 2's
  implementation, provides standardization of the signal manager response,
  allows registering classes that self-register with the signal handler, but
  lacks static wiring capabilities or prioritization.

On a more abstract level, signal slots and intercepting filters can lead to
difficulties in learning and mastering code that use them:

How are signals named?

How do you document the parameters available to slots/filters?

- How can those using IDEs discover available signals? and the arguments expected?

Where does the wiring occur?

- For instance, if any wiring is automated, this can potentially lead to more difficulty in debugging.
- If done manually, when, and where?

What happens if a slot doesn't receive arguments it needs, or cannot handle the arguments it receives?

In short, while they solve many problems, the implementations also introduce new
concerns — though this will be true of any extension system, in my experience.

Conclusions
-----------

I personally am a huge fan of Intercepting Filters and Signal Slots. I think
they can make code easier to extend, by providing a standard methodology for
introducing cross-cutting concerns without requiring class extension. They can
also make code quite expressive — sometimes at the cost of readability — by
introducing functional programming paradigms.

If you have not investigated these concepts or components before, I highly
recommend doing so; I think they play a fundamental role in the next generation
of PHP frameworks.

Caveats
-------

I am not an expert, nor well-versed, in all the frameworks listed here, and as
such, some of the information may be incorrect or incomplete. I am the author of
ZF2's current Signal Slot implementation, and am still working on improvements
to it.

Updates
-------

- **2011-01-10 11:35Z-05:00** Cal Evans found the original php|architect article
  I referenced, and I've revised some of the assessments based on re-reading it,
  as well as linked to the issue.
