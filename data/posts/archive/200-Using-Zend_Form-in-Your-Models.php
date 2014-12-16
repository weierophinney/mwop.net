<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('200-Using-Zend_Form-in-Your-Models');
$entry->setTitle('Using Zend_Form in Your Models');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1229952600);
$entry->setUpdated(1230429225);
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
    A <a href="http://blog.astrumfutura.com/index.php?url=archives/373-The-M-in-MVC-Why-Models-are-Misunderstood-and-Unappreciated.html">number</a> 
    of <a href="http://akrabat.com/2008/12/13/on-models-in-a-zend-framework-application/">blog</a>
    <a href="http://codeutopia.net/blog/2008/12/17/the-problems-faced-by-a-common-model-interface-in-frameworks/">posts</a> 
    have sprung up lately in the Zend Framework community discussing the Model
    in the <a href="http://en.wikipedia.org/wiki/Model-view-controller">Model-View-Controller pattern</a>. 
    <a href="http://framework.zend.com/">Zend Framework</a> has never had a
    concrete Model class or interface; our stand has been that models are
    specific to the application, and only the developer can really know what
    would best suit it. 
</p>
    
<p>
    Many other frameworks tie the Model to data access -- typically via the
    <a href="http://en.wikipedia.org/wiki/Active_record_pattern">ActiveRecord</a> 
    pattern or a <a href="http://martinfowler.com/eaaCatalog/tableDataGateway.html">Table Data Gateway</a> 
    -- which completely ignores the fact that this is tying the Model to the
    method by which it is persisted.  What happens later if you start using
    memcached? or migrate to an SOA architecture? What if, from the very
    beginning, your data is coming from a web service? What if you <em>do</em>
    use a database, but your business logic relies on associations
    <em>between</em> tables?
</p>

<p>
    While the aforementioned posts do an admirable job of discussing the various
    issues, they don't necessarily give any concrete approaches a developer
    <em>can</em> use when creating their models. As such, this will be the first
    in a series of posts aiming to provide some concrete patterns and techniques
    you can use when creating your models. The examples will primarily be
    drawing from Zend Framework components, but should apply equally well to a
    variety of other frameworks.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Input Filtering and Forms</h2>

<p>
    In most cases, you want your model to perform its own input filtering. The
    reason is because input filtering is domain logic: it's the set of rules
    that define what input is valid, and how to normalize that input.
</p>

<p>
    However, how does that fit in with forms? Zend Framework has a
    <code>Zend_Form</code> component, which allows you to specify your
    validation and filter chains, as well as rules for how to render the form
    via decorators. The typical pattern is to define a form, and in your
    controller, pass input to it; if it validates, you then pass the values to
    the model.
</p>

<p>
    What if you were to instead attach the <em>form</em> to the <em>model</em>?
</p>

<p>
    Some argue that this violates the concept of "separation of concerns", due
    to the fact that it mixes rendering logic into the model. I feel this is a
    pedantic argument. When attached to a form, <code>Zend_Form</code> can be
    used strictly as an input filter; you would pull the form <em>from</em> the
    model when you wish to render it, and perform any view-specific actions --
    configuring decorators, setting the action and method, etc -- within your
    <em>view</em> script. Additionally, the various plugins -- validators,
    filters, decorators -- are not loaded until they are <em>used</em> --
    meaning there is little to no overhead from the decorators when you merely
    use <code>Zend_Form</code> as an input filter.
</p>
    
<p>
    Basically, this approach helps you adhere to the DRY principle (one
    validation/filter chain), while simultaneously helping you keep a solid
    separation of business and view logic. Finally, you gain one or more
    form representations of your model, which helps with rapid application
    development, as well as providing a solid, semantic tie between the model
    and the view.
</p>

<p>
    So, on to the technique.
</p>

<h2>Attaching Forms to Models</h2>

<p>
    What I've been doing is adding a <code>getForm()</code> accessor to my
    models that takes an optional argument, the type of form to retrieve. This
    is then used within the model any time validation is necessary. (Some models
    require multiple forms, so best to plan for it early. A good example is a
    model that represents a user -- you will need a login <em>and</em>
    registration form.) Let's look at it in action:
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_Bug
{
    protected $_forms = array();

    public function getForm($type = 'bug')
    {
        $type  = ucfirst($type);
        if (!isset($this-&gt;_forms[$type])) {
            $class = 'Spindle_Model_Form_' . $type;
            $this-&gt;_forms[$type] = new $class;
        }
        return $this-&gt;_forms[$type];
    }

    public function save(array $data)
    {
        $form = $this-&gt;getForm();
        if (!$form-&gt;isValid($data)) {
            return false;
        }

        $storage = $this-&gt;getStorage();
        if ($form-&gt;getValue('id')) {
            $id = $form-&gt;getValue('id');
            $storage-&gt;update($form-&gt;getValues(), $id));
        } else {
            $id = $storage-&gt;insert($form-&gt;getValues());
        }

        return $id;
    }
}
</code></pre></div>

<p>
    As the above code snippet demonstrates, the form acts as an input filter:
    you use it first to ensure the data provided is valid, and then to ensure
    the data you pass to your persistence layer is normalized according to your
    rules. You can also use it to verify the existence of certain optional
    values, as done here, in order to ascertain the actual action necessary to
    persist the data.
</p>

<h2>What Happens in the Controller and View?</h2>

<p>
    Within your controller actions, you then have a slight paradigm shift.
    Instead of validating a form and then passing filtered data to the model,
    you simply attempt to save data to the model:
</p>

<div class="example"><pre><code class="language-php">
class BugController
{
    public function processAction()
    {
        $request = $this-&gt;getRequest();
        if (!$request-&gt;isPost()) {
            return $this-&gt;_helper-&gt;redirector('new');
        }

        if (!$id = $this-&gt;model-&gt;save($request-&gt;getPost())) {
            // Failed validation; re-render form page
            $this-&gt;view-&gt;model = $model;
            return $this-&gt;render('new');
        }

        // redirect to view newly saved bug
        $this-&gt;_helper-&gt;redirector('view', null, null, array('id' =&gt; $id));
    }
}
</code></pre></div>

<p>
    There's very little logic there, and no mention of forms whatsoever. So, how
    do we actually render the form? Note that the model is passed to the view --
    which ultimately gives us access to the form.
</p>

<div class="example"><pre><code class="language-php">
$form = $this-&gt;model-&gt;getForm();
$form-&gt;setMethod('post')
     -&gt;setAction($this-&gt;url(array('action' =&gt; 'process')));
echo $form;
</code></pre></div>

<p>
    This makes semantic sense; you're rendering a form that will be used to
    filter data for a given model. Note that some view logic is given -- the
    form method and action are set here in the view layer. This is appropriate,
    as we're now performing display-related logic.
</p>

<h2>Summary</h2>

<p>
    There are of course other ways to solve the problem, but this is a
    convenient and expedient solution that maximizes use of the various existing
    components. Attaching forms to your models keeps all logic related to input
    validation -- including error reporting -- in one place, and ensures that
    your forms do not go out of date when you change your model -- as you will
    be updating your validation rules and list of allowed input in the form
    itself.
</p>

<p>
    In the next post, we'll look at <a href="http://weierophinney.net/matthew/archives/201-Applying-ACLs-to-Models.html">using and applying Access Control Lists (ACLs) in your models</a>.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
