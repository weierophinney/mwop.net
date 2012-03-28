<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('163-Using-Zend_View-Placeholders-to-Your-Advantage');
$entry->setTitle('Using Zend_View Placeholders to Your Advantage');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1205874806);
$entry->setUpdated(1205889720);
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
    Somebody asked for some examples of how I use the <code>headLink()</code>,
    <code>headScript()</code>, and other placeholder helpers, so I thought I'd
    take a crack at that today.
</p>

<p>
    First off, let's look at what these helpers do. Each are concrete instances
    of a <em>placeholder</em>. In Zend Framework, placeholders are used for a
    number of purposes:
</p>

<ul>
    <li>Doctype awareness</li>
    <li>Aggregation and formatting of aggregated content</li>
    <li>Capturing content</li>
    <li>Persistence of content between view scripts and layout scripts</li>
</ul>

<p>
    Let's look at these in detail.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Doctype Hinting</h2>

<p>
    The HTML specification encourages you to use a DocType declaration in your
    HTML documents -- and XHTML actually requires one. Simply put, the DocType
    helps tell your browser what is considered valid syntax, as well as provides
    some hints to how it should render.
</p>

<p>
    Now, if you're like me, these are a pain to remember; the syntax is somewhat
    arcane, very long, and not something I want to type very often. Fortunately,
    the new <code>doctype()</code> helper allows you to use mnemonics such as
    'XHTML1_TRANSITIONAL' or 'HTML4_STRICT' to invoke the appropriate doctype:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;doctype('XHTML1_TRANSITIONAL') ?&gt;
</code></pre></div>

<p>
    However, a doctype isn't just a hint to the browser; it's a contract that
    you need to follow. If you select a particular doctype, you're agreeing to
    write markup that follows the specification for it.
</p>

<p>
    The <code>doctype()</code> helper is actually used internally in many of the
    placeholder helpers (as well as the <code>form*()</code> helpers) to ensure
    that the markup they generate -- if any -- adheres the the given doctype.
    However, for this to work, you need to specify your doctype early. I
    recommend doing it either in your bootstrap or in a plugin that runs before
    any output is emitted; typically, I will pull the view from the ViewRenderer
    in order to do so:
</p>

<div class="example"><pre><code lang="php">
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
$viewRenderer-&gt;initView();
$viewRenderer-&gt;view-&gt;doctype('XHTML1_TRANSITIONAL');
</code></pre></div>

<p>
    Since this sets the doctype helper's state, you can then simply echo the
    return value of the doctype helper later in your layout script:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;doctype() ?&gt;
</code></pre></div>

<h2>Content Aggregation</h2>

<p>
    Placeholders aggregate and store content across view instances. By
    aggregate, I mean that they store the data provided in an
    <code>ArrayObject</code>, allowing you to collect related data for later
    display. Since placeholders imlement <code>__toString()</code>, and can be
    collections, we've added accessors to allow you to set arbitrary text to
    prefix, append, and separate the items in the collection. The various
    concrete placeholders -- primarily the <code>head*()</code> helpers -- make
    use of this particular feature, storing each entry as a separate item in the
    collection, and then decorating them when called on to render.
</ul>

<p>
    Additionally, the concrete instances each contain some custom logic. In the
    case of <code>headLink()</code> and <code>headScript</code> helpers, we
    perform checks to ensure that when specifying files, duplicate entries are
    ignored. Why is this a good idea? Well, since you can
    <code>_forward()</code> to other actions, or even call the
    <code>action()</code> view helper, you could potentially have multiple view
    scripts loading the same stylesheets or javascript; we help protect against
    such situations.
</p>

<p>
    So, as an example:
</p>

<div class="example"><pre><code lang="php">
&lt;? // /foo/bar view script: ?&gt;
&lt;? 
$this-&gt;headLink()-&gt;appendStylesheet('/css/foo.css'); 
$this-&gt;headScript()-&gt;appendFile('/js/foo.js'); 
echo $this-&gt;action('baz', 'foo');
?&gt;

&lt;? // /foo/baz view script; ?&gt;
&lt;?
$this-&gt;headLink()-&gt;appendStylesheet('/css/foo.css'); 
$this-&gt;headScript()-&gt;appendFile('/js/foo.js'); 
?&gt;
FOO BAZ!
</code></pre></div>

<p>
    It's a contrived example, for sure, but it shows the problem quite well: if
    two view scripts are rendered during creation of the same content, then you
    have the potential for duplicate content in your placeholders. However, in
    this case, the duplicate content will not occur, as the helpers detect the
    duplicate entries when they're added, and skip them.
</p>

<h2>Capturing Content</h2>

<p>
    One way in which placeholders aggregate content is by <em>capturing</em>
    content. The base placeholder class defines both a
    <code>captureStart()</code> and <code>captureEnd()</code> method, allowing
    you to create content in your view scripts that you then capture for use
    later.
</p>

