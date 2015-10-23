---
id: 190-Setting-up-your-Zend_Test-test-suites
author: matthew
title: 'Setting up your Zend_Test test suites'
draft: false
public: true
created: '2008-09-11T15:00:00-04:00'
updated: '2008-09-13T09:37:40-04:00'
tags:
    0: php
    1: mvc
    2: oop
    4: testing
    5: 'zend framework'
---
Now that [Zend_Test](http://framework.zend.com/manual/en/zend.test.html) has
shipped, developers are of course asking, "How do I setup my test suite?"
Fortunately, after some discussion with my colleagues and a little
experimenting on my one, I can answer that now.

<!--- EXTENDED -->

[PHPUnit](http://phpunit.de) offers a variety of methods for setting up test
suites, some trivial and some complex. The Zend Framework test suite, for
instance, goes for a more complex route, adding component-level suites that
require a fair amount of initial setup, but which allow us fairly fine-grained
control.

However, testing and test automation should be easy and the complex approach is
overkill for most of our applications. Fortunately, PHPUnit offers some other
methods that make doing so relatively simple. The easiest method is to use an
[XML configuration file](http://www.phpunit.de/pocket_guide/3.2/en/appendixes.configuration.html).

As an example, consider the following:

```xml
<phpunit>
    <testsuite name="My Test Suite">
        <directory>./</directory>
    </testsuite>

    <filter>
        <whitelist>
            <directory suffix=".php">../library/</directory>
            <directory suffix=".php">../application/</directory>
            <exclude>
                <directory suffix=".phtml">../application/</directory>
            </exclude>
        </whitelist>
    </filter>

    <logging>
        <log type="coverage-html" target="./log/report" charset="UTF-8"
            yui="true" highlight="true"
            lowUpperBound="50" highLowerBound="80"/>
        <log type="testdox-html" target="./log/testdox.html" />
    </logging>
</phpunit>
```

First thing to note, relative paths are relative to the configuration file.
This allows you to run your tests from anywhere in your tests tree. Second,
providing a `directory` directive to the `testsuite` directive scans for all
files ending in `Test.php` in that directory, meaning you don't have to keep a
list of your test cases manually. It's a great way to automate the suite.
Third, the filter directive allows us to determine what classes to include
and/or exclude from coverage reports. Finally, the `logging` directive lets us
specify what kinds of logs to create and where.

Drop the above into `tests/phpunit.xml` in your application, and you can start
writing test cases and running the suite immediately, using the following
command:

```bash
$ phpunit --configuration phpunit.xml
```

I like to group my test cases by type. I have controllers, models, and often
library code, and need to keep the tests organized both on the filesystem as
well as for running the actual tests. There are two things I do to facilitate
this.

First, I create directories. For instance, I have the following hierarchy in my
test suite:

```
tests/
    phpunit.xml
    TestHelper.php
    controllers/
        IndexControllerTest.php (contains IndexControllerTest)
        ErrorControllerTest.php (contains ErrorControllerTest)
        ...
    models/
        PasteTest.php           (contains PasteTest)
        DbTable/
            PasteTest.php       (contains DbTable_PasteTest)
        ...
    My/
        Form/
            Element/
                SimpleTextareaTest.php
```

`controllers/` contains my controllers, `models/` contains my models. If I were
developing a modular application, I'd have something like `blog/controllers/`
instead. Library code is given the same hierarchy as is found in my `library/`
directory.

Second, I use docblock annotations to group my tests. I add the following to my
class-level docblock in my controller test cases:

```php
/**
 * @group Controllers
 */
```

Models get the annotation `@group Models`, etc. This allows me to run
individual sets of tests on demand:

```bash
$ phpunit --configuration phpunit.xml --group=Controllers
```

You can specify multiple `@group` annotations, which means you can separate
tests into modules, issue report identifiers, etc; additionally, you can add
the annotations to individual test methods themselves to have really
fine-grained test running capabilities.

Astute readers will have noticed the `TestHelper.php` file in that directory
listing earlier, and will be wondering what that's all about.

A test suite needs some environmental information, just like your application
does. It may need a default database adapter, altered `include_path`s,
autoloading set up, and more. Here's what my `TestHelper.php` looks like:

```php
<?php
/*
 * Start output buffering
 */
ob_start();

/*
 * Set error reporting to the level to which code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Set default timezone
 */
date_default_timezone_set('GMT');

/*
 * Testing environment
 */
define('APPLICATION_ENV', 'testing');

/*
 * Determine the root, library, tests, and models directories
 */
$root        = realpath(dirname(__FILE__) . '/../');
$library     = $root . '/library';
$tests       = $root . '/tests';
$models      = $root . '/application/models';
$controllers = $root . '/application/controllers';

/*
 * Prepend the library/, tests/, and models/ directories to the
 * include_path. This allows the tests to run out of the box.
 */
$path = array(
    $models,
    $library,
    $tests,
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Register autoloader
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

/**
 * Store application root in registry
 */
Zend_Registry::set('testRoot', $root);
Zend_Registry::set('testBootstrap', $root . '/application/bootstrap.php');

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $models, $controllers, $tests, $path);
```

The above ensures that my `APPLICATION_ENV` constant is set appropriately, that error reporting is appropriate for tests (i.e., I want to see *all* errors), and that autoloading is enabled. Additionally, I place a couple items in my registry — the bootstrap and test root directory.

In each test case file, I then do a `require_once` on this file. In future
versions of PHPUnit, you'll be able to specify a bootstrap file in your
configuration XML that gets pulled in for each test case, and you'll be able to
even further automate your testing environment setup.

Hopefully this will get you started with your application testing; what are you
waiting for?
