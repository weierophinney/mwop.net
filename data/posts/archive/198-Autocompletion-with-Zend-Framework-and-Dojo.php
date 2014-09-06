<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('198-Autocompletion-with-Zend-Framework-and-Dojo');
$entry->setTitle('Autocompletion with Zend Framework and Dojo');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1229098049);
$entry->setUpdated(1229341791);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've fielded several questions about setting up an autocompleter with
    <a href="http://framework.zend.com/">Zend Framework</a> and
    <a href="http://dojotoolkit.org/">Dojo</a>, and decided it was time to
    create a HOWTO on the subject, particularly as there are some nuances you
    need to pay attention to.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Which dijits perform autocompletion?</h2>
<p>
    Your first task is selecting an appropriate form element capable of
    autocompletion. Dijit provides two, <code>ComboBox</code> and
    <code>FilteringSelect</code>. However, they have different capabilities:
</p>

<ul>
    <li><code>ComboBox</code> allows you to enter arbitrary text; if it doesn't
    match the associated list, it is still considered valid. The text
    <em>entered</em> is submitted -- <b><i>not</i></b> the option value. (This
    differs from normal dropdown selects.)</li>

    <li><code>FilteringSelect</code> also allows you to enter arbitrary text,
    but it will only be considered valid if it matches an option provided to it.
    The <em>option value</em> is submitted, just like a normal dropdown
    select.</li>
</ul>

<p>
    Once you've chose the appropriate form element type, you then need to
    specify a <code>dojo.data</code> store. <code>dojo.data</code> provides a
    consistent API for data structures consumed by dijits and other dojo
    components. At it's heart, it's simply an array of arbitrary JSON structures
    that each contain a common identifier field containing a unique value per
    item. Internally, both <code>ComboBox</code> and
    <code>FilteringSelect</code> can utilize <code>dojo.data</code> stores to
    populate their options and/or provide matches. Dojo provides a variety of
    <code>dojo.data</code> stores for such purposes.
</p>

<h3>Defining the form element</h3>

<p>
    Defining the form element is very straightforward. From your
    <code>Zend_Dojo_Form</code> instance (or your form extending that class),
    simply call <code>addElement()</code> as usual. Later in this tutorial,
    depending on the approach you use, you may need to add some information to
    the element definition, but for now, all that's needed is the most basic of
    element definitions:
</p>

<div class="example"><pre><code lang="php">
$form-&gt;addElement('ComboBox', 'myAutoCompleteField', array(
    'label'     =&gt; 'My autocomplete field:',
));
</code></pre></div>

<h2>Providing data to a dojo.data store</h2>
<p>
    We're going to work backwards now, as providing data to the data store is
    relatively trivial when using <code>Zend_Dojo_Data</code>.
</p>

<p>
    First, we'll create an action in our controller, and assign the model and
    the query parameter to the view. We'll be setting up our
    <code>dojo.data</code> store to send the query string via the GET parameter
    "q", so that's what we'll assign to the view.
</p>

<div class="example"><pre><code lang="php">
    public function autocompleteAction()
    {
        // First, get the model somehow
        $this-&gt;view-&gt;model = $this-&gt;getModel();

        // Then get the query, defaulting to an empty string
        $this-&gt;view-&gt;query = $this-&gt;_getParam('q', '');
    }
</code></pre></div>

<p>
    Now, let's create the view script. First, we'll disable layouts; second,
    we'll pass our query to the model; third, we'll instantiate our
    <code>Zend_Dojo_Data</code> object with the results of our query; and
    finally, we'll echo the <code>Zend_Dojo_Data</code> instance.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
// Disable layouts
$this-&gt;layout()-&gt;disableLayout();

// Fetch results from the model; again, merely illustrative
$results = $this-&gt;model-&gt;query($this-&gt;params);

// Now, create a Zend_Dojo_Data object.
// The first parameter is the name of the field that has a
// unique identifier. The second is the dataset. The third
// should be specified for autocompletion, and should be the
// name of the field representing the data to display in the
// dropdown. Note how it corresponds to \&quot;name\&quot; in the 
// AutocompleteReadStore.
$data = new Zend_Dojo_Data('id', $results, 'name');

// Send our output
echo $data;
</code></pre></div>

