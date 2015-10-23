---
id: 121-Benchmarking-dynamic-functionmethod-calls
author: matthew
title: 'Benchmarking dynamic function/method calls'
draft: false
public: true
created: '2006-06-23T11:00:00-04:00'
updated: '2006-06-23T14:04:10-04:00'
tags:
    - php
---
In response to [Scott Johnson's request for advice on variable functions](http://fuzzyblog.com/archives/2006/06/22/php-advice-requested-variable-functions-versus-call_user_func-and-call_user_func_array/), I decided to run some benchmarks.

`<rant>`Writing benchmarks is easy. Yet I see a lot of blog entries and mailing list postings asking, "Which is faster?" My first thought is always, "Why didn't they test and find out?" If I ever have a question about how something will work, I open up a temporary file, start coding, and run the code. It's the easiest way to learn. Also, it teaches you to break things into manageable, testable chunks, and this code often forms the basis for a unit test later.`</rant>`

Back to benchmarking. Scott asks, "Is there a real difference between `call_user_func` versus `call_user_func_array` and the variable function syntax i.e. `$function_name()`?"

The short answer: absolutely. The long answer? Read on.

<!--- EXTENDED -->

First, the difference betwee `call_user_func()` and `call_user_func_array()`. `call_user_func()` is handy when you know exactly how many arguments the function or method you're calling takes, and that this won't vary even if the actual callback does. Instances where this would come into play include when calling observers for which there is an established interface, and you know that the called method on these observers will always have the same number of arguments. Additionally, with `call_user_func()`, you would have each argument ready to pass individually:

```php
call_user_func($callback, $arg1, $arg2, $arg3);
```

But what if you don't know how many arguments you have, or the number of arguments varies between calls? How would you build the calls to `call_user_func()`? This is where `call_user_func_array()` comes into play. Basically, `call_user_func_array()` expects only two arguments: the callback and an array of arguments to pass to the callback:

```php
$callback = 'myFunc';
$args = ('me', 'myself', I');
call_user_func_array($callback, $args);
```

This gets called as:

```php
myFunc('me', 'myself', 'I');
```

When would this be handy? When I was developing [Cgiapp2](http://cgiapp.sourceforge.net/), I knew that template engines often take variable numbers of arguments for their `assign()` methods (assigning variables to templates) — a key and a value, just a value, or an associative array of key/value pairs, for instance. Since I couldn't know in advance what the arguments would be, I setup the subject to allow a variable number of arguments, and then passed them en masse to the observer:

```php
class myClass
{
    // observer callback
    public static $observer;

    function subject()
    {
        // get arguments
        $args = func_get_args();

        // call observer with all arguments
        call_user_func_array(self::$observer, $args);
    }
}
```

So, now, what about dynamic functions? These are handy, but can be somewhat limiting: you can use them with object instance methods or defined functions, but they won't work with static methods. If you try `$class::$method`, you'll get an [unexpected T_PAAMAYIM_NEKUDOTAYIM](http://pluralvision.com/blog/?p=31) parser error. In that case, you *must* use either `call_user_func()` or `call_user_func_array()`.

All done and told, let's answer Scott's question, "Any efficiency benefits in doing it one way or another?"

From a pure execution time standpoint, yes. I ran the following code:

```php
class myTest
{
    public static function test()
    {
        return true;
    }

    public function testMe()
    {
        return true;
    }
}

function testMe()
{
    return true;
}

$o = new myTest();

$function = 'testMe';

echo 'Straight function call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    testMe();
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'Dynamic function call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $function();
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func function call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func($function);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func_array function call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func_array($function, null);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'Straight static method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    myTest::test();
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func static method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func(array('myTest', 'test'));
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func_array static method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func_array(array('myTest', 'test'), null);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'Straight method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $o->testMe();
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func(array($o, 'testMe'));
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";

echo 'call_user_func_array method call: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func_array(array($o, 'testMe'), null);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "\n";
```

which, on my machine, gave me these results:

    Straight function call: 0.909409046173 secs
    Dynamic function call: 1.14596605301 secs
    call_user_func function call: 1.48889017105 secs
    call_user_func_array function call: 2.02058911324 secs
    Straight static method call: 0.789363861084 secs
    call_user_func static method call: 4.42607593536 secs
    call_user_func_array static method call: 2.98122406006 secs
    Straight method call: 1.10703587532 secs
    call_user_func method call: 2.71344089508 secs
    call_user_func_array method call: 2.56111383438 secs

*Note: running these several times in succession yielded slightly different results; interpretation will be based on running several times.*

- Dynamic function calls are slightly slower than straight calls (the former have an extra interpretive layer to determine the function to call
- `call_user_func()` is about 50% slower, and `call_user_func_array()` is about 100% slower than a straight function call.
- Static and regular method calls are roughly equivalent to function calls
- `call_user_func()` on method calls is typically slower than `call_user_func_array()`, and the faster operation usually takes at least twice the execution time of a straight call.

From a pure performance standpoint, `call_user_func()` and `call_user_func_array()` are performance hogs. However, from a developer standpoint, they can save a lot of time and headaches: they can enable you to write a flexible [Observer/Subject](http://en.wikipedia.org/wiki/Observer_design_pattern) pattern or [Decorator pattern](http://en.wikipedia.org/wiki/Decorator_pattern), both of which can make your classes and applications more flexible and extensible, saving you coding time later.
