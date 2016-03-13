---
id: 47-When-array_key_exists-just-doesnt-work
author: matthew
title: 'When array_key_exists just doesn''t work'
draft: false
public: true
created: '2004-10-22T23:02:45-04:00'
updated: '2004-10-22T23:02:55-04:00'
tags:
    - personal
    - php
---
I've been playing with parameter testing in my various Cgiapp classes, and one
test that seemed pretty slick was the following:

```php
if (!array_key_exists('some_string', $_REQUEST)) {
    // some error
}
```

Seems pretty straight-forward: `$_REQUEST` is an associative array, and I want
to test for the existence of a key in it. Sure, I could use `isset()`, but it
seemed… ugly, and verbose, and a waste of keystrokes, particularly when I'm
using the `param()` method:

```php
if (!isset($_REQUEST[$this->param('some_param')])) {
    // some error
}
```

However, I ran into a pitfall: when it comes to `array_key_exists()`,
`$_REQUEST` isn't exactly an array. I think what's going on is that `$_REQUEST`
is actually a superset of several other arrays — `$_POST`, `$_GET`, and
`$_COOKIE` — and `isset()` has some logic to descend amongst the various keys,
while `array_key_exists()` can only work on a single level.

Whatever the explanation, I ended up reverting a bunch of code. :-(