<p>
    That's really all there is to it. You can actually automate some of this
    using the <code>AjaxContext</code> action helper, making it even simpler.
</p>

<h2>Using dojox.data.QueryReadStore</h2>
<p>
    We now have an endpoint for our <code>dojo.data</code> data store, so now we
    need to determine which store type to use.
</p>

<p>
    <code>dojox.data.QueryReadStore</code> is a fantastic <code>dojo.data</code>
    store allowing you to create arbitrary queries on data. It creates the query
    as a JSON object:
</p>

<div class="example"><pre><code lang="javascript">
{
    query: { name: \&quot;A*\&quot; },
    queryOptions: { ignoreCase: true },
    sort: [{ attribute: \&quot;name\&quot;, descending: false }],
    start: 0,
    count: 10
}
</code></pre></div>

<p>
    This is problematic in two ways. First, if you were to use it directly,
    you'd be limited to POST requests, submitting it as a raw post. Second, and
    related, this means that requests could not be cached client-side. 
</p>

<p>
    Fortunately, there's an easy way to correct the situation: extend
    <code>dojox.data.QueryReadStore</code> and override the <code>fetch</code>
    method to rewrite the query as a simple GET query with a single parameter.
</p>

<div class="example"><pre><code lang="javascript">
dojo.provide(\&quot;custom.AutocompleteReadStore\&quot;);

dojo.declare(
    \&quot;custom.AutocompleteReadStore\&quot;, // our class name
    dojox.data.QueryReadStore,      // what we're extending
    {
        fetch: function(request) {  // the fetch method
            // set the serverQuery, which sets query string parameters
            request.serverQuery = {q: request.query.name};

            // and then operate as normal:
            return this.inherited(\&quot;fetch\&quot;, arguments);
        }
    }
);
</code></pre></div>

<p>
    The question now is, where to create this definition?
</p>

<p>
    You have two options: you can inline the custom definition (less intuitive)
    and connect the data store manually to the form element, or you can create
    an actual javascript class file (slightly more work) and have your form
    element setup the data store for you.
</p>

<h3>Inlining a custom QueryReadStore class extension</h3>
<p>
    Inlining is a bit tricky to accomplish, as you need to declare things in the
    appropriate order. When using this technique, you need to do the following:
</p>

<ol>
    <li>require the <code>dojox.data.QueryReadStore</code> class</li>
    <li>define a global JS variable that will be used to identify your
    store</li>
    <li>use <code>dojo.provide</code> and <code>dojo.declare</code> to create
    your custom data store extension</li>
    <li>define an onLoad event that instantiates the data store and attaches it
    to the form element</li>
</ol>

<p>
    We can do all the above within the same view script in which we spit out our
    form:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
$this-&gt;dojo()-&gt;requireModule(\&quot;dojox.data.QueryReadStore\&quot;);

// Define a new data store class, and 
// setup our autocompleter data store
$this-&gt;dojo()-&gt;javascriptCaptureStart() ?&gt;
dojo.provide(\&quot;custom.AutocompleteReadStore\&quot;);
dojo.declare(
    \&quot;custom.AutocompleteReadStore\&quot;, 
    dojox.data.QueryReadStore, 
    {
        fetch: function(request) {
            request.serverQuery = {q: request.query.name};
            return this.inherited(\&quot;fetch\&quot;, arguments);
        }
    }
);
var autocompleter;
&lt;?php $this-&gt;dojo()-&gt;javascriptCaptureEnd();

// Once dijits have been created and all classes defined,
// instantiate the autocompleter and attach it to the element.
$this-&gt;dojo()-&gt;onLoadCaptureStart() ?&gt;
function() {
    autocompleter = new custom.AutocompleteReadStore({
        url: \&quot;/test/autocomplete\&quot;,
        requestMethod: \&quot;get\&quot;
    });
    dijit.byId(\&quot;myAutoCompleteField\&quot;).attr({
        store: autocompleter
    });
}
&lt;?php $this-&gt;dojo()-&gt;onLoadCaptureEnd() ?&gt;
&lt;h1&gt;Autocompletion Example&lt;/h1&gt;
&lt;div class=\&quot;tundra\&quot;&gt;
&lt;?php echo $this-&gt;form ?&gt;
&lt;/div&gt;
</code></pre></div>

