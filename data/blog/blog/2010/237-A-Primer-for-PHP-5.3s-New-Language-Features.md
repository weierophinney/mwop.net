---
id: 237-A-Primer-for-PHP-5.3s-New-Language-Features
author: matthew
title: 'A Primer for PHP 5.3''s New Language Features'
draft: false
public: true
created: '2010-04-06T11:10:23-04:00'
updated: '2010-04-14T12:14:26-04:00'
tags:
    0: php
    2: 'zend framework'
---
For the past month, I've been immersed in PHP 5.3 as I and my team have started
work on [Zend Framework](http://framework.zend.com/) 2.0. PHP 5.3 offers a slew
of new language features, many of which were developed to assist framework and
library developers. Most of the time, these features are straight-forward, and
you can simply use them; in other cases, however, we've run into behaviors that
were unexpected. This post will detail several of these, so *you* either don't
run into the same issues — or can capitalize on some of our discoveries.

<!--- EXTENDED -->

Closures, Anonymous Functions, and Lambdas, oh my!
--------------------------------------------------

Briefly, these are all synonyms (with slight contextual differences) for a single PHP construct, the [anonymous function](http://php.net/functions.anonymous):

```php
$callback = function ($param) {
    // do something
};
```

You can assign an anonymous function to a variable, or pass it in-line as a
callback argument to a function or method call. The construct makes for some
really flexible designs, and is particularly useful with the various array
functions and with `preg_replace_callback()`. If you see any `create_function`
constructs in your codebase, go and replace them immediately with anonymous
functions; not only will they be easier to read (escaping code content in
`create_function()` was always a pain), but they'll be much faster, and also
benefit from opcode caching if available.

We discovered one interesting issue, however. PHP does not like serializing
closures; doing so raises an exception ("Serialization of 'Closure' is not
allowed"). This has a number of implications:

- If you need to alter the SPL autoloader stack, be careful about using
  closures with it. As an example, our testbed was caching the autoloaders by
  storing the return value of `spl_autoload_functions()`, and then resetting it
  during testing. Unfortunately, if you register a closure with
  `spl_autoload_register`, you may get an error when you do this. *(Note: this
  appears to be fixed with 5.3.2 and up.)*
- If you are serializing classes that have properties that reference closures,
  you will need to add some logic to `__sleep()` and `__wakeup()` to ensure
  those properties are not serialized, and to recreate them on deserialization.

Additionally, even though internally anonymous functions are represented via
the class `Closure`, you cannot typehint on that class; the only way to test if
a variable is a closure is to use `is_callable()`.

Invokables
----------

One fun new feature of PHP 5 is the magic method `__invoke()`, which allows you
to call an object as if it were a function:

```php
class Greeting
{
    public function __invoke($name)
    {
        return "Hello, $name";
    }
}

$greeting = new Greeting;
echo $greeting('world'); // "Hello, world"
```

Unlike other magic methods, it actually is *faster* than the alternatives. When
simply returning a value, it's 25% faster than calling a method on the same
object; when used with `call_user_func_array()`, it's 30% faster than using a
normal, array-style callback (e.g., `array($o, 'greet')` — even when it's
proxying to another method!

So, sounds like a great new feature, right? Yes… but there are some things you
should know.

- Like closures, you cannot typehint explicitly for `__invoke()`; you have to
  either use `is_callable()` or create an interface defining it:

  ```php
  interface Filter
  {
      public function filter($value);
  }

  interface CallableFilter
  {
      public function __invoke($value);
  }

  class IntFilter implements Filter, CallableFilter
  {
      public function filter($value)
      {
          return (int) $value;
      }

      public function __invoke($value)
      {
          return $this->filter($value);
      }
  }

  $filter = new IntFilter;
  if ($filter instanceof CallableFilter) {
      // matches
  }
  ```

- Be careful about using objects implementing `__invoke()` as object
  properties; they don't do what you expect. For instance, consider the
  following:

  ```php
  class Foo
  {
      public function __invoke()
      {
          return 'foo';
      }
  }

  class Bar
  {
      public $foo;

      public function __construct()
      {
          $this->foo = new Foo;
      }
  }

  $bar = new Bar;
  echo $bar->foo();
  ```

  You might expect this to echo "foo" — but it won't. Instead, it'll raise an
  `E_FATAL`, claiming "Call to undefined method Bar::foo()". If you want to
  execute the property, you have to assign it to a temporary variable first, or
  explicitly call `__invoke()`:

  ```php
  $foo = $bar->foo;
  echo $foo();

  // or:

  $bar->foo->__invoke();
  ```

Namespacing for fun and profit
------------------------------

Please put aside your opinions on the choice of namespace separator in PHP;
it's water under the bridge at this point, and there were good technical
reasons for the choice. We have an implementation, so let's use it.

First off, you declare your namespace at the top of your file:

```php
namespace Zend\Filter;
```

Or you can have several namespaces in the same file, as long as you have no
loose code:

```php
namespace Zend\Filter;
// some namespaced code here...

namespace Zend\Validator;
// some namespaced code here...
```

While the above is valid, the PHP manual recommends using braces if you're
using multiple namespaces in a single file:

```php
namespace Zend\Filter 
{
    // some namespaced code here...
}

namespace Zend\Validator 
{
    // some namespaced code here...
}
```

You can *import* code from other namespaces using the `use` construct. This
construct also allows you to *alias* the namespace (or class, constant, or
function within the namespace) using the `as` modifier:

```php
namespace Foo;
use Zend\Filter;
use Zend\Validator\Int as IntValidator;

$validator = new IntValidator;  // Zend\Validator\Int
if ($validator->isValid($foo) {
    $filter = new Filter\Int(); // Zend\Filter\Int
    echo $filter($foo);
}
```

Some quick rules about namespaces:

- *Fully qualified namespaces* (FQN) begin with a namespace separator (`\\`).
  Classes, functions, constants, and static members referenced using a FQN will
  always resolve.
- The namespace declaration is always considered fully qualified, and should
  *not* be prefixed with a namespace separator.
- Namespaces referenced in a `use` statement are always considered fully
  qualfied; you *can* prefix with a namespace separator, but it's not
  necessary.
- When referring to namespaced classes within a namespace, be aware of the
  origin: if you don't fully qualify the namespace, the assumptions will be:

  - A sub-namespace of the current namespace
  - A reference to one of the aliases defined when importing

  For example, consider the following code:

  ```php
  namespace Foo;
  use Zend\Filter; // imports are always considered FQN

  $foo       = new Bar\Baz;             // actual; Foo\Bar\Baz
  $filter    = new Filter\Int;          // actual; Zend\Filter\Int
  $validator = new Zend\Validator\Int;  // actual: Foo\Zend\Validator\Int
  $validator = new \Zend\Validator\Int; // actual: Zend\Validator\Int
  ```

One discovery we made was that you can have a namespace that shares the same
name as an interface of class. As an example:

```php
namespace Foo 
{
    interface Adapter 
    {
        // definition here...
    }
}

namespace Foo\Adapter
{
    use Foo\Adapter as FooAdapter;

    class Concrete implements FooAdapter
    {
        // ...
    }
}
```

This discovery has allowed us to define more "top-level" interfaces within
components, with concrete implementations in a namespace matching the
interface. This reduces some verbiage, defines a better class hierarchy, and
makes the code relations more semantic.

Finally, we've found that one huge benefit to namespaces is when unit testing:
we can define a separate namespace for unit tests, as well as separate
namespaces for each component. If we use these namespaces for test artifacts —
classes and mock adapters consumed by the unit tests — we ensure that each test
suite is fully encapsulated. This has led to fewer issues with naming
collisions.

In closing…
-----------

PHP 5.3 offers a ton of new features — those I go through here are but some of
the more prominent ones. If you haven't started hacking with 5.3, you should —
it's definitely the future of PHP, and you'll be seeing an increasing number of
libraries and frameworks using it.
