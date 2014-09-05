<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('266-Using-the-ZF2-EventManager');
$entry->setTitle('Using the ZF2 EventManager');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1315860313);
$entry->setUpdated(1317931119);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
  3 => 'zf2',
));

$body =<<<'EOT'
<p>
Earlier this year, I <a href="http://weierophinney.net/matthew/archives/251-Aspects,-Filters,-and-Signals,-Oh,-My!.html">wrote about Aspects, Intercepting Filters, Signal Slots, and Events</a>, 
in order to compare these similar approaches to handling both asychronous
programming as well as handling cross-cutting application concerns in a cohesive
way.
</p>

<p>
I took the research I did for that article, and applied it to what was then a
"SignalSlot" implementation within Zend Framework 2, and refactored that work
into a new "EventManager" component. This article is intended to get you up and
running with it.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<style>
.note, div.php {
    margin: 1.5em 0;
    padding: 1em 1.25em 0.75em;
    background: #fafbfc;
    border: 1px solid #aaa;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
    -moz-box-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
}
</style>
<h2>Table of Contents</h2>
<div class="toc">
<ul>
<li><a href="#toc_1.1">Assumptions</a></li>
<li><a href="#toc_1.2">Terminology</a></li>
<li><a href="#toc_1.3">Getting Started</a></li>
<li><a href="#toc_1.4">EventCollection vs EventManager</a></li>
<li><a href="#toc_1.5">Global Static Listeners</a></li>
<li><a href="#toc_1.6">Listener Aggregates</a></li>
<li><a href="#toc_1.7">Introspecting Results</a></li>
<li><a href="#toc_1.8">Short Circuiting Listener Execution</a></li>
<li><a href="#toc_1.9">Keeping it in Order</a></li>
<li><a href="#toc_1.10">Custom Event Objects</a></li>
<li><a href="#toc_1.11">Putting it Together: A Simple Caching Example</a></li>
<li><a href="#toc_2">Fin</a></li>
<li><a href="#toc_3">Updates</a></li>
</ul>
</div>

<h2 id="toc_1.1">Assumptions</h2>

<p>
You must have Zend Framework 2 installed either:
</p>

<ul>
<li>
From a development snapshot (the
   <a href="http://framework.zend.com/zf2/blog/entry/2011-08-30-Dev-status-update">ZF2 blog has the latest links</a> at the time of writing), or
</li>
<li>
From <a href="http://framework.zend.com/wiki/display/ZFDEV2/Zend+Framework+Git+Guide">cloning the ZF2 git repo</a>
</li>
</ul>

<h2 id="toc_1.2">Terminology</h2>

<ul>
<li>
An <strong>Event Manager</strong> is an object that <em>aggregates</em> listeners for one or more
   named events, and which <em>triggers</em> events.
</li>
<li>
A <strong>Listener</strong> is a callback that can react to an <em>event</em>.
</li>
<li>
An <strong>Event</strong> is an action.
</li>
</ul>

<p>
Typically, an <em>event</em> will be modeled as an object, containing metadata
surrounding when and how it was triggered -- what the calling object was, what
parameters are available, etc. Events are also typically <em>named</em>, which can
allow a single <em>listener</em> to branch logic based on the current event (though
purists would argue you should never do this).
</p>

<h2 id="toc_1.3">Getting Started</h2>

<p>
The minimal things necessary to get started are:
</p>

<ul>
<li>
An <code>EventManager</code> instance
</li>
<li>
One or more listeners on one or more events
</li>
<li>
A call to <code>trigger()</code> an event
</li>
</ul>

<p>
So, here we go:
</p>

<div class="example"><pre><code lang="php">
use Zend\EventManager\EventManager;

$events = new EventManager();

$events-&gt;attach('do', function($e) {
    $event  = $e-&gt;getName();
    $params = $e-&gt;getParams();
    printf(
        'Handled event \&quot;%s\&quot;, with parameters %s',
        $event,
        json_encode($params)
    );
});

$params = array('foo' =&gt; 'bar', 'baz' =&gt; 'bat');
$events-&gt;trigger('do', null, $params);
</code></pre></div>

<p>
The above will output:
</p>

<pre>
Handled event "do", with parameters {"foo":"bar","baz":"bat"}
</pre>

