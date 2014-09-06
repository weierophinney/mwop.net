<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('65-phpt-Tutorial');
$entry->setTitle('phpt Tutorial');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1114047672);
$entry->setUpdated(1114052038);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    As promised in my earlier entry from today, here's my quick-and-dirty
    tutorial on unit testing in PHP using phpt.
</p>
<p>
    First off, phpt test files, from what I can see, were created as part of the
    <a href="http://qa.php.net/">PHP-QA</a> effort. While I cannot find a link
    within the PHP-QA site, they <a href="http://qa.php.net/write-test.php">have
        a page detailing phpt test files</a>, and this page shows all the
    sections of a phpt test file, though they do not necessarily show examples
    of each.
</p>
<p>
    Also, you might find <a
        href="http://www.phpmag.net/itr/kolumnen/psecom,id,26,nodeid,207.html">this
        International PHP Magazine article</a> informative; in it <a
        href="http://www.wormus.com/aaron">Aaron Wormus</a> gives a brief
    tutorial on them, as well as some ways to use phpt tests with PHPUnit.
</p>
<p>
    Finally, before I jump in, I want to note: I am not an expert on unit
    testing. However, the idea behind unit tests is straightforward: keep your
    code simple and modular, and test each little bit (or module) for all types
    of input and output. If the code you're testing is a function or class
    method, test all permutations of arguments that could be passed to it, and
    all possible return values.
</p>
<p>
    Okay, let's jump in!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Overview</h3>
<p>
    The basic format of a phpt test file looks like this:
</p>
<pre>
--TEST--
test name
--FILE--
&lt;?php
// your PHP code goes here
?&gt;
--EXPECT--
Expected output from the PHP code
</pre>
<p>
    As you can see, the file is broken into several sections, each beginning
    with a --TITLE--. --TEST-- is the name of the test; this could be a function
    name, a class name, a class method name, or some free text. Try and make it
    meaningful. --FILE-- is the PHP code that will be executed, and --EXPECT--
    is the expected output from this PHP code. <b>The test passes if the output
    from the PHP code matches what's expected.</b>
</p>
<p>
    There are some other sections you can use as well; I've used the --SKIPIF--
    section type to test for which version of PHP is present (Cgiapp2 is
    PHP5-only, for instance); if the condition is met, then the test is skipped.
    You may also specify --EXPECTF-- or --EXPECTREGEX-- instead of --EXPECT--,
    but I found that in most cases, I could control the output from my code such
    that neither of those was necessary.
</p>
<h3>Tips for Writing Tests</h3>
<p>
    First off, my sole experience with phpt tests is testing Cgiapp and Cgiapp2,
    which are classes; these tips may not make sense in other situations.
</p>
<p>
    Second, <b>tips are highlighted in bold</b>.
</p>
<p>
    What I found is that you should <b>create one test file per method</b>.
    (Generally speaking, that is; I have encountered a few situations where I
    needed multiple files, primarily when testing code that uses header().) In
    that test file, you then want to test:
</p>
<ul>
     <li>Method Arguments</li>
     <li>Method return value(s)</li>
</ul>
<p>
    This means that you'll need to write code for a number of situations. After
    writing a few tests, I discovered that it becomes hard to debug if you do
    not include informational output in your test code. <b>Create informational
    output about what's being tested:</b>
</p>
<pre>
&lt;?php
echo "Test 1: single string argument\n";
?&gt;
</pre>
<p>
    These statements are invaluable when a test fails; you can then see what you
    were testing at a glance.
</p>
<p>
    If you're using trigger_error() or PEAR_Error in your code (you are, aren't
    you?), <b>include an error handler in your test code</b> so you can trap
    these and convert them to messages you can format and control.
</p>
<p>
    Supposedly, the --GET-- and --POST-- sections allow you to specify the
    variables present in those arrays for the purpose of your tests. However,
    this only works on CGI versions of PHP... and, if you're like me, you're
    using the CLI SAPI. The easy workaround is to simply <b>build your $_GET and
    $_POST arrays in the --FILE-- section</b>.
</p>
<p>
    The same is true for $_SESSION. However, the $_SESSION array <em>will</em>
    be present if you specify session_start(); it will simply be empty.
</p>
<p>
    If you need to include a file, include it relative to the test directory. To
    determine what that directory is (don't assume it's '.'), <b>use the
    construct dirname(__FILE__)</b>:
</p>
<pre>
require_once dirname(__FILE__) . '/setup.php.inc';
</pre>

