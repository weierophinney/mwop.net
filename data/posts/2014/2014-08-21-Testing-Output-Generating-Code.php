<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-08-11-testing-output-generating-code');
$entry->setTitle('Testing Code That Emits Output');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-08-21 14:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-08-21 14:30', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'patterns',
  'php',
  'programming',
  'testing',
));

$body =<<<'EOT'
<p>
    Here's the scenario: you have code that will emit headers and content,
    for instance, a front controller. How do you test this?
</p>

<p>
    The answer is remarkably simple, but non-obvious: namespaces.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Prerequisites</h2>

<p>
    For this approach to work, the assumptions are:
</p>

<ul>
    <li>Your code emitting headers and output lives in a namespace other than the global namespace.</li>
</ul>

<p>
    That's it. Considering that most PHP code you grab anymore does this, and
    most coding standards you run across will require this, it's a safe bet
    that you're already ready. If you're not, go refactor your code now, before
    continuing; you'll thank me later.
</p>

<h2>The technique</h2>

<p>
    PHP introduced namespaces in PHP 5.3. Namespaces cover classes, as most of
    us are well aware, but they also cover constants and functions -- a fact
    often overlooked, as before 5.6 (releasing next week!), you cannot import
    them via <kbd>use</kbd> statements!
</p>

<p>
    That does not mean they cannot be defined and used, however -- it just
    means that you need to manually import them, typically via a
    <kbd>require</kbd> or <kbd>require_once</kbd> statement. These are usually
    anathema in libraries, but for testing, they work just fine.
</p>

<p>
    Here's an approach I took recently. I created a file that lives -- this is
    the important bit, so pay attention -- <em>in the same namespace as the
    code emitting headers and output</em>. This file defines several functions
    that live in the global (aka PHP's built-in) namespace, and an accumulator
    static object I can then use in my tests for assertions. Here's what it
    looks like:
</p>

<div class="example"><pre><code class="language-php">
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
</code></pre></div>

<p>
    A few notes:
</p>

<ul>
    <li><kbd>headers_sent()</kbd> always returns <kbd>false</kbd> here, as most
        emitters test for a boolean <kbd>true</kbd> value and bail early when
        that occurs.</li>

    <li>I used <kbd>printf()</kbd> here, as <kbd>echo</kbd> cannot be overridden
        due to being a PHP language construct and not an actual function. As such,
        if you use this technique, you will have to likely alter your emitter
        to call <kbd>printf()</kbd> instead of <kbd>echo</kbd>. The benefits,
        however, are worth it.</li>

    <li>I marked <kbd>Output</kbd> abstract, to prevent instantiation; it should
        only be used statically.</kbd>
</ul>

<p>
    I place the above file within my test suite, usually under a "TestAsset"
    directory adjacent to the test itself; since it contains functions, I'll 
    name the file "Functions.php" as well. This combination typically will 
    prevent it from being autoloaded in any way, as the test directory will 
    often not have autoloading defined, or will be under a separate namespace.
</p>

<p>
    Inside your PHPUnit test suite, then, you would do the following:
</p>

<div class="example"><pre><code class="language-php">
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
</code></pre></div>

<p>
    From here, you test as normal -- but when you invoke methods that will
    cause headers or content to emit, you can now test to see what those
    contain:
</p>

<div class="example"><pre><code class="language-php">
public function testEmitsExpectedHeadersAndContent()
{
    /* ... */

    $this->assertContains('Content-Type: application/json', Output::$headers);
    $json = Output::$body;
    $data = json_decode($json, true);
    $this->assertArrayHasKey('foo', $data);
    $this->assertEquals('bar', $data['foo']);
}
</code></pre></div>

<h2>How it works</h2>

<p>
    Why does this work?
</p>

<p>
    PHP performs some magic when it resolves functions. With classes, it looks
    for a matching class either in the current namespace, or one that was
    imported (and potentially aliased); if a match is not found, it stops, and
    raises an error. With functions, however, it looks first in the current
    namespace, and if it isn't found, then looks in the global namespace. This
    last part is key -- it means that if you redefine a function in the current
    namespace, it will be used in lieu of the original function defined by
    PHP. This also means that any code operating in the same namespace as the 
    function -- even if defined in another file -- will use that function.
</p>

<p>
    This technique just leverages this fact.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
