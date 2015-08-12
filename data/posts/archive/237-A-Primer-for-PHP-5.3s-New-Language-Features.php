<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('237-A-Primer-for-PHP-5.3s-New-Language-Features');
$entry->setTitle('A Primer for PHP 5.3\'s New Language Features');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1270566623);
$entry->setUpdated(1271261666);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    For the past month, I've been immersed in PHP 5.3 as I and my team have
    started work on <a href="http://framework.zend.com/">Zend Framework</a> 2.0.
    PHP 5.3 offers a slew of new language features, many of which were developed
    to assist framework and library developers. Most of the time, these features
    are straight-forward, and you can simply use them; in other cases, however,
    we've run into behaviors that were unexpected. This post will detail several
    of these, so <em>you</em> either don't run into the same issues -- or can
    capitalize on some of our discoveries.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Closures, Anonymous Functions, and Lambdas, oh my!</h2>

<p>
    Briefly, these are all synonyms (with slight contextual differences) for a
    single PHP construct, the <a
        href="http://php.net/functions.anonymous">anonymous function</a>:
</p>

<div class="example"><pre><code class="language-php">
$callback = function ($param) {
    // do something
};
</code></pre></div>

<p>
    You can assign an anonymous function to a variable, or pass it in-line as a
    callback argument to a function or method call. The construct makes for some
    really flexible designs, and is particularly useful with the various array
    functions and with <code>preg_replace_callback()</code>. If you see any
    "create_function" constructs in your codebase, go and replace them
    immediately with anonymous functions; not only will they be easier to read
    (escaping code content in <code>create_function()</code> was always a pain),
    but they'll be much faster, and also benefit from opcode caching if
    available.
</p>

