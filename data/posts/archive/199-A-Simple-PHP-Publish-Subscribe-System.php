<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('199-A-Simple-PHP-Publish-Subscribe-System');
$entry->setTitle('A Simple PHP Publish-Subscribe System');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1229354760);
$entry->setUpdated(1230725135);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'dojo',
));

$body =<<<'EOT'
<p>
    I've been playing a lot with <a href="http://dojotoolkit.org/">Dojo</a>
    lately, and have been very impressed by its elegant publish-subscribe
    system. Basically, any object can publish an event, and any other object can
    subscribe to it. This creates an incredibly flexible notification
    architecture that's completely opt-in.
</p>

<p>
    The system has elements of Aspect Oriented Programming (AOP), as well as the
    Observer pattern. Its power, however, is in the fact that an individual
    object does not need to implement any specific interface in order to act as
    either a Subject or an Observer; the system is globally available.
</p>

<p>
    Being a developer who recognizes good ideas when he sees them, of course I
    decided to port the idea to PHP. You can see the results <a href="http://github.com/weierophinney/phly/tree/master/Phly_PubSub">on github</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Usage is incredibly simple: an object publishes an event, which triggers all
    subscribers.
</p>

<p>
    Probably the most illustrative solution would be for optionally logging. Say
    for instance that you create a logger instance in your application
    bootstrap; you could then subscribe it to all "log" events:
</p>

<div class="example"><pre><code lang="php">
$log = new Zend_Log(new Zend_Log_Writer_Stream('/tmp/application.log'));
Phly_PubSub::subscribe('log', $log, 'info');
</code></pre></div>

<p>
    Then, in your code, whenever you might want to log some information, simply
    publish to the "log" topic:
</p>

<div class="example"><pre><code lang="php">
Phly_PubSub::publish('log', 'Log message...');
</code></pre></div>

<p>
    In production, you could simply comment out the log definition and
    subscription, disabling logging throughout the application. Events that
    publish to topics without subscribers simply return early -- meaning no
    ramifications for code that uses the system. You could then enable the
    logger at will when you need to debug or determine what events are
    triggering. 
</p>

<p>
    As another example, consider a model that has a <code>save</code> method.
    You may want to log the data sent to it, as well as the id returned.
    Additionally, you may want to update your search index and caches once the
    item has been saved to your persistence store.
</p>

<p>
    Your model's <code>save</code> method might then look like this:
</p>

<div class="example"><pre><code lang="php">
class Foo
{
    public function save(array $data)
    {
        Phly_PubSub::publish('Foo::save::start', $data, $this);

        // ...

        Phly_PubSub::publish('Foo::save::end', $id, $this);
        return $id;
    }
}
</code></pre></div>

<p>
    Elsewhere, you may have defined your logger, indexer, and cache. Where those
    are defined, you would tell them what topics you're subscribing each to.
</p>

<div class="example"><pre><code lang="php">
Phly_PubSub::subscribe('Foo::save::start', $logger, 'logSaveData');
Phly_PubSub::subscribe('Foo::save::end', $logger, 'logSaveId');
Phly_PubSub::subscribe('Foo::save::end', $cache, 'updateFooItem');
Phly_PubSub::subscribe('Foo::save::end', $index, 'updateFooItem');
</code></pre></div>

<p>
    The beauty of the approach is the simplicity: Foo doesn't need to implement
    it's own pub/sub interface -- in fact, if Foo already existed in your
    application, you could drop in this functionality trivially. On the other
    side of the coin, if you have no subscribers to the events, there are no
    drawbacks.
</p>

<p>
    Some places it could be improved:
</p>

<ul>
    <li>
        The ability for return values could be useful, to allow interruption of
        method execution or to modify arguments sent by the publisher. However,
        since each topic may have multiple handlers, a simple interface would be
        difficult to achieve.
    </li>
    <li>
        Exception handling. In most cases, you probably don't want method
        execution to halt due to a subscriber raising an exception. However, you
        still need some way to report such errors.
    </li>
</ul>

<p>
    I'm excited to see what uses <em>you</em> may be able to put this to; drop
    me a line if you start using it!
</p>

<p>
    <b>Update (2008-12-30):</b> Based on some of the comments to this post, I
    created <code>Phly_PubSub_Provider</code>, which is a non-static
    implementation that can be attached to individual classes -- basically
    providing a per-object plugin system. Usage is as follows:
</p>

<div class="example"><pre><code lang="php">
class Foo
{
    protected $_plugins;

    public function __construct()
    {
        $this-&gt;_plugins = new Phly_PubSub_Provider();
    }

    public function getPluginProvider()
    {
        return $this-&gt;_plugins;
    }

    public function bar()
    {
        $this-&gt;_plugins-&gt;publish('bar');
    }
}

$foo = new Foo();

// Subscribe echo() to the 'bar' event:
$foo-&gt;getPluginProvider()-&gt;subscribe('bar', 'echo');

$foo-&gt;bar(); // echo's 'bar'
</code></pre></div>
EOT;
$entry->setExtended($extended);

return $entry;