---
id: 65-phpt-Tutorial
author: matthew
title: 'phpt Tutorial'
draft: false
public: true
created: '2005-04-20T21:41:12-04:00'
updated: '2005-04-20T22:53:58-04:00'
tags:
    - php
---
As promised in my earlier entry from today, here's my quick-and-dirty tutorial
on unit testing in PHP using phpt.

First off, phpt test files, from what I can see, were created as part of the
[PHP-QA](http://qa.php.net/) effort. While I cannot find a link within the
PHP-QA site, they [have a page detailing phpt test files](http://qa.php.net/write-test.php),
and this page shows all the sections of a phpt test file, though they do not
necessarily show examples of each.

Also, you might find [this International PHP Magazine article](http://www.phpmag.net/itr/kolumnen/psecom,id,26,nodeid,207.html)
informative; in it [Aaron Wormus](http://www.wormus.com/aaron) gives a brief
tutorial on them, as well as some ways to use phpt tests with PHPUnit.

Finally, before I jump in, I want to note: I am not an expert on unit testing.
However, the idea behind unit tests is straightforward: keep your code simple
and modular, and test each little bit (or module) for all types of input and
output. If the code you're testing is a function or class method, test all
permutations of arguments that could be passed to it, and all possible return
values.

Okay, let's jump in!

<!--- EXTENDED -->

### Overview

The basic format of a phpt test file looks like this:

```php
--TEST--
test name
--FILE--
<?php
// your PHP code goes here
?>
--EXPECT--
Expected output from the PHP code
```

As you can see, the file is broken into several sections, each beginning with a
`--TITLE--`. `--TEST--` is the name of the test; this could be a function name,
a class name, a class method name, or some free text. Try and make it
meaningful. `--FILE--` is the PHP code that will be executed, and `--EXPECT--`
is the expected output from this PHP code. **The test passes if the output from
the PHP code matches what's expected.**

There are some other sections you can use as well; I've used the `--SKIPIF--`
section type to test for which version of PHP is present (Cgiapp2 is PHP5-only,
for instance); if the condition is met, then the test is skipped. You may also
specify `--EXPECTF--` or `--EXPECTREGEX--` instead of `--EXPECT--`, but I found
that in most cases, I could control the output from my code such that neither of
those was necessary.

### Tips for Writing Tests

First off, my sole experience with phpt tests is testing Cgiapp and Cgiapp2,
which are classes; these tips may not make sense in other situations.

Second, **tips are highlighted in bold**.

What I found is that you should **create one test file per method**. (Generally
speaking, that is; I have encountered a few situations where I needed multiple
files, primarily when testing code that uses header().) In that test file, you
then want to test:

- Method Arguments
- Method return value(s)

This means that you'll need to write code for a number of situations. After
writing a few tests, I discovered that it becomes hard to debug if you do not
include informational output in your test code. **Create informational output
about what's being tested:**

```php
<?php
echo "Test 1: single string argument\n";
?>
```

These statements are invaluable when a test fails; you can then see what you
were testing at a glance.

If you're using `trigger_error()` or `PEAR_Error` in your code (you are, aren't
you?), **include an error handler in your test code** so you can trap these and
convert them to messages you can format and control.

Supposedly, the `--GET--` and `--POST--` sections allow you to specify the
variables present in those arrays for the purpose of your tests. However, this
only works on CGI versions of PHP… and, if you're like me, you're using the CLI
SAPI. The easy workaround is to simply **build your `$_GET` and `$_POST` arrays
in the `--FILE--` section**.

The same is true for `$_SESSION`. However, the `$_SESSION` array *will* be
present if you specify `session_start()`; it will simply be empty.

If you need to include a file, include it relative to the test directory. To
determine what that directory is (don't assume it's `.`), **use the construct
`dirname(__FILE__)`**:

```php
require_once dirname(__FILE__) . '/setup.php.inc';
```

### Running Tests

Once you have a test file, simply execute **pear run-tests testFile.phpt**
(substituting your test file's name, of course). If you wish to run several
tests at once from several files, you may include each file's name as an
argument. If you want to run all tests in a directory, simply execute **pear
run-tests** without any arguments.

When tests are run, you will see information on the screen. If a test fails, the
name of the test file and the test name are given.

### Debugging a Failed Test

Eventually, a test will fail. It may be that you wrote it incorrectly, or that
you actually have a bug in your code. The question is, how do phpt tests help
you figure out which?

When tests are run on a file, the file is split on its sections. The `--FILE--`
section is actually written to a file named after the test file, but with the
`.php` extension. The `--EXPECT--` section is written to a file with the `.exp`
section; output is piped to a file with the `.out` section; and a log of what
transpires is written to a file with the `.log` extension. Finally, if the test
fails, a `.diff` file is created containing the diff between the `.exp` and
`.out` files. For example, if we have a test file named **testFile.phpt**, and
it fails tests, we'll now have the following files:

```
run-tests.log
testFile.diff
testFile.exp
testFile.log
testFile.out
testFile.php
testFile.phpt
```

Your first stop should be the `.diff` file. At a glance, you will be able to
see, for instance, if a PHP error occurred. I discovered in several of my tests
that I'd missed semicolons or braces in my test code when I saw syntax error
warnings pop up in these diffs.

If the `.diff` doesn't explain the differences enough for you, pop open your
`.exp` and `.out` files. I use [VIM](http://www.vim.org/), and I typically
execute a **:vsplit** so I can load these files up side by side and compare
them. In doing so, I can visually see where the output starts to differ from the
expected. (Several times I discovered typos in my expected, which meant the
tests ran fine after I fixed the typo.)

Remember how I said earlier to **create informational output about what's being
tested**? This is where it comes into play. What I found is that output that
reads like:

```
.
.
Bad argument passed
something
```

is simply harder to understand than:

```
Test 1: current directory as argument
.
Test 2: no argument passed
.
Test 3: object as argument
Bad argument passed
Test 4: 'something' as argument
something
```

In the above example, if what was expected for test 2 was something else, I now
know exactly which test in my test file failed — and that helps me determine
where I might need to go to fix it in my code.

### Summary

#### Tips for Writing Tests

- Create one test file per method
- Create informational output about what's being tested
- Include an error handler in your test code, if errors are being triggered
- Build your `$_GET` and `$_POST` arrays in the `--FILE--` section; it's more portable than `--GET--` and `--POST--`
- use the construct `dirname(__FILE__)`

#### Running Tests

- `pear run-tests testFile.phpt`
- `pear run-tests testFile1.phpt testFile2.phpt`
- `pear run-tests`

#### Debugging a Failed Test

- Examine the `.diff` file; look for PHP errors in your test code
- Compare the `.exp` and `.out` files side-by-side:
  - Check for typos in your expected output
  - Check informational output to determine which part of the test failed

Where to go from here
---------------------

Obviously, the only way to fully understand testing is to do it. There are
plenty of resources on unit testing available; the [c2 wiki](http://www.c2.com/cgi/wiki)
has some good resources, and many books cover the subject (*The Pragmatic
Programmer* comes to mind).

I've read arguments that you should test first the interface. This means that
you don't throw unexpected arguments at a function/method. Later, after the code
matures, you either add tests for the unexpected arguments, or you add tests for
bugs that have been reported. The PHP-QA site recommends having a test file for
the method, but then also having test files that address specific bugs; I have
yet to go that far with testing.

Finally, I have read in a number of resources that true Unit Testing should
start *before* you start programming. While I understand this principle to a
degree, I also find that as I code, I discover intricacies in the problem that I
could not have anticipated earlier… and the solutions to those intricacies are
often new methods. To that end, I feel that writing tests should happen after
the first draft of code. Doing so provides the first interface with the code,
and also helps code cleanup and bug hunting before application testing begins.
However, this is my humble opinion only.

Happy testing!
