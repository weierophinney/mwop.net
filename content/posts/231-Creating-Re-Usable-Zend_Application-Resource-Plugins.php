<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('231-Creating-Re-Usable-Zend_Application-Resource-Plugins');
$entry->setTitle('Creating Re-Usable Zend_Application Resource Plugins');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1265313312);
$entry->setUpdated(1265641907);
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
    In my <a
        href="/matthew/archives/230-Quick-Start-to-Zend_Application_Bootstrap.html">last
        article</a>, I wrote about how to get started with
    <code>Zend_Application</code>, including some information about how to write
    resource methods, as well as listing available resource plugins. What
    happens when you need a re-usable resource for which there is no existing
    plugin shipped? Why, write your own, of course!
</p>

<p>
    All plugins in Zend Framework follow a <a
        href="http://framework.zend.com/manual/en/learning.plugins.intro.html">common
    pattern</a>. Basically, you group plugins under a common directory, with a
    common class prefix, and then notify the pluggable class of their location.
</p>

<p>
    For this post, let's consider that you may want a resource plugin to do the
    following:
</p>

<ul>
    <li>Set the view doctype</li>
    <li>Set the default page title and title separator</li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Getting Started</h2>

<p>
    First, let's determine the class prefix we want to use. If we follow <a
        href="http://framework.zend.com/manual/en/coding-standard.overview.html">Zend
    Framework Coding Standards</a>, we can leverage autoloading, while
    simultaneously ensuring a common class prefix for our resources.
</p>

<p>
    For the purposes of this exercise, we'll use the class prefix
    <code>Phly_Resource</code>, located in <code>Phly/Resource/</code> on our
    <code>include_path</code>.
</p>

<p>
    We'll call our particular resource "Layouthelpers", with a full class name
    of <code>Phly_Resource_Layouthelpers</code>, and place it in
    <code>Phly/Resource/Layouthelpers.php</code>. It needs to implement
    <code>Zend_Application_Resource_Resource</code>, but it's often even easier
    to extend <code>Zend_Application_Resource_ResourceAbstract</code>. In both
    cases, you need to define an <code>init()</code> method. Let's set up our
    skeleton accordingly:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
    }
}
</code></pre></div>

<h2>On Dependency Tracking</h2>

<p>
    In my previous article, I showed an example of dependency tracking in
    <code>Zend_Application</code>. We will need it in this exercise as well, as
    both of our tasks operate on the view object, which we will retrieve via the
    View resource. 
</p>

<p>
    When creating resource methods directly in your bootstrap, you can simply
    call <code>$this-&gt;getResource($name)</code>. However, within a plugin
    resource class, you need to first get access to the bootstrap object itself
    -- which you can do with the <code>getBootstrap()</code> method.
</p>

<p>
    Let's ensure the View resource is initialized, and retrieve it.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this-&gt;getBootstrap();
        $bootstrap-&gt;bootstrap('View');
        $view = $bootstrap-&gt;getResource('View');

        // ...
    }
}
</code></pre></div>

<h2>Configuring the resource</h2>

<p>
    Now that we've got our view object, we can do some work. Since we want the
    resource to be re-usable, we should likely allow some configuration options.
    <code>Zend_Application_Resource_ResourceAbstract</code> provides some
    boilerplate functionality for doing so.
</p>

<p>
    First, we'll provide some default options via the <code>$_options</code>
    property.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         =&gt; 'XHTML1_STRICT',
        'title'           =&gt; 'Site Title',
        'title_separator' =&gt; ' :: ',
    );

    public function init()
    {
        $bootstrap = $this-&gt;getBootstrap();
        $bootstrap-&gt;bootstrap('View');
        $view = $bootstrap-&gt;getResource('View');

        // ...
    }
}
</code></pre></div>

<p>
    We can then grab options using the <code>getOptions()</code> method.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         =&gt; 'XHTML1_STRICT',
        'title'           =&gt; 'Site Title',
        'title_separator' =&gt; ' :: ',
    );

    public function init()
    {
        $bootstrap = $this-&gt;getBootstrap();
        $bootstrap-&gt;bootstrap('View');
        $view = $bootstrap-&gt;getResource('View');

        $options = $this-&gt;getOptions();
        // ...
    }
}
</code></pre></div>

<p>
    Now, in configuration files, developers can override the defaults:
</p>

<div class="example"><pre><code lang="ini">
[production]
; ...
resources.layouthelpers.doctype = \&quot;HTML5\&quot;
resources.layouthelpers.title = \&quot;My Snazzy New Website\&quot;
resources.layouthelpers.title_separator = \&quot; &amp;emdash; \&quot;
</code></pre></div>

<h2>Doing some work</h2>

<p>
    Now that we have the bits and pieces of naming and configuration out of the
    way, let's do some work:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         =&gt; 'XHTML1_STRICT',
        'title'           =&gt; 'Site Title',
        'title_separator' =&gt; ' :: ',
    );

    public function init()
    {
        $bootstrap = $this-&gt;getBootstrap();
        $bootstrap-&gt;bootstrap('View');
        $view = $bootstrap-&gt;getResource('View');

        $options = $this-&gt;getOptions();
        
        $view-&gt;doctype($options['doctype']);
        $view-&gt;headTitle()-&gt;setSeparator($options['title_separator'])
                          -&gt;append($options['title']);
    }
}
</code></pre></div>

<p>
    And that's it!
</p>

<h2>Telling the Bootstrap about us</h2>

<p>
    Well, that's it for the plugin resource, that is. But how do we tell our
    bootstrap class about it? Via our configuration file, using the
    "pluginPaths" key. This is an array, with the keys being plugin class
    prefixes, and the values the path that corresponds to that prefix.
</p>

<div class="example"><pre><code lang="ini">
[production]
; ...
pluginPaths.Phly_Resource = \&quot;Phly/Resource\&quot;
resources.layouthelpers.doctype = \&quot;HTML5\&quot;
resources.layouthelpers.title = \&quot;My Snazzy New Website\&quot;
resources.layouthelpers.title_separator = \&quot; &amp;emdash; \&quot;
</code></pre></div>

<p>
    You can register as many plugin paths as you desire. As this key is
    processed before any resources are processed, it can also be defined at any
    time in your configuration.
</p>

<h2>Further Considerations</h2>

<p>
    The example in this post was admittedly trivial. One aspect not discussed
    was creating a resource that would be reused throughout your application. As
    an example, you might want to create a resource you'll use at different
    times in your application. If you return a value in your <code>init()</code>
    method, the bootstrap object will store this for later retrieval. A good
    example of this we saw earlier: the View resource registers a
    <code>Zend_View</code> object with the bootstrap simply by returning the
    instance from its resource plugin.
</p>

<h2>Conclusions</h2>

<p>
    Hopefully this post and the post prior have helped shed some light on
    <code>Zend_Application</code>, and in particular, how to write and bootstrap
    resources.
</p>

<p>
    If you have further questions, you can find me on the <a
        href="http://framework.zend.com/archives">ZF mailing lists</a>, on
    IRC via the Freenode servers, or on <a
        href="http://twitter.com/weierophinney">twitter</a>. Good luck!
</p>
EOT;
$entry->setExtended($extended);

return $entry;