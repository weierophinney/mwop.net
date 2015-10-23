---
id: 140-globals-continued
author: matthew
title: 'Globals, continued'
draft: false
public: true
created: '2007-05-20T12:23:50-04:00'
updated: '2007-05-20T19:29:41-04:00'
tags:
    - php
---
**Update:** Sara has pointed out a flaw in my last case. The file
`loadFileWithGlobals.php` was incorrectly loading the wrong file — it should
be loading `withGlobals2.php` (updated now). When it does, access to `baz2`
works as it should.

As I note to in my comment, however, I stand by my original rant: relying on
globals for your applications is a bad practice, as it makes them difficult to
integrate with other applications later. Developers using your application
should not need to hunt down exactly when a global is first declared and
explicitly push it into the global scope in order to get that application to
integrate with others. Use other means, such as singletons or registries, to
persist configuration within your applications.

* * * * *

In [my last entry](/blog/139-PHP-globals-for-the-OOP-developer.html), I
evidently greatly simplified the issue to the point that my example actually
didn't display the behaviour I had observed. I'm going to show a more detailed
example that shows exactly the behaviour that was causing issues for me.

First off, this has specifically to do with including files from within
functions or class methods that then call on other files that define values in
the global scope. In the original example, I show an action controller method
that includes the serendipity bootstrap file, which in turn loads a
configuration file that sets a multi-dimensional array variable in the global
scope. Without first defining the variable in the global scope, this method of
running serendipity fails.

Now, for the examples.

<!--- EXTENDED -->

First, let's define six files. Four set variables, two by regular declaration,
the other two by declaring using `$GLOBALS`. The other two files each load one
of these and act on the variables set.

```php
<?php
// File: withoutGlobals.php
$bar = 'baz';
?>

<?php
// File: withoutGlobals2.php
$bar2 = 'baz2';
?>

<?php
// File: withGlobals.php
$GLOBALS['baz'] = 'bat';
?>

<?php
// File: withGlobals2.php
$GLOBALS['baz2'] = 'bat2';
?>

<?php
// File: loadFileWithoutGlobals.php
include dirname(__FILE__) . '/withoutGlobals2.php';

echo 'Direct access to bar2: ', $bar2, "\n";"
echo 'GLOBALS access to bar2: ', $GLOBALS['bar2'], "\n";
?>

<?php
// File: loadFileWithGlobals.php
include dirname(__FILE__) . '/withGlobals2.php';

echo 'Direct access to baz2: ', $baz2, "\n";
echo '$GLOBALS access to baz2: ', $GLOBALS['baz2'], "\n";
?>
```

Now, I'll define a class, MyFoo, that tries in a variety of ways to set and access global values:

```php
<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

class MyFoo
{
    public function setGlobal()
    {
        $GLOBALS['foo'] = 'bar';
    }

    public function loadFileWithoutGlobals()
    {
        include dirname(__FILE__) . '/withoutGlobals.php';
    }

    public function loadFileWithGlobals()
    {
        include dirname(__FILE__) . '/withGlobals.php';
    }

    public function loadScriptThatCallsFileWithoutGlobals()
    {
        include dirname(__FILE__) . '/loadFileWithoutGlobals.php';
    }

    public function loadScriptThatCallsFileWithGlobals()
    {
        include dirname(__FILE__) . '/loadFileWithGlobals.php';
    }
}
```

Finally, we actually try a few cases:

```php
<?php
$o = new MyFoo();

// Case 1; expect 'Foo: bar'
$o->setGlobal();
if (isset($foo)) {
    echo 'Foo: ', $foo, "\n";
} else {
    echo \"Foo not set\n";
}

// Case 2; expect 'Bar not set'
$o->loadFileWithoutGlobals();
if (isset($bar)) {
    echo 'Bar: ', $bar, "\n";
} else {
    echo "Bar not set\n";
}

// Case 3; expect 'Baz: bat'
$o->loadFileWithGlobals();
if (isset($baz)) {
    echo 'Baz: ', $baz, "\n";
} else {
    echo "Baz not set\n";
}

// Case 4; expect failure
$o->loadScriptThatCallsFileWithoutGlobals();

// Case 5; expect failure
$o->loadScriptThatCallsFileWithGlobals();
```

Now, I was wrong about being able to declare globals using `$GLOBALS`; the first
case, where I set `foo`, works fine. Case 2 works as I expect, too; since the
variable was technically defined in the same scope as the method, it's not
global. The third case, which I initially said didn't work in my last post,
works as well; `$baz` is set correctly in the global scope.

Cases 4 and 5 are where things get interesting. In Case 4, direct access to
`$bar2` works because it's technically in the same scope as where it's defined.
However, access to it via `$GLOBALS` fails, as expected, because it was not
defined in the global scope.

~~In case 5, *neither* access works; direct access to `$baz2` does not work, nor
does access via `$GLOBALS`; in both cases, I get a notice indicating that the
index is undefined. This was the exact situation that was causing issues for
me, and precisely the sort of inconsistency that makes working with globals so
frustrating.~~ In the updated code, Case 5 works exactly as it should; `$baz2`
is in the global scope.
