<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('254-Why-PHP-Namespaces-Matter');
$entry->setTitle('Why PHP Namespaces Matter');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1296829800);
$entry->setUpdated(1297373366);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
You've heard about PHP namespaces by now. Most likely, you've heard about -- and
likely participated in -- the <a href="http://en.wikipedia.org/wiki/Bikeshedding">bikeshedding</a> surrounding the selection of the namespace separator. 
</p>

<p>
Regardless of your thoughts on the namespace separator, or how namespaces may or
may not work in other languages, I submit to you several reasons for why I think
namespaces in PHP are a positive addition to the language.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Code Organization</h2>

<p>
Prior to PHP 5.3, we've had a number of standards surrounding how to name
classes and where to put the class files in the filesystem. These range from
completely arbitrary, to conventions-based ("abcSomeClass" in
"library/abc/some"), to PEAR-like (1:1 correlation between class name and
filesystem location).
</p>

<p>
While namespaces do not enforce any specific paradigm, they lend themselves to
the PEAR-style conventions. Why?
</p>

<p>
Consider:
</p>

<div class="example"><pre><code class="language-php">
namespace my\Component;

class Gateway {}
</code></pre></div>

<p>
Where would you expect to find this file? Did you say "in
my/Component/Gateway.php"? My guess is that greater than 90% of my readers did.
Why? <strong><em>Because the namespace separator reminds us of the directory separator.</em></strong> Plain and simple. This convention just makes sense.
</p>

<p>
As such, namespaces lend themselves to efficient and simple naming conventions.
</p>

<h2 id="toc_1.2">Interfaces</h2>

<p>
Interfaces are, to my thinking, often underused in PHP. Many will argue, "hey,
they don't do anything, require more files to be loaded, and I can typehint just
as easily on an abstract or concrete class." These are all true. However,
interfaces provide us with a simple representation of the contracts we define
for our applications, and provide us with the blueprints we need for extending
and modifying our systems.
</p>

<p>
One thing I struggled with using pre-PHP 5.3 code was how to name interfaces.
Since we didn't have true namespaces, we (PHP developers, that is) often used
names such as "My_Component_Adapter_Interface". Considering that this becomes
"My\Component\Adapter\Interface" when doing a literal 1:1 transition from
pseudo-namespaces to PHP 5.3 namespaces, I encountered several issues:
</p>

<ul>
<li>
First, due to how the PHP lexer works, you get an <code>E_FATAL</code> due to a
   declaration of "interface Interface".
</li>
<li>
Second, the structure now feels odd: we're ultimately describing an adapter,
   but why would we put that a level deeper in the namespace hierarchy?
</li>
</ul>

<p>
An organization I've found that works looks like the following:
</p>

<pre>
library/
|-- mwop/
|   |-- Component/
|   |   |-- ClassConsumingAdapters.php
|   |   |-- Adapter.php
|   |   |-- Adapter/
|   |   |   |-- AbstractAdapter.php
|   |   |   |-- SomeConcreteAdapter.php
</pre>

<p>
In the above, we are declaring a <code>mwop\Component</code> namespace. In that namespace
live a concrete class that consumes adapters, and the actual adapter interface
itself -- named simply for what it is, an Adapter. This puts the adapter
definition at the same level where it is consumed.
</p>

<p>
Concrete adapters are then in the subnamespace <code>mwop\Component\Adapter</code>. We put
a base implementation in the <code>AbstractAdapter</code> class, and concrete adapters
typically extend this. The abstract adapter declaration looks like the
following:
</p>

<div class="example"><pre><code class="language-php">
namespace mwop\Component\Adapter;

use mwop\Component\Adapter;

abstract class AbstractAdapter implements Adapter
{ ... }
</code></pre></div>

<p>
This looked odd and like it wouldn't work when I first tried it, but it is
indeed legal syntax. What I particularly like about it is that it's clear what
the class <em>is</em> (it's an <em>adapter</em>), and also clear that I'll find sibling
classes within this namespace.
</p>

<p>
In my <code>ClassConsumingAdapters</code>, I only make reference to <code>Adapter</code>s:
</p>

<div class="example"><pre><code class="language-php">
namespace mwop\Component;

class ClassConsumingAdapters
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this-&gt;adapter = $adapter;
    }

    public function doSomething()
    {
        $data = $this-&gt;adapter-&gt;someMethodCall();
        // do some work
        return $data;
    }
}
</code></pre></div>

<p>
I'm simply worried about having an adapter, and consuming it, not the specific
implementation -- which is what programming with interfaces is about. Having the
interface at the same level makes the code eminently readable and
comprehensible.
</p>

<h2 id="toc_1.3">Readability</h2>

<p>
One argument for having namespaces in the first place was code readability.
Admittedly, this was mainly coming from those of us in the PEAR camp, where we
were trying to organize code semantically into hierarchies and dependencies, and
ending up with long names like <code>Foo_Component_Decorator_View_Helper</code> -- when
what we really meant was "a helper object". However, due to the use of
pseudo-namespaces to organize our code, and the fact that we could only utilize
class names, we were stuck with verbosity.
</p>

<p>
With namespaces, we have two tools at our disposal.
</p>

