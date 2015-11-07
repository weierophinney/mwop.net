---
id: on-error-handling-and-closures
author: matthew
title: 'On Error Handling and Closures'
draft: false
public: true
created: '2011-12-16T11:26:18-05:00'
updated: '2011-12-16T11:26:18-05:00'
tags:
    - php
    - oop
---
The error suppression operator in PHP (`@`) is often seen as a necessary evil.
Many, many low-level function will return a value indicating an error, but also
raise an `E_NOTICE` or `E_WARNING` — things you might be able to recover from,
or conditions where you may want to raise an exception.

So, at times, you find yourself writing code like this:

```php
if (false === ($fh = @fopen($filename, 'r'))) {
    throw new RuntimeException(sprintf(
        'Could not open file "%s" to read', $filename
    ));
}
```

Seems straight-forward enough, right? But it's wrong on so many levels.

<!--- EXTENDED -->

- The error doesn't magically go away. If you've got PHP's log setup, you're
  going to be getting a log entry each time the suppressed statement errors.
- Error suppression is expensive. Like, really, really expensive. A special
  error handler is registered to prevent the error propagating to the display
  (if `display_errors` is enabled), but errors are still sent to the log (as
  noted above). When done, the original error handler has to be restored.
- If you use things like `error_get_last()`, you may find that if you have many
  error suppressions, it returns something unrelated to the error that just
  occurred.
- PHPUnit, anyone? Error suppression and PHPUnit do not play well together. And
  there's a reason for that: often suppressed errors are indicative of bigger
  issues.

So, how do you address it?

PHP has two functions to assist with this: `set_error_handler()` and
`restore_error_handler()`. The first takes a callable argument, and optionally
a mask of error levels to which it will respond; the second is used to return
error handling to the previously set handler.

```php
function handleError($errno, $errmsg = '', $errfile = '', $errline = 0)
{
    throw new RuntimeException(sprintf(
        'Error reading file (in %s@%d): %s',
        $errfile, $errline, $errmsg
    ), $errno);
}

set_error_handler('handleError', E_WARNING);
$fh = fopen($filename, 'r');
restore_error_handler();
```

Traditionally, these have been a pain to use, as you have to create individual
functions or methods for handlers, and methods must have public visibility,
even if the functionality is internal to the class.

With PHP 5.3, we get a new option, however: closures.

With closures, error handlers are still a pain to use, but you now get to scope
the handlers directly in the context of the application flow. Let's look at an
example:

```php
set_error_handler(
    function($error, $message = '', $file = '', $line = 0) use ($filename) {
        throw new RuntimeException(sprintf(
            'Error reading file "%s" (in %s@%d): %s',
            $filename, $file, $line, $message
        ), $error);
    },
    E_WARNING
);
$fh = fopen($filename, 'r');
restore_error_handler();
```

If you just want to ignore the error, it's even simpler:

```php
set_error_handler(function() { return true; }, E_NOTICE);
$contents = file_get_contents($filename);
restore_error_handler();
```

The code isn't necessarily succinct, which is one reason many gravitate towards
using error suppression instead. However, it has the benefit of being
context-sensitive and robust, which is always a good goal.
