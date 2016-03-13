---
id: 106-Automating-PHPUnit2-with-SPL
author: matthew
title: 'Automating PHPUnit2 with SPL'
draft: false
public: true
created: '2006-04-08T00:57:00-04:00'
updated: '2006-04-11T18:01:43-04:00'
tags:
    - php
---
I don't blog much any more. Much of what I work on any more is for my employer, Zend, and I don't feel at liberty to talk about it (and some of it is indeed confidential). However, I *can* say that I've been programming heavily on PHP5 the past few months, and had a chance to do some pretty fun stuff. Among the new things I've been able to play with are [SPL](http://php.net/spl) and [PHPUnit](http://phpunit.de/) — and, recently, together.

<!--- EXTENDED -->

I've [written](/blog/65-phpt-Tutorial.html) [before](/blog/64-PHP-Unit-Tests-and-the-winner-is-phpt.html) about unit testing, and my preference for the phpt-style tests used in PEAR. However, since Zend Framework uses PHPUnit2, and I work at Zend… I must to as the Romans do.

I've actually come to enjoy the PHPUnit2 style of tests. In the end, I find that my tests are much less verbose than the way I was performing them with phpt, and I tend to test for failure rather than success; failure should be the exception to the rule. The myriad of 'assert' methods make this relatively easy (though some operate in unexpected ways — try testing `assertSame()` on two objects that contain PDO handles, for instance).

One thing that was missing for me was an easy way to run all tests in a directory, ala `pear run-tests`. I read the [Pocket Guide](http://www.phpunit.de/pocket_guide), and noted the possibility of creating test suites to automate running tests. (Indeed, the [newer versions of PEAR now support running PHPUnit tests](http://greg.chiaraquartet.net/archives/117-PEAR-1.4.7-released.html) via `pear run-tests` as long as there is a file named `AllTests.php` containing the test suite in the test directory.)

However, I was initially disappointed. The demonstrated way to do this is to manually require each test file and add the class contained therein to the test suite. Basically, I was going to need to touch the file every time I added a test class to the suite. Bleh!

So, I started thinking about it, and realized I could just go through the directory tree, grabbing files matching the pattern `/(.*?Test)\.php\$/`, load them up, and add their respective class (by substituting `_` for `/` in the path, and trimming the `Test.php` from the end) to the suite.

Initially, I was going to do this with the combination of `opendir()`, `readdir()`, and `closedir()`, and then thought, "I'm doing something new with PHPUnit, why not keep learning and do this with SPL?"

The problem with SPL is that it's not documented very well. It has extensive API documentation, but that's mainly of the sort, "such-and-such class exists, with such-and-such properties and methods." If any use cases exist, they're typically in the user-contributed comments. I know, if it's a problem, get off my duff and fix it — and maybe I will, when I have a spare week or so.

Fortunately, there's a nice use case of `RecursiveDirectoryIterator` in the comments to the [DirectoryIterator::construct() entry](http://php.net/directoryiterator-construct). One thing to note: you can't use `foreach()` with the `RecursiveDirectoryIterator`, as you need access to not just the `array` elements, but the iterator itself; a `for()` loop thus becomes necessary.

With `RecursiveDirectoryIterator` in hand, I was then able to whip up a very nice quick routine for creating a test suite:

```php
<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

class AllTests
{
    /**
     * Root directory of tests
     */
    public static $root;

    /**
     * Pattern against which to test files to see if they contain tests
     */
    public static $filePattern;

    /**
     * Pattern against which to test directories to see if they are for source
     * code control metadata
     */
    public static $sscsPattern = '/(CVS|\.svn)$/';

    /**
     * Associative array of test class => file
     */
    public static $list = array();

    /**
     * Main method
     *
     * @static
     * @access public
     * @return void
     */
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Create test suite by recursively iterating through tests directory
     *
     * @static
     * @access public
     * @return PHPUnit2_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('MyTestSuite');

        self::$root = realpath(dirname(__FILE__));
        self::$filePattern = '|^' . self::$root . '/(.*?Test)\.php$|';

        self::createTestList(new RecursiveDirectoryIterator(self::$root));

        foreach (self::$list as $class => $file) {
            require_once $file;
            $suite->addTestSuite($class);
        }

        return $suite;
    }

    /**
     * Recursively iterate through a directory looking for test classes
     *
     * @static
     * @access public
     * @param RecursiveDirectoryIterator $dir
     * @return void
     */
    public static function createTestList(RecursiveDirectoryIterator $dir)
    {
        for ($dir->rewind(); $dir->valid(); $dir->next()) {
            if ($dir->isDot()) {
                continue;
            }

            $file = $dir->current()->getPathname();

            if ($dir->isDir()) {
                if (!preg_match(self::$sscsPattern, $file)
                    && $dir->hasChildren())
                {
                    self::createTestList($dir->getChildren());
                }
            } elseif ($dir->isFile()) {
                if (preg_match(self::$filePattern, $file, $matches)) {
                    self::$list[str_replace('/', '_', $matches[1])] = $file;
                }
            }
        }
    }
}

/**
 * Run tests
 */
if (PHPUnit2_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
```

The crux of the class is the createTestList() method:

```php
    public static function createTestList(RecursiveDirectoryIterator $dir)
    {
        for ($dir->rewind(); $dir->valid(); $dir->next()) {
            if ($dir->isDot()) {
                continue;
            }

            $file = $dir->current()->getPathname();

            if ($dir->isDir()) {
                if (!preg_match(self::$sscsPattern, $file)
                    && $dir->hasChildren())
                {
                    self::createTestList($dir->getChildren());
                }
            } elseif ($dir->isFile()) {
                if (preg_match(self::$filePattern, $file, $matches)) {
                    self::$list[str_replace('/', '_', $matches[1])] = $file->__toString();
                }
            }
        }
    }
```

Basically, you step through each element of the directory. the `isDot()` method of RDI allows you to quickly identify the `.` and `..` entries and skip over them. `isDir()` and `isFile()` let you quickly identify directories and files with nice, OOP syntax. `hasChildren()` lets you decide whether or not you need to descend into a directory; `getChildren()` returns a new RDI object for the subdirectory.

~~What's more fun is the usage of objects as strings. `$dir->current()` actually returns an `SplFileObject`. However, because it has a defined `__toString()` method, you can use it in situations that require strings — such as the `preg_match()`s I perform here. In the case of `SplFileObject`, the `__toString()` method returns the *full* path to the file — which is much handier than when using `readdir()`, which gives only the basename, as you can much more portably and easily perform operations on the file provided (such as `require`, `file_get_contents()`, etc).~~ **Update:** Turns out there are some differences in how `DirectoryIterator` is implemented in PHP 5.0.x vs 5.1.x. As a result, I modified this to pull the `pathName()` using an agile interface instead.

The effort of using RDI is actually roughly equivalent to using `readdir()`, with the exception that I don't have to keep track of the path to the file — which is actually a pretty substantial benefit. What will be even easier is when `RegexFindFile` makes it into a core release — this will allow you to do something like:

```php
$files = new RegexFindFile(realpath(dirname(__FILE__)), '/Test\.php$/');
$files = iterator_to_array($files);
foreach ($files as $file) {
    // We're just working on filenames now... and we have the full list!
    //...
}
```

So, in the end, you get an `AllTests.php` file that you can write once and never have to touch again, assuming you name your tests consistently.