<p>
    We discovered one interesting issue, however. PHP does not like serializing
    closures; doing so raises an exception ("Serialization of 'Closure' is not
    allowed"). This has a number of implications:
</p>

<ul>
     <li>If you need to alter the SPL autoloader stack, be careful about using
     closures with it. As an example, our testbed was caching the autoloaders by
     storing the
     return value of <code>spl_autoload_functions()</code>, and then resetting
     it during testing. Unfortunately, if you register a closure with
     <code>spl_autoload_register</code>, you may get an error when you do this.
     <em>(Note: this appears to be fixed with 5.3.2 and up.)</em></li>

     <li>If you are serializing classes that have properties that reference
     closures, you will need to add some logic to <code>__sleep()</code> and
     <code>__wakeup()</code> to ensure those properties are not serialized, and
     to recreate them on deserialization.</li>
</ul>

<p>
     Additionally, even though internally anonymous functions are represented
     via the class <code>Closure</code>, you cannot typehint on that class; the
     only way to test if a variable is a closure is to use
     <code>is_callable()</code>.
</p>

<h2>Invokables</h2>

<p>
    One fun new feature of PHP 5 is the magic method <code>__invoke()</code>,
    which allows you to call an object as if it were a function:
</p>

<div class="example"><pre><code class="language-php">
class Greeting
{
    public function __invoke($name)
    {
        return \&quot;Hello, $name\&quot;;
    }
}

$greeting = new Greeting;
echo $greeting('world'); // \&quot;Hello, world\&quot;
</code></pre></div>

<p>
    Unlike other magic methods, it actually is <em>faster</em> than the
    alternatives. When simply returning a value, it's 25% faster than calling a
    method on the same object; when used with
    <code>call_user_func_array()</code>, it's 30% faster than using a normal,
    array-style callback (e.g., <code>array($o, 'greet')</code> -- even when
    it's proxying to another method! 
</p>

<p>
    So, sounds like a great new feature, right? Yes... but there are some
    things you should know.
</p>

<ul>
    <li>Like closures, you cannot typehint explicitly for
    <code>__invoke()</code>; you have to either use <code>is_callable()</code>
    or create an interface defining it:
<div class="example"><pre><code class="language-php">
interface Filter
{
    public function filter($value);
}

interface CallableFilter
{
    public function __invoke($value);
}

class IntFilter implements Filter, CallableFilter
{
    public function filter($value)
    {
        return (int) $value;
    }

    public function __invoke($value)
    {
        return $this-&gt;filter($value);
    }
}

$filter = new IntFilter;
if ($filter instanceof CallableFilter) {
    // matches
}
</code></pre></div>
    </li>

    <li>Be careful about using objects implementing <code>__invoke()</code> as
    object properties; they don't do what you expect. For instance, consider the
    following:

<div class="example"><pre><code class="language-php">
class Foo
{
    public function __invoke()
    {
        return 'foo';
    }
}

class Bar
{
    public $foo;

    public function __construct()
    {
        $this-&gt;foo = new Foo;
    }
}

$bar = new Bar;
echo $bar-&gt;foo();
</code></pre></div>
    You might expect this to echo "foo" -- but it won't. Instead, it'll raise an
    <code>E_FATAL</code>, claiming "Call to undefined method Bar::foo()". If you
    want to execute the property, you have to assign it to a temporary variable
    first, or explicitly call <code>__invoke()</code>:
<div class="example"><pre><code class="language-php">
$foo = $bar-&gt;foo;
echo $foo();

// or:

$bar-&gt;foo-&gt;__invoke();
</code></pre></div>
    </li>
</ul>

<h2>Namespacing for fun and profit</h2>

<p>
    Please put aside your opinions on the choice of namespace separator in PHP;
    it's water under the bridge at this point, and there were good technical
    reasons for the choice. We have an implementation, so let's use it.
</p>

<p>
    First off, you declare your namespace at the top of your file:
</p>

<div class="example"><pre><code class="language-php">
namespace Zend\Filter;
</code></pre></div>

<p>
    Or you can have several namespaces in the same file, as long as you have no
    loose code:
</p>

<div class="example"><pre><code class="language-php">
namespace Zend\Filter;
// some namespaced code here...

namespace Zend\Validator;
// some namespaced code here...
</code></pre></div>

<p>
    While the above is valid, the PHP manual recommends using braces if you're
    using multiple namespaces in a single file:
</p>

<div class="example"><pre><code class="language-php">
namespace Zend\Filter 
{
    // some namespaced code here...
}

namespace Zend\Validator 
{
    // some namespaced code here...
}
</code></pre></div>

<p>
    You can <em>import</em> code from other namespaces using the
    <code>use</code> construct. This construct also allows you to <em>alias</em>
    the namespace (or class, constant, or function within the namespace) using
    the <code>as</code> modifier:
</p>

<div class="example"><pre><code class="language-php">
namespace Foo;
use Zend\Filter;
use Zend\Validator\Int as IntValidator;

$validator = new IntValidator;  // Zend\Validator\Int
if ($validator-&gt;isValid($foo) {
    $filter = new Filter\Int(); // Zend\Filter\Int
    echo $filter($foo);
}
</code></pre></div>

<p>
   Some quick rules about namespaces:
</p>

<ul>
    <li><em>Fully qualified namespaces</em> (FQN) begin with a namespace
    separator ("\"). Classes, functions, constants, and static members
    referenced using a FQN will always resolve.</li>

    <li>The namespace declaration is always considered fully qualified, and
    should <em>not</em> be prefixed with a namespace separator.</li>

    <li>Namespaces referenced in a <code>use</code> statement are always
    considered fully qualfied; you <em>can</em> prefix with a namespace
    separator, but it's not necessary.</li>

    <li>When referring to namespaced classes within a namespace, be aware of the
    origin: if you don't fully qualify the namespace, the assumptions will be:

    <ul>
        <li>A sub-namespace of the current namespace</li>
        <li>A reference to one of the aliases defined when importing</li>
    </ul>

    For example, consider the following code:

<div class="example"><pre><code class="language-php">
namespace Foo;
use Zend\Filter; // imports are always considered FQN

$foo       = new Bar\Baz;             // actual; Foo\Bar\Baz
$filter    = new Filter\Int;          // actual; Zend\Filter\Int
$validator = new Zend\Validator\Int;  // actual: Foo\Zend\Validator\Int
$validator = new \Zend\Validator\Int; // actual: Zend\Validator\Int
</code></pre></div>
    </li>
</ul>

<p>
    One discovery we made was that you can have a namespace that shares the same
    name as an interface of class. As an example:
</p>

<div class="example"><pre><code class="language-php">
namespace Foo 
{
    interface Adapter 
    {
        // definition here...
    }
}

namespace Foo\Adapter
{
    use Foo\Adapter as FooAdapter;

    class Concrete implements FooAdapter
    {
        // ...
    }
}
</code></pre></div>

<p>
    This discovery has allowed us to define more "top-level" interfaces within
    components, with concrete implementations in a namespace matching the
    interface. This reduces some verbiage, defines a better class hierarchy, and
    makes the code relations more semantic.
</p>

<p>
    Finally, we've found that one huge benefit to namespaces is when unit
    testing: we can define a separate namespace for unit tests, as well as
    separate namespaces for each component. If we use these namespaces for test
    artifacts -- classes and mock adapters consumed by the unit tests -- we
    ensure that each test suite is fully encapsulated. This has led to fewer
    issues with naming collisions.
</p>

<h2>In closing...</h2>

<p>
    PHP 5.3 offers a ton of new features -- those I go through here are but some
    of the more prominent ones. If you haven't started hacking with 5.3, you
    should -- it's definitely the future of PHP, and you'll be seeing an
    increasing number of libraries and frameworks using it.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
