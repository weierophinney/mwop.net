---
id: 135-Extending-Singletons
author: matthew
title: 'Extending Singletons'
draft: false
public: true
created: '2007-02-05T10:04:01-05:00'
updated: '2007-02-11T01:29:58-05:00'
tags:
    - php
---
This morning, I was wondering about how to extend a singleton class such that
you could retrieve the new class when retrieving the singleton later. In
particular, `Zend_Controller_Front` is a singleton, but what if I want to
extend it later? A number of plugins in the Zend Framework, particularly view
helpers and routing functionality, make use of the singleton; would I need to
alter all of these later so I could make use of the new subclass?

For instance, try the following code:

```php
class My_Controller_Front extends Zend_Controller_Front
{}

$front = My_Controller_Front::getInstance();
```

You'll get an instance of `Zend_Controller_Front`. But if you do the following:

```php
class My_Controller_Front extends Zend_Controller_Front
{
    protected static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

$front = My_Controller_Front::getInstance();
```

You'll now get an instance of `My_Controller_Front`. However, since
`$_instance` is *private* in `Zend_Controller_Front`, calling
`Zend_Controller_Front::getInstance()` will still return a
`Zend_Controller_Front` instance — not good.

However, if I redefine `Zend_Controller_Front::$_instance` as *protected*, and
have the following:

```php
class My_Controller_Front extends Zend_Controller_Front
{
    public static function getInstance()
    {
        if (null === self::$_instance) {

            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

$front = My_Controller_Front::getInstance();
```

Then the any time I call `getInstance()` on either `My_Controller_Front` or
`Zend_Controller_Front`, I get a `My_Controller_Front` instance!

So, the takeaway is: if you think a singleton object could ever benefit from
extension, define the static property holding the instance as protected, and
then, in any extending class, override the method retrieving the instance.