<p>
    This works well, and is an expedient way to get autocompletion working for
    your element. However, it breaks the DRY principle as you cannot re-use the
    custom class in other areas. So, let's look at a better solution
</p>

<h3>Creating a reusable custom QueryReadStore class extension</h3>

<p>
    The recommendation by the Dojo developers is that you should create this
    class as a <em>javascript</em> class, with your other <em>javascript</em>
    code. The reasons for this are numerous: you can re-use the class elsewhere,
    and you can also include it in custom builds -- which will ensure that it is
    stripped of whitespace and packed, leading to smaller downloads for your end
    users.
</p>

<p>
    The process isn't as scary as it may initially sound.  Assuming that your
    "public/" directory has the following structure:
</p>

<code><pre>
public/
    js/
        dojo/
            dojo.js
        dijit/
        dojox/
</pre></code>

<p>
    what we'll do here is to create a sibling to the "dojo" subdirectory, called
    "custom", and create our class file there:
</p>

<code><pre>
public/
    js/
        dojo/
            dojo.js
        dijit/
        dojox/
        custom/
            AutocompleteReadStore.js
</pre></code>

<p>
    We'll use the definition as originally shown above, and simply save it as
    "public/js/custom/AutocompleteReadStore.js", with one addition: after the
    <code>dojo.provide</code> call, add this:
</p>

<div class="example"><pre><code lang="javascript">
dojo.require(\&quot;dojox.data.QueryReadStore\&quot;);
</code></pre></div>

<p>
    This is analagous to a <code>require_once</code> call in PHP, and ensures
    that the class has all dependencies prior to declaring itself. We'll
    leverage this fact later, when we hint in our <code>ComboBox</code> element
    what type of data store to use.
</p>

<p>
    On the framework side of things, we're going to alter our element definition
    slightly to include information about the <code>dojo.data</code> store it
    will be using:
</p>

<div class="example"><pre><code lang="php">
$form-&gt;addElement('ComboBox', 'myAutoCompleteField', array(
    'label'     =&gt; 'My autocomplete field:',

    // The javascript identifier for the data store:
    'storeId'   =&gt; 'autocompleter',

    // The class type for the data store:
    'storeType' =&gt; 'custom.AutocompleteReadStore',

    // Parameters to use when initializint the data store:
    'storeParams' =&gt; array(
        'url'           =&gt; '/foo/autocomplete',
        'requestMethod' =&gt; 'get',
    ),
));
</code></pre></div>

<p>
    If you've been following along closely, you'll notice that the "storeParams"
    are exactly the same as what we used to initialize the data store when
    inlining. The difference is that now the <code>ComboBox</code> view helper
    will create all the necessary Javascript for you.
</p>

<p>
    The view script now becomes greatly simplified; we no longer need to setup
    any javascript, and can literally simply echo the form:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;form ?&gt;
</code></pre></div>

<p>
    Hopefully it should now be clear which method is easiest in the long run.
</p>

<h2>Next Steps</h2>
<p>
    <code>dojox.data.QueryReadStore</code> offers much more than simply
    specifying the query string. As noted when introducing the component, it
    creates a JSON structure that also includes keys for sorting, selecting how
    many results to display, and offsets when pulling results. These, too, can
    be added to your query strings to allow finer grained selection of results
    -- for instance, you could ensure that no more than 3 or 5 results are
    returned, to allow for a more manageable list of matches, or specify a sort
    order that makes more sense to users.
</p>

<h2>Summary</h2>
<p>
    Learning new tools can be difficult, and Dojo and Zend Framework are no
    exceptions. One compelling reason to learn Dojo if you're using Zend
    Framework, however, is that its structure and design should be familiar: it
    uses the same 1:1 class name:filename mapping paradigm. Additionally,
    because it is written to utilize strong OOP principles, familiar concepts
    such as extending classes can be used to customize Dojo for your site's
    needs.
</p>

<p>
    Hopefully this tutorial will shed a little light on both the subject of
    autocompletion in Dojo, as well as class extensions in Dojo, and help get
    you started creating your own custom Dojo libraries for use with your
    applications.
</p>
EOT;
$entry->setExtended($extended);

return $entry;