<p>
Pretty simple!
</p>

<blockquote class="note">
Note: throughout this post, I use closures as listeners. However, any
valid PHP callback can be attached as a listeners -- PHP function names,
static class methods, object instance methods, or closures. I use closures
within this post simply for illustration and simplicity.
</blockquote>

<p>
But what's that "null", second argument for?
</p>

<p>
Typically, you will compose an <code>EventManager</code> within a class, to allow
triggering actions within methods. The middle argument to <code>trigger()</code> is a
"context" or "target", and in the case described, would be the current object
instance. This gives event listeners access to the calling object, which can
often be useful.
</p>

<div class="example"><pre><code lang="php">
use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Example
{
    protected $events;
    
    public function setEventManager(EventCollection $events)
    {
        $this-&gt;events = $events;
    }
    
    public function events()
    {
        if (!$this-&gt;events) {
            $this-&gt;setEventManager(new EventManager(
                array(__CLASS__, get_called_class())
            );
        }
        return $this-&gt;events;
    }
    
    public function do($foo, $baz)
    {
        $params = compact('foo', 'baz');
        $this-&gt;events()-&gt;trigger(__FUNCTION__, $this, $params);
    }

}

$example = new Example();

$example-&gt;events()-&gt;attach('do', function($e) {
    $event  = $e-&gt;getName();
    $target = get_class($e-&gt;getTarget()); // \&quot;Example\&quot;
    $params = $e-&gt;getParams();
    printf(
        'Handled event \&quot;%s\&quot; on target \&quot;%s\&quot;, with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
});

$example-&gt;do('bar', 'bat');
</code></pre></div>

<p>
The above is basically the same as the first example. The main difference is
that we're now using that middle argument in order to pass a context on to the
listeners. Our listener is now retrieving that (<code>$e-&gt;getTarget()</code>), and doing
something with it.
</p>

<p>
If you're reading this critically, you should have two questions:
</p>

<ul>
<li>
What is this <code>EventCollection</code> bit?
</li>
<li>
What is that argument being passed to the <code>EventManager</code> constructor?
</li>
</ul>

<p>
The answer to the first will lead us into the second.
</p>

<h2 id="toc_1.4">EventCollection vs EventManager</h2>

<p>
One principle we're trying to follow with ZF2 is the <a href="http://en.wikipedia.org/wiki/Liskov_substitution_principle">Liskov Substitution Principle</a>. 
One typical interpretation of this is that strong interfaces should be defined
for any class for which there could be a potential substitution, so that
consumers may use other implementations without worrying about variances in
internal behavior. 
</p>

<p>
As such, we developed an interface, <code>EventCollection</code> that describes an object
capable of aggregating listeners for events, and triggering those events.
<code>EventManager</code> is the standard implementation we provide.
</p>

<h2 id="toc_1.5">Global Static Listeners</h2>

<p>
One aspect that the <code>EventManager</code> implementation provides is an ability to
interface with a <code>StaticEventCollection</code>. This interface allows attaching
listeners not only on events, but on events emitted by specific contexts or
targets. The <code>EventManager</code>, when notifying listeners, will also pull listeners
for the event from the <code>StaticEventCollection</code> object it subscribes to, and
notify them.
</p>

<p>
How does this work, exactly?
</p>

<p>
At the application level, you grab an instance of <code>StaticEventManager</code>, and
start attaching events to it.
</p>

<div class="example"><pre><code lang="php">
use Zend\EventManager\StaticEventManager;

$events = StaticEventManager::getInstance();
$events-&gt;attach('Example', 'do', function($e) {
    $event  = $e-&gt;getName();
    $target = get_class($e-&gt;getTarget()); // \&quot;Example\&quot;
    $params = $e-&gt;getParams();
    printf(
        'Handled event \&quot;%s\&quot; on target \&quot;%s\&quot;, with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
});
</code></pre></div>

<p>
You'll notice it looks almost the same as the original example. The only
difference is there is a new argument at the beginning of the list, to which we
attached the name 'Example'. This code is basically saying, "Listen to the 'do'
event of the 'Example' target, and, when notified, execute this callback."
</p>

<p>
This is finally where the constructor argument of <code>EventManager</code> comes into
play. The constructor allows passing a string, or an array of strings, defining
the name of the context or target the given instance will be interested in. If
an array is given, then any listener on <em>any</em> of the targets given will be
notified. Listeners attached directly to the <code>EventManager</code> will be executed
before any attached statically.
</p>

<p>
So, getting back to our example, let's assume that the above static listener is
registered, and also that the <code>Example</code> class is defined as above. We can then
execute the following:
</p>

<div class="example"><pre><code lang="php">
$example = new Example();
$example-&gt;do('bar', 'bat');
</code></pre></div>

<p>
and expect the following to be echo'd:
</p>

<pre>
Handled event "do" on target "Example", with parameters {"foo":"bar","baz":"bat"}
</pre>

<p>
Now, let's say we extended <code>Example</code> as follows:
</p>

<div class="example"><pre><code lang="php">
class SubExample extends Example
{
}
</code></pre></div>

<p>
One interesting aspect of our <code>EventManager</code> construction is that we defined it
to listen both on <code>__CLASS__</code> and <code>get_called_class()</code>. This means that calling
<code>do()</code> on our <code>SubExample</code> class would also trigger the event we attached
statically! It also means that, if desired, we could attach to specifically
<code>SubExample</code>, and listeners on  simply <code>Example</code> would not be triggered.
</p>

<p>
Finally, the names used as contexts or targets need not be class names; they can
be some name that only has meaning in your application if desired. As an
example, you could have a set of classes that respond to "log" or "cache" -- and
listeners on these would be notified by any of them.
</p>

<p>
At any point, if you do not want the <code>EventManager</code> attached to a class to
notify statically attached listeners, you can simply pass a <code>null</code> value to the
<code>setStaticConnections()</code> method:
</p>

<div class="example"><pre><code lang="php">
$events-&gt;setStaticConnections(null);
</code></pre></div>

<p>
and they will be ignored. If at any point, you want to enable them again, pass
the <code>StaticEventManager</code> instance:
</p>

<div class="example"><pre><code lang="php">
$events-&gt;setStaticConnections(StaticEventManager::getInstance());
</code></pre></div>

<h2 id="toc_1.6">Listener Aggregates</h2>

<p>
Oftentimes, you may want a single class to listen to multiple events, attaching
one or more instance methods as listeners. To make this paradigm easy, you can
simply implement the <code>HandlerAggregate</code> interface. This interface defines two
methods, <code>attach(EventCollection $events)</code> and
<code>detach(EventCollection $events)</code>. Basically, you pass an
        <code>EventManager</code> instance to one and/or the
other, and then it's up to the implementing class to determine what to do.
</p>

<p>
As an example:
</p>

<div class="example"><pre><code lang="php">
use Zend\EventManager\Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\HandlerAggregate,
    Zend\Log\Logger;

class LogEvents implements HandlerAggregate
{
    protected $handlers = array();
    protected $log;

    public function __construct(Logger $log)
    {
        $this-&gt;log = $log;
    }

    public function attach(EventCollection $events)
    {
        $this-&gt;handlers[] = $events-&gt;attach('do', array($this, 'log'));
        $this-&gt;handlers[] = $events-&gt;attach('doSomethingElse', array($this, 'log'));
    }
    
    public function detach(EventCollection $events)
    {
        foreach ($this-&gt;handlers as $key =&gt; $handler) {
            $events-&gt;detach($handler);
            unset($this-&gt;handlers[$key];
        }
        $this-&gt;handlers = array();
    }

    public function log(Event $e)
    {
        $event  = $e-&gt;getName();
        $params = $e-&gt;getParams();
        $log-&gt;info(sprintf('%s: %s', $event, json_encode($params)));
    }
}
</code></pre></div>

<p>
You would then attach it as follows:
</p>

<div class="example"><pre><code lang="php">
$doLog = new LogEvents($logger);
$events-&gt;attachAggregate($doLog);
</code></pre></div>

<p>
and any events it handles would then be notified when they are triggered. This
allows you to have stateful event listeners.
</p>

<p>
You'll notice the <code>detach()</code> method implementation. Just like <code>attach()</code>, it
accepts an <code>EventManager</code>, and then calls detach for each handler it has
aggregated. This is possible because <code>EventManager::attach()</code> returns an object representing
the listener -- which we've aggregated within our aggregate's <code>attach()</code> method
previously.
</p>

<h2 id="toc_1.7">Introspecting Results</h2>

<p>
Sometimes you'll want to know what your listeners returned. One thing to
remember is that you may have multiple listeners on the same event; the
interface for results must be consistent regardless of the number of listeners.
</p>

<p>
The <code>EventManager</code> implementation by default returns a <code>ResponseCollection</code>
object. This class extends PHP's <code>SplStack</code>, allowing you to loop through
responses in reverse order (since the last one executed is likely the one you're
most interested in). It also implements the following methods:
</p>

<ul>
<li>
<code>first()</code> will retrieve the first result received
</li>
<li>
<code>last()</code> will retrieve the last result received
</li>
<li>
<code>contains($value)</code> allows you to test all values to see if a given one was
   received, and returns simply a boolean true if found, and false if not.
</li>
</ul>

<p>
Typically, you should not worry about the return values from events, as the
object triggering the event shouldn't really have much insight into what
listeners are attached. However, sometimes you may want to short-circuit
execution if interesting results are obtained.
</p>

<h2 id="toc_1.8">Short Circuiting Listener Execution</h2>

<p>
You may want to short-ciruit execution if a particular result is obtained, or if
a listener determines that something is wrong, or that it can return something
quicker than the target.
</p>

<p>
As examples, one rationale for adding an <code>EventManager</code> is as a caching
mechanism. You can trigger one event early in the method, returning if a cache
is found, and trigger another event late in the method, seeding the cache. 
</p>

<p>
The <code>EventManager</code> component offers two ways to handle this. The
first is to pass a callback as the last argument to <code>trigger()</code>;
callback; if that callback returns a boolean true, execution is halted.
</p>

<p>
Here's an example:
</p>

<div class="example"><pre><code lang="php">
    public function someExpensiveCall($criteria1, $criteria2)
    {
        $params  = compact('criteria1', 'criteria2');
        $results = $this-&gt;events()-&gt;trigger(__FUNCTION__, $this, $params, function ($r) {
            return ($r instanceof SomeResultClass);
        });
        if ($results-&gt;stopped()) {
            return $results-&gt;last();
        }
        
        // ... do some work ...
    }
</code></pre></div>

<p>
With this paradigm, we know that the likely reason of execution halting is due
to the last result meeting the test callback criteria; as such, we simply return
that last result.
</p>

<p>
The other way to halt execution is within a listener, acting on the <code>Event</code>
object it receives. In this case, the listener calls <code>stopPropagation(true)</code>,
and the <code>EventManager</code> will then return without notifying any additional
listeners.
</p>

<div class="example"><pre><code lang="php">
$events-&gt;attach('do', function ($e) {
    $e-&gt;stopPropagation();
    return new SomeResultClass();
});
</code></pre></div>

<p>
This, of course, raises some ambiguity when using the <code>trigger</code> paradigm,
as you can no longer be certain that the last result meets the criteria it's
searching on. As such, my recommendation is you use one approach or the other.
</p>

<h2 id="toc_1.9">Keeping it in Order</h2>

<p>
On occasion, you may be concerned about the order in which listeners execute. As
an example, you may want to do any logging early, to ensure that if
short-circuiting occurs, you've logged; or if implementing a cache, you may want
to return early if a cache hit is found, and execute late when saving to a
cache.
</p>

<p>
Each of <code>EventManager::attach()</code> and <code>StaticEventManager::attach()</code> accept one
additional argument, a <em>priority</em>. By default, if this is omitted, listeners get
a priority of 1, and are executed in the order in which they are attached. If
you provide a priority value, you can influence order of execution. Higher
priority values execute earlier, while lower (negative) values execute later.
</p>

<p>
To borrow an example from earlier:
</p>

<div class="example"><pre><code lang="php">
$priority = 100;
$events-&gt;attach('Example', 'do', function($e) {
    $event  = $e-&gt;getName();
    $target = get_class($e-&gt;getTarget()); // \&quot;Example\&quot;
    $params = $e-&gt;getParams();
    printf(
        'Handled event \&quot;%s\&quot; on target \&quot;%s\&quot;, with parameters %s',
        $event,
        $target,
        json_encode($params)
    );
}, $priority);
</code></pre></div>

<p>
This would execute with <em>high priority</em>, meaning it would execute early. If we
changed <code>$priority</code> to <code>-100</code>, it would execute with <em>low priority</em>, executing
late.
</p>

<p>
While you can't necessarily know all the listeners attached, chances are you can
make adequate guesses when necessary in order to set appropriate priority
values. My advice is to avoid setting a priority value unless absolutely
necessary.
</p>

<h2 id="toc_1.10">Custom Event Objects</h2>

<p>
    Hopefully some of you have been wondering, "where and when is the Event
    object created"? In all of the examples above, it's created based on the
    arguments passed to <code>trigger()</code> -- the event name, target, and
    parameters. Sometimes, however, you may want greater control over the
    object, however.
</p>

<p>
    As an example, as we've been developing the ZF2 MVC layer, we've been adding
    event awareness to several of the core MVC components. One thing that looks
    like a code smell is when you have code like this:
</p>

<div class="example"><pre><code lang="php">
$routeMatch = $e-&gt;getParam('route-match', false);
if (!$routeMatch) {
    // Oh noes! we cannot do our work! whatever shall we do?!?!?!
}
</code></pre></div>

<p>
    The problems with this are several. First, relying on string keys is going
    to very quickly run into problems -- typos when setting or retrieving the
    argument can lead to hard to debug situations. Second, we now have a
    documentation issue; how do we document expected arguments? how do we
    document what we're shoving into the event. Third, as a side effect, we
    can't use IDE or editor hinting support -- string keys give these tools
    nothing to work with.
</p>

<p>
    Similarly, we found ourselves writing some wierd hacks around how we
    represent a computational result of a method when triggering an event. As an
    example:
</p>

<div class="example"><pre><code lang="php">
// in the method:
$params['__RESULT'] = $computedResult;
$events-&gt;trigger(__FUNCTION__ . '.post', $this, $params);

// in the listener:
$result = $e-&gt;getParam('__RESULT__');
if (!$result) {
    // Oh noes! we cannot do our work! whatever shall we do?!?!?!
}
</code></pre></div>

<p>
    Sure, that key may be unique, but it suffers from a lot of the same issues.
</p>

<p>
    So, the solution is to create custom events. As an example, we have a custom
    "MvcEvent" in the ZF2 MVC layer. This event composes a router, route match
    object, request and response objects, and also a result. We end up with code
    like this in our listeners:
</p>

<div class="example"><pre><code lang="php">
$response = $e-&gt;getResponse();
$result   = $e-&gt;getResult();
if (is_string($result)) {
    $content = $view-&gt;render('layout.phtml', array('content' =&gt; $result));
    $response-&gt;setContent($content);
}
</code></pre></div>

<p>
    But how do we use this custom event? Simple: <code>trigger()</code> can
    accept an event object instead of any of the event name, target, or params
    arguments.
</p>

<div class="example"><pre><code lang="php">
$event = new CustomEvent();
$event-&gt;setSomeKey($value);

// Injected with event name and target:
$events-&gt;trigger('foo', $this, $event);

// Injected with event name:
$event-&gt;setTarget($this);
$events-&gt;trigger('foo', $event);

// Fully encapsulates all necessary properties:
$event-&gt;setName('foo');
$event-&gt;setTarget($this);
$events-&gt;trigger($event);

// Passing a callback following the event object works for 
// short-circuiting, too.
$results = $events-&gt;trigger('foo', $this, $event, $callback);
</code></pre></div>

<p>
    This is a really powerful technique for domain-specific event systems, and
    definitely worth experimenting with.
</p>

<h2 id="toc_1.11">Putting it Together: A Simple Caching Example</h2>

<p>
In the previous section, I indicated that short-circuiting is a way to
potentially implement a caching solution. Let's create a full example.
</p>

<p>
First, let's define a method that could use caching. You'll note that in most of
the examples, I've used <code>__FUNCTION__</code> as the event name; this is a good
practice, as it makes it simple to create a macro for triggering events, as well
as helps to keep event names unique (as they're usually within the context of
the triggering class). However, in the case of a caching example, this would
lead to identical events being triggered. As such, I recommend postfixing the
event name with semantic names: "do.pre", "do.post", "do.error", etc. I'll use
that convention in this example.
</p>

<p>
Additionally, you'll notice that the <code>$params</code> I pass to the event is usually
the list of parameters passed to the method. This is because those are often not
stored in the object, and also to ensure the listeners have the exact same
context as the calling method. But it raises an interesting problem in this
example: what name do we give the <em>result</em> of the method? I've standardized on
<code>__RESULT__</code>, as double-underscored variables are typically reserved for the
sytem. If you have better suggestions, I'd love to hear them!
</p>

<p>
Here's what the method will look like:
</p>

<div class="example"><pre><code lang="php">
    public function someExpensiveCall($criteria1, $criteria2)
    {
        $params  = compact('criteria1', 'criteria2');
        $results = $this-&gt;events()-&gt;trigger(__FUNCTION__ . '.pre', $this, $params, function ($r) {
            return ($r instanceof SomeResultClass);
        });
        if ($results-&gt;stopped()) {
            return $results-&gt;last();
        }
        
        // ... do some work ...
        
        $params['__RESULT__'] = $calculatedResult;
        $this-&gt;events()-&gt;trigger(__FUNCTION__ . '.post', $this, $params);
        return $calculatedResult;
    }
</code></pre></div>

<p>
Now, to provide some caching listeners. We'll need to attach to each of the
'someExpensiveCall.pre' and 'someExpensiveCall.post' methods. In the former
case, if a cache hit is detected, we return it, and move on. In the latter, we
store the value in the cache.
</p>

<p>
We'll assume <code>$cache</code> is defined, and follows the paradigms of <code>Zend_Cache</code>.
We'll want to return <em>early</em> if a hit is detected, and execute <em>late</em> when
saving a cache (in case the result is modified by another listener). As such,
we'll set the 'someExpensiveCall.pre' listener to execute with priority <code>100</code>,
and the 'someExpensiveCall.post' listener to execute with priority <code>-100</code>.
</p>

<div class="example"><pre><code lang="php">
$events-&gt;attach('someExpensiveCall.pre', function($e) use ($cache) {
    $params = $e-&gt;getParams();
    $key    = md5(json_encode($params));
    $hit    = $cache-&gt;load($key);
    return $hit;
}, 100);

$events-&gt;attach('someExpensiveCall.post', function($e) use ($cache) {
    $params = $e-&gt;getParams();
    $result = $params['__RESULT__'];
    unset($params['__RESULT__']);
    $key    = md5(json_encode($params));
    $cache-&gt;save($result, $key);
}, -100);
</code></pre></div>

<blockquote>
Note: the above could have been done within a <code>HandlerAggregate</code>, which
would have allowed keeping the <code>$cache</code> instance as a stateful property,
instead of importing it into closures.
</blockquote>

<p>
Sure, we could probably simply add caching to the object itself - but this
approach allows the same handlers to be attached to multiple events, or to
attach multiple listeners to the same events (e.g. an argument validator, a
logger <em>and</em> a cache manager). The point is that if you design your object with
events in mind, you can easily make it more flexible and extensible, without
requiring developers to <em>actually</em> extend it -- they can simply attach
listeners.
</p>

<h1 id="toc_2">Fin</h1>

<p>
The <code>EventManager</code> is a powerful new addition to Zend Framework. Already, it's
being used with the new MVC prototype to empower some constructs that were
difficult to accomplish well in the version 1.X series -- as an example, I was
able to prototype a <code>ViewRenderer</code> replacement in a handful of lines of code, in
a way that properly accomplishes the separation of concerns one expects from
MVC. I anticipate we'll be using it much, much more often as version 2 matures.
</p>

<p>
There are certainly some rough edges -- the boiler-plate code for
short-circuiting is verbose, and we will likely want to add capabilities such as
event globbing -- but the foundation is solid and mature at this point in time.
Experiment with it, and see what you can accomplish!
</p>

<h1 id="toc_3">Updates</h1>

<ul>
    <li><b>2011-10-06</b>: Removed references to <code>triggerUntil()</code>, as
        that functionality is now incorporated into <code>trigger()</code>.
        Added section on <a href="#toc_1.10">Custom Event Objects</a>.
    </li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;