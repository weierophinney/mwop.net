---
id: 2014-08-11-testing-output-generating-code
author: matthew
title: 'Testing Code That Emits Output'
draft: false
public: true
created: '2014-08-21T14:30:00-05:00'
updated: '2014-08-21T14:30:00-05:00'
tags:
    - patterns
    - php
    - programming
    - testing
---
Here's the scenario: you have code that will emit headers and content, for
instance, a front controller. How do you test this?

The answer is remarkably simple, but non-obvious: namespaces.

<!--- EXTENDED -->

Prerequisites
-------------

For this approach to work, the assumptions are:

- Your code emitting headers and output lives in a namespace other than the
  global namespace.

That's it. Considering that most PHP code you grab anymore does this, and most
coding standards you run across will require this, it's a safe bet that you're
already ready. If you're not, go refactor your code now, before continuing;
you'll thank me later.

The technique
-------------

PHP introduced namespaces in PHP 5.3. Namespaces cover classes, as most of us
are well aware, but they also cover constants and functions — a fact often
overlooked, as before 5.6 (releasing next week!), you cannot import them via
use statements!

That does not mean they cannot be defined and used, however — it just means
that you need to manually import them, typically via a `require` or `require_once`
statement. These are usually anathema in libraries, but for testing, they work
just fine.

Here's an approach I took recently. I created a file that lives — this is the
important bit, so pay attention — *in the same namespace as the code emitting
headers and output*. This file defines several functions that live in the
global (aka PHP's built-in) namespace, and an accumulator static object I can
then use in my tests for assertions. Here's what it looks like:

```php
namespace Some\Project;

abstract class Output
{
    public static $headers = array();
    public static $body;

    public static function reset()
    {
        self::$headers = array();
        self::$body = null;
    }
}

function headers_sent()
{
    return false;
}

function header($value)
{
    Output::$headers[] = $value;
}

function printf($text)
{
    Output::$body .= $text;
}
```

A few notes:

- `headers_sent()` always returns false here, as most emitters test for a boolean true value and bail early when that occurs.
- I used `printf()` here, as echo cannot be overridden due to being a PHP
  language construct and not an actual function. As such, if you use this
  technique, you will have to likely alter your emitter to call `printf()`
  instead of echo. The benefits, however, are worth it.
- I marked Output abstract, to prevent instantiation; it should only be used
  statically.

I place the above file within my test suite, usually under a `TestAsset`
directory adjacent to the test itself; since it contains functions, I'll name
the file `Functions.php` as well. This combination typically will prevent it
from being autoloaded in any way, as the test directory will often not have
autoloading defined, or will be under a separate namespace.

Inside your PHPUnit test suite, then, you would do the following:

```php
namespace SomeTest\Project;

use PHPUnit_Framework_TestCase as TestCase;
use Some\Project\FrontController;
use Some\Project\Output;                 // <-- our Output class from above
require_once __DIR__ . '/TestAsset/Functions.php'; // <-- get our functions

class FrontControllerTest extends TestCase
{
    public function setUp()
    {
        Output::reset();
        /* ... */
    }

    public function tearDown()
    {
        Output::reset();
        /* ... */
    }
}
```

From here, you test as normal — but when you invoke methods that will cause
headers or content to emit, you can now test to see what those contain:

```php
    public function testEmitsExpectedHeadersAndContent()
    {
        /* ... */

        $this->assertContains('Content-Type: application/json', Output::$headers);
        $json = Output::$body;
        $data = json_decode($json, true);
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
    }
```

How it works
------------

Why does this work?

PHP performs some magic when it resolves functions. With classes, it looks for
a matching class either in the current namespace, or one that was imported (and
potentially aliased); if a match is not found, it stops, and raises an error.
With functions, however, it looks first in the current namespace, and if it
isn't found, then looks in the global namespace. This last part is key — it
means that if you redefine a function in the current namespace, it will be used
in lieu of the original function defined by PHP. This also means that any code
operating in the same namespace as the function — even if defined in another
file — will use that function.

This technique just leverages this fact.