<p>
First, namespaces themselves. If we're writing new code, we can create
namespaces, and immediately all code inside our namespace is available, without
needing to prefix at all. An example of that is above, where the
<code>ClassConsumingAdapters</code> simply references <code>Adapter</code> -- since they're in the
same namespace, no prefixing is necessary.
</p>

<p>
Our second tool is the ability to import and alias. As an example, let's
consider this:
</p>

<pre>
library/
|-- mwop/
|   `-- Component/
|      |-- ClassConsumingAdapters.php
|      |-- Adapter.php
|      `-- Adapter/
|          |-- AbstractAdapter.php
|          `-- SomeConcreteAdapter.php
|-- Zend/
|   `-- EventManager/
|      |-- EventCollection.php
|      |-- EventManager.php
|      `-- StaticEventManager.php
</pre>

<p>
Let's say that <code>ClassConsumingAdapters</code> wants to utilize the new
<code>Zend\EventManager</code> component. There are several ways this can be done. First,
it could simply use global resolution:
</p>

<div class="example"><pre><code class="language-php">
namespace mwop\Component;

class ClassConsumingAdapters
{
    protected $events;

    public function events(\Zend\EventManager\EventCollection $events = null)
    {
        if (null !== $events) {
            $this-&gt;events = $events;
        } elseif (null === $this-&gt;events) {
            $this-&gt;events = new \Zend\EventManager\EventManager(__CLASS__);
        }
        return $this-&gt;events;
    }
}
</code></pre></div>

<p>
That's pretty ugly, and arguably worse than pre-namespace code. So, let's try
<em>importing</em> some classes and interfaces. In PHP, we use the <code>use</code> keyword to
import classes into the current scope:
</p>

<div class="example"><pre><code class="language-php">
namespace mwop\Component;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class ClassConsumingAdapters
{
    protected $events;

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this-&gt;events = $events;
        } elseif (null === $this-&gt;events) {
            $this-&gt;events = new EventManager(__CLASS__);
        }
        return $this-&gt;events;
    }
}
</code></pre></div>

<p>
That's a bit easier to read! We now have references that better indicate the
purpose of the classes we're using, which makes comprehension of what we're
doing easier.
</p>

<p>
The third option is to <em>alias</em>. Aliasing is something you do when importing a
class; at the time you import, you indicate an alternate name by which you want
to refer to the class or interface. An illustration will help:
</p>

<div class="example"><pre><code class="language-php">
namespace mwop\Component;

use Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager;

class ClassConsumingAdapters
{
    protected $events;

    public function events(Events $events = null)
    {
        if (null !== $events) {
            $this-&gt;events = $events;
        } elseif (null === $this-&gt;events) {
            $this-&gt;events = new EventManager(__CLASS__);
        }
        return $this-&gt;events;
    }
}
</code></pre></div>

<p>
In the above example, we're <em>aliasing</em> <code>Zend\EventManager\EventCollection</code> to
simply <code>Events</code> (plural often connotes a collection).
</p>

<p>
Now that we know about aliasing, here's a tip: you don't <em>need</em> to rewrite all
that nice, clean, pre-PHP 5.3 library code to make use of namespaces! You can
simply use aliasing in your consumer code:
</p>

<div class="example"><pre><code class="language-php">
namespace Application;

use Zend_Controller_Action as ActionController;

class FooController extends ActionController
{
}
</code></pre></div>

<p>
(I've been using the above trick in my presentations since last spring, as it
often helps make the code samples more readable!)
</p>

<h2 id="toc_1.4">Identifying Dependencies</h2>

<p>
Now that you know about importing and aliasing, there's another point to bring
up: importing helps you make dependencies explicit.
</p>

<p>
Declaring an import statement does not immediately load a class -- it simply
hints to the PHP interpreter as to how to understand certain symbols when it
encounters them. 
</p>

<p>
In fact, you can import and alias not just classes and interfaces, but
namespaces themselves -- though when importing namespaces, you then prefix
classes under that namespace:
</p>

<div class="example"><pre><code class="language-php">
namespace Application;

use Foo\Exception;

// ...
// Foo\Exception\InvalidArgumentException:
throw Exception\InvalidArgumentException(); 
</code></pre></div>

<p>
A side effect of importing is that you're documenting at a code level your dependencies on components from other namespaces. This allows you to do things such as use static analysis tools to identify dependencies. As an example, I've <a href="https://github.com/weierophinney/zf-examples/tree/master/zf-utils">created a scanDeps tool</a> that will analyze a tree of PHP files for import statements, and create a list of unique components referenced.
</p>

<p>
This sort of automation is invaluable; it can help you identify what tests you
may want to run when changing code in a given component, allow you to create
PEAR packages of your code that reference the appropriate dependencies, and
more.
</p>

<h2 id="toc_1.5">Conclusion</h2>

<p>
Organization. Readability. Dependency tracking. All of these are worthy goals in
and of themselves, and together, they're impressive. And all from one feature:
namespaces.
</p>

<p>
Sure, we can all debate the namespace separator. At the end of the day, however,
the point is: what do namespaces give me, regardless of the syntax? Hopefully,
my arguments have convinced you of their general utility to PHP development.
</p>

<p>
If you haven't played with namespaces yet, install PHP 5.3 if you haven't and
start experimenting -- and let me know what usage patterns <em>you</em> find!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
