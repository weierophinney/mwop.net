---
id: 254-Why-PHP-Namespaces-Matter
author: matthew
title: 'Why PHP Namespaces Matter'
draft: false
public: true
created: '2011-02-04T09:30:00-05:00'
updated: '2011-02-10T16:29:26-05:00'
tags:
    - php
---
You've heard about PHP namespaces by now. Most likely, you've heard about — and
likely participated in — the [bikeshedding](http://en.wikipedia.org/wiki/Bikeshedding)
surrounding the selection of the namespace separator.

Regardless of your thoughts on the namespace separator, or how namespaces may
or may not work in other languages, I submit to you several reasons for why I
think namespaces in PHP are a positive addition to the language.

<!--- EXTENDED -->

Code Organization
-----------------

Prior to PHP 5.3, we've had a number of standards surrounding how to name
classes and where to put the class files in the filesystem. These range from
completely arbitrary, to conventions-based (`abcSomeClass` in
`library/abc/some`), to PEAR-like (1:1 correlation between class name and
filesystem location).

While namespaces do not enforce any specific paradigm, they lend themselves to
the PEAR-style conventions. Why?

Consider:

```php
namespace my\Component;

class Gateway {}
```

Where would you expect to find this file? Did you say "in
`my/Component/Gateway.php`"? My guess is that greater than 90% of my readers
did. Why? ***Because the namespace separator reminds us of the directory
separator.*** Plain and simple. This convention just makes sense.

As such, namespaces lend themselves to efficient and simple naming conventions.

Interfaces
----------

Interfaces are, to my thinking, often underused in PHP. Many will argue, "hey,
they don't do anything, require more files to be loaded, and I can typehint
just as easily on an abstract or concrete class." These are all true. However,
interfaces provide us with a simple representation of the contracts we define
for our applications, and provide us with the blueprints we need for extending
and modifying our systems.

One thing I struggled with using pre-PHP 5.3 code was how to name interfaces.
Since we didn't have true namespaces, we (PHP developers, that is) often used
names such as `My_Component_Adapter_Interface`. Considering that this
becomes `My\Component\Adapter\Interface` when doing a literal 1:1 transition
from pseudo-namespaces to PHP 5.3 namespaces, I encountered several issues:

- First, due to how the PHP lexer works, you get an `E_FATAL` due to a declaration of `interface Interface`.
- Second, the structure now feels odd: we're ultimately describing an adapter, but why would we put that a level deeper in the namespace hierarchy?

An organization I've found that works looks like the following:

```
library/
|-- mwop/
|   |-- Component/
|   |   |-- ClassConsumingAdapters.php
|   |   |-- Adapter.php
|   |   |-- Adapter/
|   |   |   |-- AbstractAdapter.php
|   |   |   |-- SomeConcreteAdapter.php
```

In the above, we are declaring a `mwop\Component` namespace. In that namespace
live a concrete class that consumes adapters, and the actual adapter interface
itself — named simply for what it is, an `Adapter`. This puts the adapter
definition at the same level where it is consumed.

Concrete adapters are then in the subnamespace `mwop\Component\Adapter`. We put
a base implementation in the `AbstractAdapter` class, and concrete adapters
typically extend this. The abstract adapter declaration looks like the
following:

```php
namespace mwop\Component\Adapter;

use mwop\Component\Adapter;

abstract class AbstractAdapter implements Adapter
{ ... }
```

This looked odd and like it wouldn't work when I first tried it, but it is
indeed legal syntax. What I particularly like about it is that it's clear what
the class *is* (it's an *adapter*), and also clear that I'll find sibling
classes within this namespace.

In my `ClassConsumingAdapters`, I only make reference to `Adapter`s:

```php
namespace mwop\Component;

class ClassConsumingAdapters
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function doSomething()
    {
        $data = $this->adapter->someMethodCall();
        // do some work
        return $data;
    }
}
```

I'm simply worried about having an adapter, and consuming it, not the specific
implementation — which is what programming with interfaces is about. Having the
interface at the same level makes the code eminently readable and
comprehensible.

Readability
-----------

One argument for having namespaces in the first place was code readability.
Admittedly, this was mainly coming from those of us in the PEAR camp, where we
were trying to organize code semantically into hierarchies and dependencies,
and ending up with long names like `Foo_Component_Decorator_View_Helper` — when
what we really meant was "a helper object". However, due to the use of
pseudo-namespaces to organize our code, and the fact that we could only utilize
class names, we were stuck with verbosity.

With namespaces, we have two tools at our disposal.

First, namespaces themselves. If we're writing new code, we can create
namespaces, and immediately all code inside our namespace is available, without
needing to prefix at all. An example of that is above, where the
`ClassConsumingAdapters` simply references `Adapter` — since they're in the
same namespace, no prefixing is necessary.

Our second tool is the ability to import and alias. As an example, let's
consider this:

```
library/
|-- mwop/
|   `-- Component/
|      |-- ClassConsumingAdapters.php
|      |-- Adapter.php
|      `-- Adapter/
|          |-- AbstractAdapter.php
|          `-- SomeConcreteAdapter.php
|-- Zend/
|   `-- EventManager/
|      |-- EventCollection.php
|      |-- EventManager.php
|      `-- StaticEventManager.php
```

Let's say that `ClassConsumingAdapters` wants to utilize the new
`Zend\EventManager` component. There are several ways this can be done. First,
it could simply use global resolution:

```php
namespace mwop\Component;

class ClassConsumingAdapters
{
    protected $events;

    public function events(\Zend\EventManager\EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new \Zend\EventManager\EventManager(__CLASS__);
        }
        return $this->events;
    }
}
```

That's pretty ugly, and arguably worse than pre-namespace code. So, let's try
*importing* some classes and interfaces. In PHP, we use the `use` keyword to
import classes into the current scope:

```php
namespace mwop\Component;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class ClassConsumingAdapters
{
    protected $events;

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(__CLASS__);
        }
        return $this->events;
    }
}
```

That's a bit easier to read! We now have references that better indicate the
purpose of the classes we're using, which makes comprehension of what we're
doing easier.

The third option is to *alias*. Aliasing is something you do when importing a
class; at the time you import, you indicate an alternate name by which you want
to refer to the class or interface. An illustration will help:

```php
namespace mwop\Component;

use Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager;

class ClassConsumingAdapters
{
    protected $events;

    public function events(Events $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(__CLASS__);
        }
        return $this->events;
    }
}
```

In the above example, we're *aliasing* `Zend\EventManager\EventCollection` to
simply `Events` (plural often connotes a collection).

Now that we know about aliasing, here's a tip: you don't *need* to rewrite all
that nice, clean, pre-PHP 5.3 library code to make use of namespaces! You can
simply use aliasing in your consumer code:

```
namespace Application;

use Zend_Controller_Action as ActionController;

class FooController extends ActionController
{
}
```

(I've been using the above trick in my presentations since last spring, as it
often helps make the code samples more readable!)

Identifying Dependencies
------------------------

Now that you know about importing and aliasing, there's another point to bring
up: importing helps you make dependencies explicit.

Declaring an import statement does not immediately load a class — it simply
hints to the PHP interpreter as to how to understand certain symbols when it
encounters them.

In fact, you can import and alias not just classes and interfaces, but
namespaces themselves — though when importing namespaces, you then prefix
classes under that namespace:

```php
namespace Application;

use Foo\Exception;

// ...
// Foo\Exception\InvalidArgumentException:
throw Exception\InvalidArgumentException(); 
```

A side effect of importing is that you're documenting at a code level your
dependencies on components from other namespaces. This allows you to do things
such as use static analysis tools to identify dependencies. As an example, I've
[created a scanDeps tool](https://github.com/weierophinney/zf-examples/tree/master/zf-utils)
that will analyze a tree of PHP files for import statements, and create a list
of unique components referenced.

This sort of automation is invaluable; it can help you identify what tests you
may want to run when changing code in a given component, allow you to create
PEAR packages of your code that reference the appropriate dependencies, and
more.

Conclusion
----------

Organization. Readability. Dependency tracking. All of these are worthy goals
in and of themselves, and together, they're impressive. And all from one
feature: namespaces.

Sure, we can all debate the namespace separator. At the end of the day,
however, the point is: what do namespaces give me, regardless of the syntax?
Hopefully, my arguments have convinced you of their general utility to PHP
development.

If you haven't played with namespaces yet, install PHP 5.3 if you haven't and
start experimenting — and let me know what usage patterns *you* find!
