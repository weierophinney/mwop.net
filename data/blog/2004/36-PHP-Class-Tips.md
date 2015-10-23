---
id: 36-PHP-Class-Tips
author: matthew
title: 'PHP Class Tips'
draft: false
public: true
created: '2004-03-19T09:49:57-05:00'
updated: '2004-09-20T13:56:13-04:00'
tags:
    - programming
    - personal
    - php
---
We're starting to use OO in our PHP at work. I discovered when I started using
it why I'd been having problems wrapping my head around some of the applications
I've been programming lately: I've become accustomed in Perl to using an OO
framework. Suddenly, programming in PHP was much easier.

There's a few things that are different, however. It appears that you cannot
pass objects in object attributes, and then reference them like thus:

```php
$object->db>query($sql)
```

PHP doesn't like that kind of syntax (at least not in versions 4.x). Instead,
you have to pass a reference to the object in the attribute, then set a
temporary variable to that reference whenever you wish to use it:

```php
$object->db =& $db;
...
$db = $object->db;
$res = $db->query($sql);
```

What if you want to inherit from another class and extend one of the methods? In
other words, you want to use the method from the parent class, but you want to
do some additional items with it? Simple: use `parent`:

```php
function method1()
{
    /* do some pre-processing */

    parent::method1(); // Do the parent's version of the method

    /* do some more stuff here */
}
```

#### Update:

Actually, you *can* reference objects when they are attributes of another
object; you just have to define the references in the correct order:

```php
$db =& DB::connect('dsn');
$this->db =& $db;
...
$res = $this->db->query($sql);
```

I've tested the above syntax with both PEAR's DB and with Smarty, and it works
without issue.