<h3>Running Tests</h3>
<p>
    Once you have a test file, simply execute <b>pear run-tests
    testFile.phpt</b> (substituting your test file's name, of course). If you
    wish to run several tests at once from several files, you may include each
    file's name as an argument. If you want to run all tests in a directory,
    simply execute <b>pear run-tests</b> without any arguments.
</p>
<p>
    When tests are run, you will see information on the screen. If a test fails,
    the name of the test file and the test name are given.
</p>

<h3>Debugging a Failed Test</h3>
<p>
    Eventually, a test will fail. It may be that you wrote it incorrectly, or
    that you actually have a bug in your code. The question is, how do phpt
    tests help you figure out which?
</p>
<p>
    When tests are run on a file, the file is split on its sections. The
    --FILE-- section is actually written to a file named after the test file,
    but with the .php extension. The --EXPECT-- section is written to a file
    with the .exp section; output is piped to a file with the .out section; and
    a log of what transpires is written to a file with the .log extension.
    Finally, if the test fails, a .diff file is created containing the diff
    between the .exp and .out files. For example, if we have a test file named
    <b>testFile.phpt</b>, and it fails tests, we'll now have the following
    files:
</p>
<pre>
run-tests.log
testFile.diff
testFile.exp
testFile.log
testFile.out
testFile.php
testFile.phpt
</pre>
<p>
    Your first stop should be the .diff file. At a glance, you will be able to
    see, for instance, if a PHP error occurred. I discovered in several of my
    tests that I'd missed semicolons or braces in my test code when I saw syntax
    error warnings pop up in these diffs.
</p>
<p>
    If the .diff doesn't explain the differences enough for you, pop open your
    .exp and .out files. I use <a href="http://www.vim.org/">VIM</a>, and I
    typically execute a <b>:vsplit</b> so I can load these files up side by side
    and compare them. In doing so, I can visually see where the output starts to
    differ from the expected. (Several times I discovered typos in my expected,
    which meant the tests ran fine after I fixed the typo.) 
</p>
<p>
    Remember how I said earlier to <b>create informational output about what's
    being tested</b>?  This is where it comes into play. What I found is that
    output that reads like:
</p>
<pre>
.
.
Bad argument passed
something
</pre>
<p>
    is simply harder to understand than:
</p>
<pre>
Test 1: current directory as argument
.
Test 2: no argument passed
.
Test 3: object as argument
Bad argument passed
Test 4: 'something' as argument
something
</pre>
<p>
    In the above example, if what was expected for test 2 was something else, I
    now know exactly which test in my test file failed -- and that helps me
    determine where I might need to go to fix it in my code.
</p>

<h3>Summary</h3>
<h4>Tips for Writing Tests</h4>
<ul>
    <li>Create one test file per method</li>
    <li>Create informational output about what's being tested</li>
    <li>Include an error handler in your test code, if errors are being
        triggered</li>
    <li>Build your $_GET and $_POST arrays in the --FILE-- section; it's
        more portable than --GET-- and --POST--</li>
    <li>use the construct <b>dirname(__FILE__)</b></li>
</ul>
<h4>Running Tests</h4>
<ul>
    <li>pear run-tests testFile.phpt</li>
    <li>pear run-tests testFile1.phpt testFile2.phpt</li>
    <li>pear run-tests</li>
</ul>
<h4>Debugging a Failed Test</h4>
<ul>
    <li>Examine the .diff file; look for PHP errors in your test code</li>
    <li>Compare the .exp and .out files side-by-side:
        <ul>
            <li>Check for typos in your expected output</li>
            <li>Check informational output to determine which part of the test
            failed</li>
        </ul>
    </li>
</ul>

<h2>Where to go from here</h2>
<p>
    Obviously, the only way to fully understand testing is to do it. There are
    plenty of resources on unit testing available; the <a
    href="http://www.c2.com/cgi/wiki">c2 wiki</a> has some good resources, and
    many books cover the subject (<em>The Pragmatic Programmer</em> comes to
    mind).
</p>
<p>
    I've read arguments that you should test first the interface. This means
    that you don't throw unexpected arguments at a function/method. Later, after
    the code matures, you either add tests for the unexpected arguments, or you
    add tests for bugs that have been reported. The PHP-QA site recommends
    having a test file for the method, but then also having test files that
    address specific bugs; I have yet to go that far with testing.
</p>
<p>
    Finally, I have read in a number of resources that true Unit Testing should
    start <em>before</em> you start programming. While I understand this
    principle to a degree, I also find that as I code, I discover intricacies in
    the problem that I could not have anticipated earlier... and the solutions
    to those intricacies are often new methods. To that end, I feel that writing
    tests should happen after the first draft of code. Doing so provides the
    first interface with the code, and also helps code cleanup and bug hunting
    before application testing begins. However, this is my humble opinion only.
</p>
<p>
    Happy testing!
</p>
EOT;
$entry->setExtended($extended);

return $entry;