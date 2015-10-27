---
id: 181-Migrating-OOP-Libraries-and-Frameworks-to-PHP-5.3
author: matthew
title: 'Migrating OOP Libraries and Frameworks to PHP 5.3'
draft: false
public: true
created: '2008-06-30T09:00:00-04:00'
updated: '2008-07-06T23:49:14-04:00'
tags:
    0: php
    1: oop
    3: 'zend framework'
---
With PHP 5.3 coming up on the horizon, I'm of course looking forward to using namespaces. Let's be honest, who wants to write the following line?

```php
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
```

when the more succinct:

```php
$viewRenderer = HelperBroker::getStaticHelper('viewRenderer');
```

could be used? (Assuming you've executed `'use Zend::Controller::Action;'` somewhere earlier…)

However, while namespaces will hopefully lead to more readable code,
particularly code in libraries and frameworks, PHP developers will finally need
to start thinking about sane standards for abstract classes and interfaces.

<!--- EXTENDED -->

For instance, we've been doing things like the following in Zend Framework:

- `Zend_Controller_Request_Abstract`
- `Zend_View_Interface`

These conventions make it really easy to find Abstract classes and Interfaces
using `find` or `grep`, and also are predictable and easy to understand.
However, they won't play well with namespaces. Why? Consider the following:

```php
namespace Zend::Controller::Request

class Http extends Abstract
{
    // ...
}
```

Spot the problem? `Abstract` is a reserved word in PHP. The same goes for
interfaces. Consider this particularly aggregious example:

```php
namespace Zend::View

abstract class Abstract implements Interface
{
    // ...
}
```

We've got two reserved words there: `Abstract` *and* `Interface`.

[Stas](http://php100.wordpress.com/), Dmitry, and I sat down to discuss this a
few weeks ago to come up with a plan for migrating to PHP 5.3. In other OOP
languages, such as Python, C#, interfaces are denoted by prefixing the
interface with a capital 'I'; in the example above, we would then have
`Zend::View::IView`. We decided this would be a sane step, as it would keep the
interface within the namespace, and visually denote it as well. We also decided
that this convention made sense for abstract classes: `Zend::View::AView`. So,
our two examples become:

```php
namespace Zend::Controller::Request

class Http extends ARequest
{
    // ...
}
```

and:

```
namespace Zend::View

abstract class AView implements IView
{
    // ...
}
```

Another thing that looks likely to affect OOP libraries and frameworks is
autoloading, specifically when using exceptions. For instance, consider this:

```php
namespace Foo::Bar

class Baz
{
    public function status()
    {
        throw new Exception("This isn't what you think it is");
    }
}
```

You'd expect the exception to be of class `Foo::Bar::Exception`, right? Wrong;
it'll be a standard `Exception`. To get around this, you can do the following:

```php
namespace Foo::Bar

class Baz
{
    public function status()
    {
        throw new namespace::Exception("This is exactly what you think it is");
    }
}
```

By using the `namespace` keyword, you're telling the PHP engine to explicitly
use the Exception class from the current namespace. I also find this to be more
semantically correct — it's more explicit that you're throwing a particular
type of exception, and makes it easy to find and replace these with alternate
declarations at a later date.

I'd like to recommend other libraries adopt similar standards — they're
sensible, and fit already within PEAR/Horde/ZF coding standards. What say you?
