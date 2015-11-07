<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('229-Real-time-ZF-Monitoring-via-Zend-Server');
$entry->setTitle('Real-time ZF Monitoring via Zend Server');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1260478282);
$entry->setUpdated(1261064960);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zend server',
));

$body =<<<'EOT'
<p>
    When keeping tabs on your ZF applications, it's often difficult to separate
    application errors from general PHP errors, and if you aggregate them in the
    same location as your web server errors, this can become more difficult
    still.
</p>

<p>
    Additionally, PHP's error reporting doesn't provide a ton of context, even
    when reporting uncaught exceptions -- typically you'll only get a cryptic
    exception message, and what file and line emitted it.
</p>

<p>
    Zend Server's Monitor extension has some capabilities for providing more
    context, and does much of this by default: request and environment settings
    available when the error was logged, the function name and arguments
    provided, and a full backtrace are available for you to inspect.
    Additionally, the Monitor extension includes an API that allows you to
    trigger custom Monitor events, and you can provide additional context when
    doing so -- such as passing objects or arrays that may help provide context
    when debugging.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p style="text-align: center;"><img src="/uploads/zend.server.event.png" border="0" /></a></p>

<p>
    To tie into this feature, we've developed a new <code>Zend_Log</code>
    writer, <code>Zend_Log_Writer_ZendMonitor</code>, that will emit such custom
    events. In addition, some modifications were made to the
    <code>Zend_Log</code> API to allow passing extra contextual information to
    log writers.
</p>

<p>
    As an example, you could do the following:
</p>

<div class="example"><pre><code class="language-php">
$log = new Zend_Log(new Zend_Log_Writer_ZendMonitor());
$log-&gt;crit('Exception occurred processing login', $e);

// or:
$log-&gt;crit('Exception occurred processing login', array(
    'request'   =&gt; $request, // Request object
    'exception' =&gt; $e,       // Exception
));
</code></pre></div>

<p>
    Zend Server's GUI would then present a tab, "Custom", that includes the
    extra arguments passed; by default, if just an object is passed, the
    information will be returned under the key "info". Passing an associative
    array is incredibly useful, as it allows you to provide detailed contextual
    information.
</p>

<p style="text-align: center;"><img
    src="/uploads/zend.server.custom.info.png" border="0" /></a></p>

<p>
    One use case for this feature is to report application exceptions via the
    <code>ErrorController</code>. This can then provide some great feedback via
    your Zend Server GUI; you can filter based on a "Rule Name" of "Custom
    Event", and further on criteria such as Severity to determine when and why
    your application is hitting the <code>ErrorController</code> -- and
    hopefully reduce such occurrences.
</p>

<p>
    We thought this might make a good default use case, and have provided some
    code generation surrounding it in <code>Zend_Tool</code>. The
    <code>ErrorController</code> will now check to see if a Log resource is
    available, and if so, write to it.
</p>

<p>
    To make this happen, we've also written a new Log bootstrap resource that
    piggy-backs on some additional new functionality: a new
    <code>factory()</code> method in <code>Zend_Log</code>. This allows you to
    create <code>Zend_Log</code> instances from configuration, with one or more
    writers and configured filters. Enabling Zend Monitor logging via the
    <code>ErrorController</code> is now as simple as adding a single line to
    your configuration:
</p>

<div class="example"><pre><code class="language-ini">
resources.log.zendmonitor.writerName = \&quot;ZendMonitor\&quot;
</code></pre></div>

<p>
    Note: you can log to <em>any</em> logger, or multiple loggers if desired.
</p>

<p>
    If, within your controllers, you want to log other events, you can do so by
    simply grabbing the bootstrap object and then the Log resource:
</p>

<div class="example"><pre><code class="language-php">
$bootstrap = $this-&gt;getInvokeArg('bootstrap')
if ($bootstrap-&gt;hasResource('Log')) {
    $log = $bootstrap-&gt;getResource('Log');
    $log-&gt;info(/* ... */);
}
</code></pre></div>

<p>
    This kind of simple integration leads to some fantastic benefits for Zend
    Framework users that are using Zend Server, and it's incredibly cheap to
    implement (the ZendMonitor logger acts as a null logger when the Monitor
    extension is not present). What other uses can you find to put it to?
</p>

<p>
    <em>Note: this functionality is available now via the Zend Framework
        subversion repository, in trunk. It will be made available in a stable
        release with the upcoming 1.10 release.</em>
</p>

<p>
    <strong>Update:</strong> I'd like to point out that the bootstrap resource
    was made possible by contributions of two <a href="http://ibuildings.com">ibuildings</a>
    contributors, Martin Roest and Mark van der Velden, who contributed code that
    makes it possible to instantiate log instances via a new <code>factory()</code> method.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
