<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('106-Automating-PHPUnit2-with-SPL');
$entry->setTitle('Automating PHPUnit2 with SPL');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1144472220);
$entry->setUpdated(1144792903);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I don't blog much any more. Much of what I work on any more is for my
    employer, Zend, and I don't feel at liberty to talk about it (and some of it
    is indeed confidential). However, I <em>can</em> say that I've been
    programming heavily on PHP5 the past few months, and had a chance to do some
    pretty fun stuff. Among the new things I've been able to play with are 
    <a href="http://php.net/spl">SPL</a> and 
    <a href="http://phpunit.de/">PHPUnit</a> -- and, recently, together.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I've <a href="/matthew/archives/65-phpt-Tutorial.html">written</a> 
    <a href="/matthew/archives/64-PHP-Unit-Tests-and-the-winner-is-phpt.html">before</a>
    about unit testing, and my preference for the phpt-style tests used in PEAR.
    However, since Zend Framework uses PHPUnit2, and I work at Zend... I must to
    as the Romans do.
</p>
<p>
    I've actually come to enjoy the PHPUnit2 style of tests. In the end, I find
    that my tests are much less verbose than the way I was performing them with
    phpt, and I tend to test for failure rather than success; failure should be
    the exception to the rule. The myriad of 'assert' methods make this
    relatively easy (though some operate in unexpected ways -- try testing
    assertSame() on two objects that contain PDO handles, for instance).
</p>
<p>
    One thing that was missing for me was an easy way to run all tests in a
    directory, ala 'pear run-tests'.  I read the 
    <a href="http://www.phpunit.de/pocket_guide">Pocket Guide</a>, and noted the
    possibility of creating test suites to automate running tests. (Indeed, the
    <a href="http://greg.chiaraquartet.net/archives/117-PEAR-1.4.7-released.html">newer versions of PEAR now support running PHPUnit tests</a> 
    via pear run-tests as long as there is a file named AllTests.php containing
    the test suite in the test directory.)
</p>
<p>
    However, I was initially disappointed. The demonstrated way to do this is to
    manually require each test file and add the class contained therein to the
    test suite. Basically, I was going to need to touch the file every time I
    added a test class to the suite. Bleh!
</p>
<p>
    So, I started thinking about it, and realized I could just go through the
    directory tree, grabbing files matching the pattern '/(.*?Test)\.php$/',
    load them up, and add their respective class (by substituting '_' for '/' in
    the path, and trimming the Test.php from the end) to the suite.
</p>
<p>
    Initially, I was going to do this with the combination of opendir(),
    readdir(), and closedir(), and then thought, "I'm doing something new with
    PHPUnit, why not keep learning and do this with SPL?"
</p>
<p>
    The problem with SPL is that it's not documented very well. It has extensive
    API documentation, but that's mainly of the sort, 'such-and-such class
    exists, with such-and-such properties and methods.' If any use cases exist,
    they're typically in the user-contributed comments. I know, if it's a
    problem, get off my duff and fix it -- and maybe I will, when I have a spare
    week or so.
</p>
<p>
    Fortunately, there's a nice use case of RecursiveDirectoryIterator in the
    comments to the 
    <a href="http://php.net/directoryiterator-construct">DirectoryIterator::construct() entry</a>.
    One thing to note: you can't use foreach() with the
    RecursiveDirectoryIterator, as you need access to not just the 'array'
    elements, but the iterator itself; a for() loop thus becomes necessary.
</p>
<p>
<p>
    With RecursiveDirectoryIterator in hand, I was then able to whip up a very
    nice quick routine for creating a test suite:
</p>
<div class="example"><pre><code lang="php">
&lt;?php
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
     * Associative array of test class =&gt; file
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

        foreach (self::$list as $class =&gt; $file) {
            require_once $file;
            $suite-&gt;addTestSuite($class);
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
        for ($dir-&gt;rewind(); $dir-&gt;valid(); $dir-&gt;next()) {
            if ($dir-&gt;isDot()) {
                continue;
            }

            $file = $dir-&gt;current()-&gt;getPathname();

            if ($dir-&gt;isDir()) {
                if (!preg_match(self::$sscsPattern, $file)
                    &amp;&amp; $dir-&gt;hasChildren())
                {
                    self::createTestList($dir-&gt;getChildren());
                }
            } elseif ($dir-&gt;isFile()) {
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
</code></pre></div>
<p>
    The crux of the class is the createTestList() method:
</p>
<div class="example"><pre><code lang="php">
    public static function createTestList(RecursiveDirectoryIterator $dir)
    {
        for ($dir-&gt;rewind(); $dir-&gt;valid(); $dir-&gt;next()) {
            if ($dir-&gt;isDot()) {
                continue;
            }

            $file = $dir-&gt;current()-&gt;getPathname();

            if ($dir-&gt;isDir()) {
                if (!preg_match(self::$sscsPattern, $file)
                    &amp;&amp; $dir-&gt;hasChildren())
                {
                    self::createTestList($dir-&gt;getChildren());
                }
            } elseif ($dir-&gt;isFile()) {
                if (preg_match(self::$filePattern, $file, $matches)) {
                    self::$list[str_replace('/', '_', $matches[1])] = $file-&gt;__toString();
                }
            }
        }
    }
</code></pre></div>
<p>
    Basically, you step through each element of the directory. the isDot()
    method of RDI allows you to quickly identify the . and .. entries and skip
    over them. isDir() and isFile() let you quickly identify directories and
    files with nice, OOP syntax. hasChildren() lets you decide whether or not
    you need to descend into a directory; getChildren() returns a new RDI object
    for the subdirectory.
</p>
<p>
    <strike>
    What's more fun is the usage of objects as strings. $dir->current() actually
    returns an SplFileObject. However, because it has a defined __toString()
    method, you can use it in situations that require strings -- such as the
    preg_match()s I perform here. In the case of SplFileObject, the __toString()
    method returns the <em>full</em> path to the file -- which is much handier
    than when using readdir(), which gives only the basename, as you can much
    more portably and easily perform operations on the file provided (such as
    require, file_get_contents(), etc).
    </strike>
    <b>Update:</b> Turns out there are some differences in how DirectoryIterator
    is implemented in PHP 5.0.x vs 5.1.x. As a result, I modified this to pull
    the pathName() using an agile interface instead.
</p>
<p>
    The effort of using RDI is actually roughly equivalent to using readdir(),
    with the exception that I don't have to keep track of the path to the file
    -- which is actually a pretty substantial benefit. What will be even easier
    is when RegexFindFile makes it into a core release -- this will allow you to
    do something like:
</p>
<div class="example"><pre><code lang="php">
    $files = new RegexFindFile(realpath(dirname(__FILE__)), '/Test\.php$/');
    $files = iterator_to_array($files);
    foreach ($files as $file) {
        // We're just working on filenames now... and we have the full list!
        //...
    }
</code></pre></div>
<p>
    So, in the end, you get an AllTests.php file that you can write once and
    never have to touch again, assuming you name your tests consistently.
</p>
EOT;
$entry->setExtended($extended);

return $entry;