<p>
    This is particularly useful for the <code>headScript()</code> helper, as it
    allows you to create javascript directly in your view that will be executed
    in the HTML head (or, if you use the <code>inlineScript()</code>) helper,
    you can have it executed at the end of your document, which is what Y!Slow
    recommends). The same goes for the <code>headStyle()<code> helper; you can
    define custom stylesheets to include directly in your document directly with
    the view that needs them.
</p>
  
<p>
    As an example, <a href="http://dojotoolkit.org/">Dojo</a> ships with some
    custom stylesheets for rendering its various widgits, and also has the
    ability to load custom classes and widgets dynamically. Let's say we want to
    present a Dojo ComboBox in our page: we'll need a couple of stylesheets, as
    well as a few Dojo resources:
</p>

<p>
    First, let's tackle the stylesheets:
</p>

<div class="example"><pre><code lang="php">
&lt;? $this-&gt;headStyle()-&gt;captureStart() ?&gt;
@import \&quot;/js/dijit/themes/tundra/tundra.css\&quot;;
@import \&quot;/js/dojo/resources/dojo.css\&quot;;
&lt;? $this-&gt;headStyle()-&gt;captureEnd() ?&gt;
</code></pre></div>

<p>
    These are now aggregated in our <code>headStyle()</code> view helper, and
    we can render them later; they will not appear inline in the page as they do
    here in the view script.
</p>

<p>
    Now, let's tackle the javascript. We need to load the main
    <code>dojo.js</code> file as a script, and then create an inline script to
    load our various widgets. Dojo often uses its own custom HTML attributes,
    and the <code>head*()</code> helpers typically don't like this (they like to
    stick to those attributes defined in the specs), so we'll need to tell the
    helper that this is okay so that Dojo will parse the page when it finishes
    loading (to decorate our widget with the appropriate, requested
    functionality).
</p>

<div class="example"><pre><code lang="php">
&lt;? $this-&gt;headScript()
        -&gt;setAllowArbitraryAttributes(true)
        -&gt;appendFile('/js/dojo/dojo.js', 'text/javascript', array('djConfig' =&gt; 'parseOnLoad: true'))
        -&gt;captureStart() ?&gt;
djConfig.usePlainJson=true;
dojo.require(\&quot;dojo.parser\&quot;);
dojo.require(\&quot;dojox.data.QueryReadStore\&quot;);
dojo.require(\&quot;dijit.form.ComboBox\&quot;);
&lt;? $this-&gt;headScript()-&gt;captureEnd() ?&gt;
</code></pre></div>

<p>
    What's the benefit to doing this? It allows you to keep the JS and CSS
    functionality that's related to the specific view script at hand
    <em>with</em> that view script -- you have everything in one place. If you
    need to change what JS or CSS is loaded, or modify the inline JS you're
    going to utilize, you can find it with the rest of the content to which it
    applies.
</p>

<h2>Putting it Together: the Layout</h2>

;<p>
    I keep talking about "when you render it later" in this narrative. "Later"
    refers to your layout script. I'm not going to go into how you initialize or
    define your layouts here, as it's been covered in <a href="/matthew/archives/162-Zend-Framework-1.5-is-on-its-way!.html">other</a> <a href="http://akrabat.com/2007/12/11/simple-zend_layout-example/">places</a>. However, let's
    look at how we can pull in our doctype and head helpers into our layout:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;doctype() ?&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;? // headTitle() is another concrete placeholder ?&gt;
        &lt;?= $this-&gt;headLink() ?&gt; 
        &lt;?= $this-&gt;headStyle() ?&gt; 
        &lt;?= $this-&gt;headScript() ?&gt; 
    &lt;/head&gt;
    ...
</code></pre></div>

<p>
    Sure, you may want to put more in there than that -- if you have stylesheets
    or scripts that load on every page, you may want to define them statically
    in the layout... in addition to calling the placeholder helpers. But adding
    the placeholder helpers gives you some definite benefits: increased
    separation of code, more maintainable code (as the CSS and JS specific to a
    view is kept <em>with</em> the view), simpler logic in your layouts, and
    the ability to prevent duplicate file inclusions.
</p>

<p>
    All this functionality is now standard with Zend Framework 1.5.0; if you
    haven't given it a try, 
    <a href="http://framework.zend.com/download">download it today</a>.
</p>

<p>
    <b>Note:</b> my colleague, Ralph Schindler -- the original proposal author of
    Zend_Layout and a substantial contributor to the various placeholder
    helpers -- is 
    <a href="http://www.zend.com/en/company/news/event/webinar-zend-layout-and-zend-view-enhancements">giving a webinar on Zend_Layout and Zend_View</a> 
    tomorrow, 18 March 2008; if you're interested in this topic, you should check it out.
</p>

<p>
    <b>Updated:</b> fixed links to layout articles.
</p>
EOT;
$entry->setExtended($extended);

return $entry;