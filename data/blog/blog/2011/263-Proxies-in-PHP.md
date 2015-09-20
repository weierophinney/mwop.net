---
id: 263-Proxies-in-PHP
author: matthew
title: 'Proxies in PHP'
draft: false
public: true
created: '2011-07-05T14:05:00-04:00'
updated: '2011-07-07T09:51:35-04:00'
tags:
    - php
---
A number of programming design patterns look very similar. One of these is the
*Proxy* pattern, which, at first glance, can look like a number of others:
*Decorator*, *Flyweight*, even plain old object extension. However, it has its
own niche, and it can provide some incredible flexibility for a number of
programming scenarios.

<!--- EXTENDED -->

Of the other patterns mentioned, the one closest to the *Proxy* is the
*Decorator*. In the case of a *Decorator*, the focus is on *adding*
functionality to an existing object — for instance, adding methods, processing
input before delegating to the target object, or filtering the return of a
method from a target object.

The *Proxy* differentiates itself as it typically acts as a stand-in for an
existing object. Classically, the Proxy object has three typical use cases:

- Acting as a placeholder for "expensive to create" objects, lazy-loading them only on first access (this is similar to the *Flyweight* pattern).
- Local object representation of remote system processes.
- Consuming and controlling access to another object.

Typically, I've considered *Proxy* objects only in light of the first two
points. Recently, however, [Ralph](http://ralphschindler.com/) pointed me to the
last definition, and showed how powerful it can be.

Accessing the Invisible
-----------------------

### The Problem

Often we end up writing both setters and getters for class metadata that we
never truly intend to expose; we're more interested in how the object consumes
that information via other methods. As an example, we may want to write a value
object that accumulates data, and then later do something with that value
object. The getters really have no purpose outside the primary use case — even
in testing, we're really mostly interested in what the object *does* with those
values, not that it's storing them. Why waste time writing methods that will,
quite simply, never be used in practice?

In this scenario, the developer works directly with these objects, populating
and manipulating them, passing them around to other objects, etc., but never
introspecting them.

Later, we may want to re-use the same, fully-configured object, but introspect
it in order to process it in different ways. Alternately, we may want an empty
value object, and use a proxy in order to populate it before returning it to the
user (this is in fact one way in which Doctrine2 currently works with entities).
So, how do we go about this?

The first option seems obvious: extend the original class. However, this fails
one of the criteria: we want to *re-use* an existing object instance, and work
with an instance of the original class.

The next common option would be to use *Decoration*. However, decoration only
gives us access to public members — you're simply passing the original object
in, warts and all, so the same visibility rules apply.

So, how do we access those non-public members?

### The Solution

Consider the common conception of how visibility works in PHP (this is how *I*
thought it worked, too, until recently):

```php
class SomeObject
{
    protected $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    protected function doSomething()
    {
        return $this->message;
    }
}

$o = new SomeObject('foo bar');
```

In the above example, how would we call `doSomething()`? or access the
`$message` property? We couldn't.

Enter the Proxy pattern.

Traditional proxies have you (a) implement the same interface as the proxied
class, and (b) pass the proxied class to the constructor of the proxy or (c)
have the proxy manage the proxied class instance entirely. In the case of PHP,
since you cannot cast to an interface, you miss out on a lot of what Java and
.NET can offer. So, we have to go a different route that looks convoluted at
first, but once you understand a key point about PHP, it makes sense. That
point?

> PHP's visibility applies at the class-level, not instance-level.

Here we go:

```php
class Proxy extends SomeObject 
{
    protected $proxied;

    public function __construct(SomeObject $o)
    {
        $this->proxied = $o;
    }
    
    public function doSomething()
    {
        return ucwords($this->proxied->message);
    }
}

$o = new SomeObject('foo bar');
$p = new Proxy($o);
$p->doSomething();
```

My first guess when looking at this is that it wouldn't work — the `$proxied`
property refers to an instance of `SomeObject`, and `SomeObject`'s `$message`
property is protected — `$this->proxied->message` should not be accessible. But
let's go back to my earlier assertion: visibility applies to the *class*, not
*instances*. In our case, `Proxy` is extending `SomeObject`, so it shares
visibility. This means that as it operates on other instances deriving from
`SomeObject`, it has access to its members.

> One note: Because we're extending a class, normal visibility rules still
> apply: you cannot access *private* members from the class being extended. This
> is another reason why I continue to assert that frameworks and libraries
> should only in very exceptional circumstances declare private visibility.

Gotchas
-------

- You need to override any method that affects your workflow. As an example,
  let's consider the following class definition:

  ```php
  class SomeObject 
  {
      public function foo()
      {
          $value = $this->bar() . $this->baz();
          return $value;
      }
      
      protected function bar()
      {
          return __CLASS__;
      }
      
      protected function baz()
      {
          return __FUNCTION__; 
      }
  }
  ```

  If you wanted to override `bar()`, but have it continue to aggregate its
  return value from the `foo()` method, you'd need to override *both* these
  methods as follows:

  ```php
  class Proxy extends SomeObject
  {
      protected $proxy;
      
      public function __construct(SomeObject $o)
      {
          $this->proxy = $o;
      }

      public function foo()
      {
          $value = $this->bar() . $this->proxy->baz();
          return $value;
      }
      
      protected function bar()
      {
          return __FUNCTION__;
      }
  }
  ```

- Copy over any properties you may be accessing in your overridden methods, or
  accessed in methods you may call.

  As an example, consider a class you're proxying where you want want to call a
  method that, in the proxied object, refers to an instance property.

  ```php
  class Adapter
  {
      protected $name;

      public function __construct($name)
      {
          $this->name = $name;
      }

      public function getName()
      {
          return $this->name;
      }
  }

  class SomeObject
  {
      protected $adapter;

      public function __construct()
      {
          $this->adapter = new Adapter(__METHOD__);
      }

      public function execute()
      {
          return $this->adapter->getName();
      }
  } 
  ```

  If I want to proxy `SomeObject` and then call the `execute()` method, I might
  try the following:

  ```php
  class Proxy extends SomeObject
  {
      protected $proxy;
      
      public function __construct(SomeObject $o)
      {
          $this->proxy = $o;
      }
  }

  $o = new SomeObject();
  $p = new Proxy($o);
  echo $p->execute();
  ```

  Try running that code. I'll wait.

  If you have error reporting properly configured and `display_errors` enabled,
  you'll have received a fatal error indicating something about being unable
  to call a member function on a non-object.

  What has happened is that the call to `execute()` is now in the scope of the
  `Proxy` object… which has no defined `$adapter` property.

  There are two ways around this. First, define the method in your proxy object:

  ```php
  class Proxy extends SomeObject
  {
      protected $proxy;
      
      public function __construct(SomeObject $o)
      {
          $this->proxy = $o;
      }

      public function execute()
      {
          return $this->proxy->adapter->getName(); 
      }
  }

  $o = new SomeObject();
  $p = new Proxy($o);
  echo $p->execute();
  ```

  Sure, it works… but do you want to do this for every single method in your
  proxied class that you may call?

  The better way is to assign any properties from the proxied object directly to
  the proxy object:

  ```php
  class Proxy extends SomeObject
  {
      protected $proxy;
      
      public function __construct(SomeObject $o)
      {
          $this->proxy = $o;
          
          // Assign the adapter instance to this object as well...
          $this->adapter = $o->adapter;
      }
  }

  $o = new SomeObject();
  $p = new Proxy($o);
  echo $p->execute();
  ```

  Note, you don't need to define those properties; they're defined in
  `SomeObject` already, and we're still extending `SomeObject`. As such, now
  that we've assigned the property, the call just works. This is more succinct,
  and can help save some keystrokes later when you override more methods.

Summary
-------

The Proxy pattern is a fantastic way to re-use *object instances* to which you
want visibility into protected attributes or methods, and particularly when you
may not have control over the object lifecycle of the various objects it
composes.

Some good uses cases include unit testing (proxies deliver a nice way to test
internal state of an object without needing to expose that state), object
persistence strategies (ala Doctrine 2), and much more.

Resources
---------

There's a ton of information on the Proxy pattern on the intarwebs, but very
little that displays the visibility aspects of it in relation to PHP. One good
resource, however is the Doctrine2 project, which
[uses proxy objects for a variety of purposes](http://www.doctrine-project.org/docs/orm/2.0/en/reference/configuration.html#proxy-objects).

We're using it in Zend Framework 2's Dependency Injection system for
[generating service locator objects](https://github.com/zendframework/zf2/blob/master/library/Zend/Di/ServiceLocator/DependencyInjectorProxy.php)
from a configured `DependencyInjector` instance as well.

My main takeaway from learning about the pattern was that it enables me a way to
control access to and/or manipulate internal processes of object members without
requiring consumers of the code to change practices; my code can consume
existing objects to do the work.

What uses have *you* found for proxies? What things could proxies enable for you?
