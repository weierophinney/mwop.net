<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('263-Proxies-in-PHP');
$entry->setTitle('Proxies in PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1309889100);
$entry->setUpdated(1310046695);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
A number of programming design patterns look very similar. One of these is the
<em>Proxy</em> pattern, which, at first glance, can look like a number of others:
<em>Decorator</em>, <em>Flyweight</em>, even plain old object extension. However, it has its
own niche, and it can provide some incredible flexibility for a number of
programming scenarios.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
Of the other patterns mentioned, the one closest to the <em>Proxy</em> is the
<em>Decorator</em>. In the case of a <em>Decorator</em>, the focus is on <em>adding</em>
functionality to an existing object -- for instance, adding methods, processing
input before delegating to the target object, or filtering the return of a
method from a target object.
</p>

<p>
The <em>Proxy</em> differentiates itself as it typically acts as a stand-in for an
existing object. Classically, the Proxy object has three typical use cases:
</p>

<ul>
<li>
Acting as a placeholder for "expensive to create" objects, lazy-loading them
   only on first access (this is similar to the <em>Flyweight</em> pattern).
</li>
<li>
Local object representation of remote system processes.
</li>
<li>
Consuming and controlling access to another object.
</li>
</ul>

<p>
Typically, I've considered <em>Proxy</em> objects only in light of the first two
points. Recently, however, <a href="http://ralphschindler.com/">Ralph</a> pointed me to the
last definition, and showed how powerful it can be.
</p>
      
<h2 id="toc_1.1">Accessing the Invisible</h2>

<h3 id="toc_1.1.1">The Problem</h3>

<p>
Often we end up writing both setters and getters for class metadata that we
never truly intend to expose; we're more interested in how the object consumes
that information via other methods. As an example, we may want to write a value
object that accumulates data, and then later do something with that value
object. The getters really have no purpose outside the primary use case -- even
in testing, we're really mostly interested in what the object <em>does</em> with those
values, not that it's storing them. Why waste time writing methods that will,
quite simply, never be used in practice?
</p>

<p>
In this scenario, the developer works directly with these objects, populating
and manipulating them, passing them around to other objects, etc., but never
introspecting them.
</p>

<p>
Later, we may want to re-use the same, fully-configured object, but introspect
it in order to process it in different ways. Alternately, we may want an empty
value object, and use a proxy in order to populate it before returning it to the
user (this is in fact one way in which Doctrine2 currently works with entities). 
So, how do we go about this?
</p>

<p>
The first option seems obvious: extend the original class. However, this fails
one of the criteria: we want to <em>re-use</em> an existing object instance, and work
with an instance of the original class.
</p>

<p>
The next common option would be to use <em>Decoration</em>. However, decoration only
gives us access to public members -- you're simply passing the original object
in, warts and all, so the same visibility rules apply.
</p>

<p>
So, how do we access those non-public members?
</p>

<h3 id="toc_1.1.2">The Solution</h3>
      
<p>
Consider the common conception of how visibility works in PHP (this is how
<em>I</em> thought it worked, too, until recently):
</p>

<div class="example"><pre><code class="language-php">
class SomeObject
{
    protected $message;
    
    public function __construct($message)
    {
        $this-&gt;message = $message;
    }
    
    protected function doSomething()
    {
        return $this-&gt;message;
    }
}

$o = new SomeObject('foo bar');
</code></pre></div>
      
<p>
In the above example, how would we call <code>doSomething()</code>? or access the
<code>$message</code> property? We couldn't. 
</p>

<p>
Enter the Proxy pattern.
</p>

<p>
Traditional proxies have you (a) implement the same interface as the proxied
class, and (b) pass the proxied class to the constructor of the proxy or (c)
have the proxy manage the proxied class instance entirely. In the case of PHP,
since you cannot cast to an interface, you miss out on a lot of what Java and
.NET can offer. So, we have to go a different route that looks convoluted at
first, but once you understand a key point about PHP, it makes sense. That
point?
</p>
<blockquote style="font-weight:bold; font-size: 1.2em; font-style: italic">
PHP's visibility applies at the class-level, not instance-level.
</b></blockquote>
    
<p>
Here we go:
</p>

<div class="example"><pre><code class="language-php">
class Proxy extends SomeObject 
{
    protected $proxied;

    public function __construct(SomeObject $o)
    {
        $this-&gt;proxied = $o;
    }
    
    public function doSomething()
    {
        return ucwords($this-&gt;proxied-&gt;message);
    }
}

$o = new SomeObject('foo bar');
$p = new Proxy($o);
$p-&gt;doSomething();
</code></pre></div>

<p>
My first guess when looking at this is that it wouldn't work -- the <code>$proxied</code>
property refers to an instance of <code>SomeObject</code>, and <code>SomeObject</code>'s <code>$message</code>
property is protected -- <code>$this-&gt;proxied-&gt;message</code> should not be accessible. But
let's go back to my earlier assertion: visibility applies to the <em>class</em>, not
<em>instances</em>. In our case, <code>Proxy</code> is extending <code>SomeObject</code>, so it shares
visibility. This means that as it operates on other instances deriving from
<code>SomeObject</code>, it has access to its members. 
</p>
<blockquote>
One note: Because we're extending a class, normal visibility rules still
apply: you cannot access <em>private</em> members from the class being extended.
This is another reason why I continue to assert that frameworks and
libraries should only in very exceptional circumstances declare private
visibility.
</blockquote>
    
<h2 id="toc_1.2">Gotchas</h2>

<ul>
<li>
You need to override any method that affects your workflow. As an example,
   let's consider the following class definition:
<div class="example"><pre><code class="language-php">
class SomeObject 
{
    public function foo()
    {
        $value = $this-&gt;bar() . $this-&gt;baz();
        return $value;
    }
    
    protected function bar()
    {
        return __CLASS__;
    }
    
    protected function baz()
    {
        return __FUNCTION__; 
    }
}
</code></pre></div>
   If you wanted to override <code>bar()</code>, but have it continue to aggregate its
   return value from the <code>foo()</code> method, you'd need to override <em>both</em> these
   methods as follows:
<div class="example"><pre><code class="language-php">
class Proxy extends SomeObject
{
    protected $proxy;
    
    public function __construct(SomeObject $o)
    {
        $this-&gt;proxy = $o;
    }

    public function foo()
    {
        $value = $this-&gt;bar() . $this-&gt;proxy-&gt;baz();
        return $value;
    }
    
    protected function bar()
    {
        return __FUNCTION__;
    }
}
</code></pre></div>
</li>
<li>
Copy over any properties you may be accessing in your overridden methods,
   or accessed in methods you may call.
   <br /><br />
   As an example, consider a class you're proxying where you want want to call a
   method that, in the proxied object, refers to an instance property.
<div class="example"><pre><code class="language-php">
class Adapter
{
    protected $name;

    public function __construct($name)
    {
        $this-&gt;name = $name;
    }

    public function getName()
    {
        return $this-&gt;name;
    }
}

class SomeObject
{
    protected $adapter;

    public function __construct()
    {
        $this-&gt;adapter = new Adapter(__METHOD__);
    }

    public function execute()
    {
        return $this-&gt;adapter-&gt;getName();
    }
}
</code></pre></div>
   If I want to proxy <code>SomeObject</code> and then call the <code>execute()</code> method, I might
   try the following:
<div class="example"><pre><code class="language-php">
class Proxy extends SomeObject
{
    protected $proxy;
    
    public function __construct(SomeObject $o)
    {
        $this-&gt;proxy = $o;
    }
}

$o = new SomeObject();
$p = new Proxy($o);
echo $p-&gt;execute();
</code></pre></div>
   Try running that code. I'll wait.
   <br /><br />
   If you have error reporting properly configured and <code>display_errors</code>
   enabled, you'll have received a fatal error indicating something about
   being unable to call a member function on a non-object.
   <br /><br />
   What has happened is that the call to <code>execute()</code> is now in the scope of
   the <code>Proxy</code> object... which has no defined <code>$adapter</code> property.
   <br /><br />
   There are two ways around this. First, define the method in your proxy
   object:
<div class="example"><pre><code class="language-php">
class Proxy extends SomeObject
{
    protected $proxy;
    
    public function __construct(SomeObject $o)
    {
        $this-&gt;proxy = $o;
    }

    public function execute()
    {
        return $this-&gt;proxy-&gt;adapter-&gt;getName(); 
    }
}

$o = new SomeObject();
$p = new Proxy($o);
echo $p-&gt;execute();
</code></pre></div>
   Sure, it works... but do you want to do this for every single method in
   your proxied class that you may call?
   <br /><br />
   The better way is to assign any properties from the proxied object directly
   to the proxy object:
<div class="example"><pre><code class="language-php">
class Proxy extends SomeObject
{
    protected $proxy;
    
    public function __construct(SomeObject $o)
    {
        $this-&gt;proxy = $o;
        
        // Assign the adapter instance to this object as well...
        $this-&gt;adapter = $o-&gt;adapter;
    }
}

$o = new SomeObject();
$p = new Proxy($o);
echo $p-&gt;execute();
</code></pre></div>
   Note, you don't need to define those properties; they're defined in
   <code>SomeObject</code> already, and we're still extending <code>SomeObject</code>. As such, now
   that we've assigned the property, the call just works. This is more succinct,
   and can help save some keystrokes later when you override more methods. 
</li>
</ul>

<h2 id="toc_1.3">Summary</h2>

<p>
The Proxy pattern is a fantastic way to re-use <em>object instances</em> to which you
want visibility into protected attributes or methods, and particularly when you
may not have control over the object lifecycle of the various objects it
composes. 
</p>

<p>
Some good uses cases include unit testing (proxies deliver a nice way to test
internal state of an object without needing to expose that state), object
persistence strategies (ala Doctrine 2), and much more.
</p>

<h2 id="toc_1.4">Resources</h2>

<p>
There's a ton of information on the Proxy pattern on the intarwebs, but very
little that displays the visibility aspects of it in relation to PHP. One good
resource, however is the Doctrine2 project, which <a href="http://www.doctrine-project.org/docs/orm/2.0/en/reference/configuration.html#proxy-objects">uses proxy objects for a variety of purposes</a>. 
</p>

<p>
We're using it in Zend Framework 2's Dependency Injection system for <a href="https://github.com/zendframework/zf2/blob/master/library/Zend/Di/ServiceLocator/DependencyInjectorProxy.php">generating service locator objects</a> from a configured <code>DependencyInjector</code> instance as well.
</p>

<p>
My main takeaway from learning about the pattern was that it enables me a way to
control access to and/or manipulate internal processes of object members without
requiring consumers of the code to change practices; my code can consume
existing objects to do the work.
</p>

<p>
What uses have <em>you</em> found for proxies? What things could proxies enable for
you?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
