---
id: 131-Overloading-arrays-in-PHP-5.2.0
author: matthew
title: 'Overloading arrays in PHP 5.2.0'
draft: false
public: true
created: '2007-01-18T15:39:00-05:00'
updated: '2007-01-21T16:00:39-05:00'
tags:
    - php
---
**Update:** I ran into issues with the `ArrayObject` solution, as there was a bug in PHP 5.2.0 (now fixed) with its interaction with `empty()` and `isset()` when used with the `ARRAY_AS_PROPS` flag. I tried a number of fixes, but eventually my friend [Mike](http://mikenaberezny.com/) pointed out something I'd missed: as of PHP 5.1, setting undefined public properties no longer raises an `E_STRICT` notice. Knowing this, you can now do the following without raising any errors:

```php
class Foo
{
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}

$foo        = new Foo();
$foo->bar   = array();
$foo->bar[] = 42;
```

This is a much simpler solution, performs better, and solves all the issues I was presented. Thanks, Mike!

* * * * *

<!--- EXTENDED -->

Several weeks back, a bug was reported against [Zend_View](http://framework.zend.com/manual/en/zend.view.html) that had me initially stumped. Basically, the following was now failing in PHP 5.2.0:

```php
$view->foo   = array();
$view->foo[] = 42;
```

A notice was raised stating, "Notice: Indirect modification of overloaded property Zend_View::$foo has no effect."

I'd read about this some months back on the php internals list, but at the time hadn't understood the consequences. Basically, `__get()` no longer returns a reference and returns values in read mode, which makes modifying arrays using overloading impossible using traditional methods.

Derick Rethans [blogged about the issue](http://derickrethans.nl/overloaded_properties_get.php) in August. His solution was to use a `switch()` statement in `__get()` to cast the returned value explicitly as an array:

```php
    public function __get($key)
    {
        if (is_array($this->_vars[$key])) {
            return (array) $this->_vars[$key];
        }

        return $this->_vars[$key];
    }
```

The problem with this approach is that you then have issues with other array functionalities, such as assigning by reference.

After some work, I found the best solution was to have the class extend `ArrayObject`, but with a slight twist:

```php
class My_Class extends ArrayObject
{
    public function __construct($config = array())
    {
        // ... some setup

        // Allow accessing properties as either array keys or object properties:
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
    }
}
```

This combination allows some very flexible access to properties in the object:

```php
// from the original example:
$view->foo   = array();
$view->foo[] = 42;

echo $view['foo'][0]; // '42'
echo $view->foo[0];   // same
```

One issue that was always difficult to work with in `Zend_View` was keeping 'public' properties — template variables — separate from private/protected properties (things like the helper, filter, and script paths). Since those properties are pre-declared in the class, the `ArrayObject::ARRAY_AS_PROPS` setting prevented any such collision from happening — and helped simplify the code.

Moral of the story? If you need to be able to modify overloaded arrays in your class, and support PHP 5.2.0, extend `ArrayObject`.
