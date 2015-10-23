---
id: 266-Using-the-ZF2-EventManager
author: matthew
title: 'Using the ZF2 EventManager'
draft: false
public: true
created: '2011-09-12T16:45:13-04:00'
updated: '2011-10-06T15:58:39-04:00'
tags:
    0: php
    2: 'zend framework'
    3: zf2
---
Earlier this year, I
[wrote about Aspects, Intercepting Filters, Signal Slots, and Events](/blog/251-Aspects,-Filters,-and-Signals,-Oh,-My!.html),
in order to compare these similar approaches to handling both asychronous
programming as well as handling cross-cutting application concerns in a cohesive
way.

I took the research I did for that article, and applied it to what was then a
"SignalSlot" implementation within Zend Framework 2, and refactored that work
into a new "EventManager" component. This article is intended to get you up and
running with it.

<!--- EXTENDED -->

Table of Contents
-----------------

- [Assumptions](#assumptions)
- [Terminology](#terminology)
- [Getting Started](#getting-started)
- [EventCollection vs EventManager](#eventcollection-vs-eventmanager)
- [Global Static Listeners](#global-static-listeners)
- [Listener Aggregates](#listener-aggregates)
- [Introspecting Results](#introspecting-results)
- [Short Circuiting Listener Execution](#short-circuiting-listener-execution)
- [Keeping it in Order](#keeping-it-in-order)
- [Custom Event Objects](#custom-event-objects)
- [Putting it Together: A Simple Caching Example](#putting-it-together-a-simple-caching-example)
- [Fin](#fin)
- [Updates](#updates)

Assumptions
-----------

You must have Zend Framework 2 installed either:

- From a development snapshot (the [ZF2 blog has the latest links](http://framework.zend.com/zf2/blog/entry/2011-08-30-Dev-status-update) at the time of writing), or
- From [cloning the ZF2 git repo](http://framework.zend.com/wiki/display/ZFDEV2/Zend+Framework+Git+Guide)

Terminology
-----------

- An **Event Manager** is an object that *aggregates* listeners for one or more named events, and which *triggers* events.
- A **Listener** is a callback that can react to an *event*.
- An **Event** is an action.

Typically, an *event* will be modeled as an object, containing metadata
surrounding when and how it was triggered — what the calling object was, what
parameters are available, etc. Events are also typically *named*, which can
allow a single *listener* to branch logic based on the current event (though
purists would argue you should never do this).

Getting Started
---------------

The minimal things necessary to get started are:

- An `EventManager` instance
- One or more listeners on one or more events
- A call to `trigger()` an event

So, here we go:

```php
use Zend\EventManager\EventManager;

$events = new EventManager();

$events->attach('do', function($e) {
    $event  = $e->getName();
    $params = $e->getParams();
    printf(
        'Handled event "%s", with parameters %s',
        $event,
        json_encode($params)
    );
});

$params = array('foo' => 'bar', 'baz' => 'bat');
$events->trigger('do', null, $params);
```

The above will output:

```
Handled event "do", with parameters {"foo":"bar","baz":"bat"}
```

Pretty simple!

> Note: throughout this post, I use closures as listeners. However, any valid
> PHP callback can be attached as a listeners — PHP function names, static class
> methods, object instance methods, or closures. I use closures within this post
> simply for illustration and simplicity.

But what's that `null`, second argument for?

Typically, you will compose an `EventManager` within a class, to allow
triggering actions within methods. The middle argument to `trigger()` is a
"context" or "target", and in the case described, would be the current object
instance. This gives event listeners access to the calling object, which can
often be useful.

```php
use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Example
{
    protected $events;
    
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
    }
    
    public function events()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager(
                array(__CLASS__, get_called_class())
            );
        }
        return $this->events;
    }
    
    public function do($foo, $baz)
    {
        $params = compact('foo', 'baz');
        $this->events()->trigger(__FUNCTION__, $this, $params);
    }

}

$example = new Example();

$example->events()->attach('do', function($e) {
    $event  = $e->getName();
    $target = get_class($e->getTarget()); // "Example"
    $params = $e->getParams();
    printf(
        'Handled event "%s" on target "%s", with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
});

$example->do('bar', 'bat');
```

The above is basically the same as the first example. The main difference is
that we're now using that middle argument in order to pass a context on to the
listeners. Our listener is now retrieving that (`$e->getTarget()`), and doing
something with it.

If you're reading this critically, you should have two questions:

- What is this `EventCollection` bit?
- What is that argument being passed to the `EventManager` constructor?

The answer to the first will lead us into the second.

EventCollection vs EventManager
-------------------------------

One principle we're trying to follow with ZF2 is the
[Liskov Substitution Principle](http://en.wikipedia.org/wiki/Liskov_substitution_principle).
One typical interpretation of this is that strong interfaces should be defined
for any class for which there could be a potential substitution, so that
consumers may use other implementations without worrying about variances in
internal behavior.

As such, we developed an interface, `EventCollection` that describes an object
capable of aggregating listeners for events, and triggering those events.
`EventManager` is the standard implementation we provide.

Global Static Listeners
-----------------------

One aspect that the `EventManager` implementation provides is an ability to
interface with a `StaticEventCollection`. This interface allows attaching
listeners not only on events, but on events emitted by specific contexts or
targets. The `EventManager`, when notifying listeners, will also pull listeners
for the event from the `StaticEventCollection` object it subscribes to, and
notify them.

How does this work, exactly?

At the application level, you grab an instance of `StaticEventManager`, and
start attaching events to it.

```php
use Zend\EventManager\StaticEventManager;

$events = StaticEventManager::getInstance();
$events->attach('Example', 'do', function($e) {
    $event  = $e->getName();
    $target = get_class($e->getTarget()); // "Example"
    $params = $e->getParams();
    printf(
        'Handled event "%s" on target "%s", with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
});
```

You'll notice it looks almost the same as the original example. The only
difference is there is a new argument at the beginning of the list, to which we
attached the name 'Example'. This code is basically saying, "Listen to the 'do'
event of the 'Example' target, and, when notified, execute this callback."

This is finally where the constructor argument of `EventManager` comes into
play. The constructor allows passing a string, or an array of strings, defining
the name of the context or target the given instance will be interested in. If
an array is given, then any listener on *any* of the targets given will be
notified. Listeners attached directly to the `EventManager` will be executed
before any attached statically.

So, getting back to our example, let's assume that the above static listener is
registered, and also that the `Example` class is defined as above. We can then
execute the following:

```php
$example = new Example();
$example->do('bar', 'bat');
```

and expect the following to be `echo`'d:

```
Handled event "do" on target "Example", with parameters {"foo":"bar","baz":"bat"}
```

Now, let's say we extended `Example` as follows:

```php
    class SubExample extends Example
    {
    }
```

One interesting aspect of our `EventManager` construction is that we defined it
to listen both on `__CLASS__` and `get_called_class()`. This means that calling
`do()` on our `SubExample` class would also trigger the event we attached
statically! It also means that, if desired, we could attach to specifically
`SubExample`, and listeners on simply `Example` would not be triggered.

Finally, the names used as contexts or targets need not be class names; they can
be some name that only has meaning in your application if desired. As an
example, you could have a set of classes that respond to "log" or "cache" — and
listeners on these would be notified by any of them.

At any point, if you do not want the `EventManager` attached to a class to
notify statically attached listeners, you can simply pass a `null` value to the
`setStaticConnections()` method:

```php
$events->setStaticConnections(null);
```

and they will be ignored. If at any point, you want to enable them again, pass
the `StaticEventManager` instance:

```php
$events->setStaticConnections(StaticEventManager::getInstance());
```

Listener Aggregates
-------------------

Oftentimes, you may want a single class to listen to multiple events, attaching
one or more instance methods as listeners. To make this paradigm easy, you can
simply implement the `HandlerAggregate` interface. This interface defines two
methods, `attach(EventCollection $events)` and `detach(EventCollection $events)`.
Basically, you pass an `EventManager` instance to one and/or the other, and then
it's up to the implementing class to determine what to do.

As an example:

```php
use Zend\EventManager\Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\HandlerAggregate,
    Zend\Log\Logger;

class LogEvents implements HandlerAggregate
{
    protected $handlers = array();
    protected $log;

    public function __construct(Logger $log)
    {
        $this->log = $log;
    }

    public function attach(EventCollection $events)
    {
        $this->handlers[] = $events->attach('do', array($this, 'log'));
        $this->handlers[] = $events->attach('doSomethingElse', array($this, 'log'));
    }
    
    public function detach(EventCollection $events)
    {
        foreach ($this->handlers as $key => $handler) {
            $events->detach($handler);
            unset($this->handlers[$key];
        }
        $this->handlers = array();
    }

    public function log(Event $e)
    {
        $event  = $e->getName();
        $params = $e->getParams();
        $log->info(sprintf('%s: %s', $event, json_encode($params)));
    }
}
```

You would then attach it as follows:

```php
$doLog = new LogEvents($logger);
$events->attachAggregate($doLog);
```

and any events it handles would then be notified when they are triggered. This
allows you to have stateful event listeners.

You'll notice the `detach()` method implementation. Just like `attach()`, it
accepts an `EventManager`, and then calls detach for each handler it has
aggregated. This is possible because `EventManager::attach()` returns an object
representing the listener — which we've aggregated within our aggregate's
`attach()` method previously.

Introspecting Results
---------------------

Sometimes you'll want to know what your listeners returned. One thing to
remember is that you may have multiple listeners on the same event; the
interface for results must be consistent regardless of the number of listeners.

The `EventManager` implementation by default returns a `ResponseCollection`
object. This class extends PHP's `SplStack`, allowing you to loop through
responses in reverse order (since the last one executed is likely the one you're
most interested in). It also implements the following methods:

- `first()` will retrieve the first result received
- `last()` will retrieve the last result received
- `contains($value)` allows you to test all values to see if a given one was received, and returns simply a boolean true if found, and false if not.

Typically, you should not worry about the return values from events, as the
object triggering the event shouldn't really have much insight into what
listeners are attached. However, sometimes you may want to short-circuit
execution if interesting results are obtained.

Short Circuiting Listener Execution
-----------------------------------

You may want to short-ciruit execution if a particular result is obtained, or if
a listener determines that something is wrong, or that it can return something
quicker than the target.

As examples, one rationale for adding an `EventManager` is as a caching
mechanism. You can trigger one event early in the method, returning if a cache
is found, and trigger another event late in the method, seeding the cache.

The `EventManager` component offers two ways to handle this. The first is to
pass a callback as the last argument to `trigger()`; callback; if that callback
returns a boolean true, execution is halted.

Here's an example:

```php
public function someExpensiveCall($criteria1, $criteria2)
{
    $params  = compact('criteria1', 'criteria2');
    $results = $this->events()->trigger(__FUNCTION__, $this, $params, function ($r) {
        return ($r instanceof SomeResultClass);
    });
    if ($results->stopped()) {
        return $results->last();
    }
    
    // ... do some work ...
}
```

With this paradigm, we know that the likely reason of execution halting is due
to the last result meeting the test callback criteria; as such, we simply return
that last result.

The other way to halt execution is within a listener, acting on the `Event`
object it receives. In this case, the listener calls `stopPropagation(true)`,
and the `EventManager` will then return without notifying any additional
listeners.

```php
$events->attach('do', function ($e) {
    $e->stopPropagation();
    return new SomeResultClass();
});
```

This, of course, raises some ambiguity when using the `trigger` paradigm, as you
can no longer be certain that the last result meets the criteria it's searching
on. As such, my recommendation is you use one approach or the other.

Keeping it in Order
-------------------

On occasion, you may be concerned about the order in which listeners execute. As
an example, you may want to do any logging early, to ensure that if
short-circuiting occurs, you've logged; or if implementing a cache, you may want
to return early if a cache hit is found, and execute late when saving to a
cache.

Each of `EventManager::attach()` and `StaticEventManager::attach()` accept one
additional argument, a *priority*. By default, if this is omitted, listeners get
a priority of 1, and are executed in the order in which they are attached. If
you provide a priority value, you can influence order of execution. Higher
priority values execute earlier, while lower (negative) values execute later.

To borrow an example from earlier:

```php
$priority = 100;
$events->attach('Example', 'do', function($e) {
    $event  = $e->getName();
    $target = get_class($e->getTarget()); // "Example"
    $params = $e->getParams();
    printf(
        'Handled event "%s" on target "%s", with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
}, $priority);
```

This would execute with *high priority*, meaning it would execute early. If we
changed `$priority` to `-100`, it would execute with *low priority*, executing
late.

While you can't necessarily know all the listeners attached, chances are you can
make adequate guesses when necessary in order to set appropriate priority
values. My advice is to avoid setting a priority value unless absolutely
necessary.

Custom Event Objects
--------------------

Hopefully some of you have been wondering, "where and when is the Event object
created"? In all of the examples above, it's created based on the arguments
passed to `trigger()` — the event name, target, and parameters. Sometimes,
however, you may want greater control over the object, however.

As an example, as we've been developing the ZF2 MVC layer, we've been adding
event awareness to several of the core MVC components. One thing that looks like
a code smell is when you have code like this:

```php
$routeMatch = $e->getParam('route-match', false);
if (!$routeMatch) {
    // Oh noes! we cannot do our work! whatever shall we do?!?!?!
}
```

The problems with this are several. First, relying on string keys is going to
very quickly run into problems — typos when setting or retrieving the argument
can lead to hard to debug situations. Second, we now have a documentation issue;
how do we document expected arguments? how do we document what we're shoving
into the event. Third, as a side effect, we can't use IDE or editor hinting
support — string keys give these tools nothing to work with.

Similarly, we found ourselves writing some wierd hacks around how we represent a
computational result of a method when triggering an event. As an example:

```php
// in the method:
$params['__RESULT'] = $computedResult;
$events->trigger(__FUNCTION__ . '.post', $this, $params);

// in the listener:
$result = $e->getParam('__RESULT__');
if (!$result) {
    // Oh noes! we cannot do our work! whatever shall we do?!?!?!
}
```

Sure, that key may be unique, but it suffers from a lot of the same issues.

So, the solution is to create custom events. As an example, we have a custom
"MvcEvent" in the ZF2 MVC layer. This event composes a router, route match
object, request and response objects, and also a result. We end up with code
like this in our listeners:

```php
$response = $e->getResponse();
$result   = $e->getResult();
if (is_string($result)) {
    $content = $view->render('layout.phtml', array('content' => $result));
    $response->setContent($content);
}
```

But how do we use this custom event? Simple: `trigger()` can accept an event
object instead of any of the event name, target, or params arguments.

```php
$event = new CustomEvent();
$event->setSomeKey($value);

// Injected with event name and target:
$events->trigger('foo', $this, $event);

// Injected with event name:
$event->setTarget($this);
$events->trigger('foo', $event);

// Fully encapsulates all necessary properties:
$event->setName('foo');
$event->setTarget($this);
$events->trigger($event);

// Passing a callback following the event object works for 
// short-circuiting, too.
$results = $events->trigger('foo', $this, $event, $callback);
```

This is a really powerful technique for domain-specific event systems, and
definitely worth experimenting with.

Putting it Together: A Simple Caching Example
---------------------------------------------

In the previous section, I indicated that short-circuiting is a way to
potentially implement a caching solution. Let's create a full example.

First, let's define a method that could use caching. You'll note that in most of
the examples, I've used `__FUNCTION__` as the event name; this is a good
practice, as it makes it simple to create a macro for triggering events, as well
as helps to keep event names unique (as they're usually within the context of
the triggering class). However, in the case of a caching example, this would
lead to identical events being triggered. As such, I recommend postfixing the
event name with semantic names: "do.pre", "do.post", "do.error", etc. I'll use
that convention in this example.

Additionally, you'll notice that the `$params` I pass to the event is usually
the list of parameters passed to the method. This is because those are often not
stored in the object, and also to ensure the listeners have the exact same
context as the calling method. But it raises an interesting problem in this
example: what name do we give the *result* of the method? I've standardized on
`__RESULT__`, as double-underscored variables are typically reserved for the
sytem. If you have better suggestions, I'd love to hear them!

Here's what the method will look like:

```php
public function someExpensiveCall($criteria1, $criteria2)
{
    $params  = compact('criteria1', 'criteria2');
    $results = $this->events()->trigger(__FUNCTION__ . '.pre', $this, $params, function ($r) {
        return ($r instanceof SomeResultClass);
    });
    if ($results->stopped()) {
        return $results->last();
    }
    
    // ... do some work ...
    
    $params['__RESULT__'] = $calculatedResult;
    $this->events()->trigger(__FUNCTION__ . '.post', $this, $params);
    return $calculatedResult;
}
```

Now, to provide some caching listeners. We'll need to attach to each of the
'someExpensiveCall.pre' and 'someExpensiveCall.post' methods. In the former
case, if a cache hit is detected, we return it, and move on. In the latter, we
store the value in the cache.

We'll assume `$cache` is defined, and follows the paradigms of `Zend_Cache`.
We'll want to return *early* if a hit is detected, and execute *late* when
saving a cache (in case the result is modified by another listener). As such,
we'll set the 'someExpensiveCall.pre' listener to execute with priority `100`,
and the 'someExpensiveCall.post' listener to execute with priority `-100`.

```php
$events->attach('someExpensiveCall.pre', function($e) use ($cache) {
    $params = $e->getParams();
    $key    = md5(json_encode($params));
    $hit    = $cache->load($key);
    return $hit;
}, 100);

$events->attach('someExpensiveCall.post', function($e) use ($cache) {
    $params = $e->getParams();
    $result = $params['__RESULT__'];
    unset($params['__RESULT__']);
    $key    = md5(json_encode($params));
    $cache->save($result, $key);
}, -100);
```

> Note: the above could have been done within a `HandlerAggregate`, which would
> have allowed keeping the `$cache` instance as a stateful property, instead of
> importing it into closures.

Sure, we could probably simply add caching to the object itself - but this
approach allows the same handlers to be attached to multiple events, or to
attach multiple listeners to the same events (e.g. an argument validator, a
logger *and* a cache manager). The point is that if you design your object with
events in mind, you can easily make it more flexible and extensible, without
requiring developers to *actually* extend it — they can simply attach listeners.

## Fin

The `EventManager` is a powerful new addition to Zend Framework. Already, it's
being used with the new MVC prototype to empower some constructs that were
difficult to accomplish well in the version 1.X series — as an example, I was
able to prototype a `ViewRenderer` replacement in a handful of lines of code, in
a way that properly accomplishes the separation of concerns one expects from
MVC. I anticipate we'll be using it much, much more often as version 2 matures.

There are certainly some rough edges — the boiler-plate code for
short-circuiting is verbose, and we will likely want to add capabilities such as
event globbing — but the foundation is solid and mature at this point in time.
Experiment with it, and see what you can accomplish!

Updates
=======

- **2011-10-06**: Removed references to `triggerUntil()`, as that functionality
  is now incorporated into `trigger()`. Added section on
  [Custom Event Objects](#custom-event-obejcts).
