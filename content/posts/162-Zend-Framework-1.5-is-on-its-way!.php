<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('162-Zend-Framework-1.5-is-on-its-way!');
$entry->setTitle('Zend Framework 1.5 is on its way!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1205717214);
$entry->setUpdated(1205890293);
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
    As many know, <a href="http://framework.zend.com/">Zend Framework</a> 1.5.0
    is almost ready for release... heck, it might even be released by the time
    you read this. There are a ton of new features worth looking into, but I'll
    list some of my own favorites here - the ones I've been either working on or
    using.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Zend_Layout and Zend_View enhancements</h3>
<p>
    I've been using the Two Step View pattern for years now, and its practically
    second nature with each project to set it up. With Zend_Layout now released,
    I no longer have to setup a plugin of my own and think about how it all
    works -- I can just create my site layout and start working.
</p>

<p>
    But Zend_Layout by itself isn't that revolutionary. What <em>is</em>
    revolutionary is the combination of Zend_Layout with a variety of new view
    helpers: partials, placeholders, and actions. In particular, placeholders
    are increasingly finding a place in my toolbox.
</p>

<p>
    As an example, I use the new <code>headScript()</code> and
    <code>headLink()</code> view helpers in my applications a ton. They allow me
    to specify javascript and CSS includes for my HTML head section... and also
    prevent me from accidently including the same file multiple times. Add to
    this the fact that, as placeholders, they can also capture arbitrary content
    for later inclusion, I can now create javascript that I need for a
    particular view <em>in that view</em> and include it in the document head
    easily. This helps me keep my UI logic close to the application, while still
    keeping it located in the appropriate place in the document.
</p>

<h3>Zend_Form</h3>

<p>
    To be honest, about half-way through writing Zend_Form, I was seriously
    wondering if a form component was necessary; having Zend_Filter_Input
    directly in your model, and simply writing your forms using Zend_View
    helpers seemed perfectly straightforward and painless.
</p>

<p>
    However, I've had a chance to play with Zend_Form a fair bit since then, and
    I'm very much appreciating the component. With it, I can put all the
    information about an element -- how it should validate, any pre-filtering I
    want, any metadata I want to associate with it, and also hints as to how I
    want it rendered -- all in one place. While it seems like a dangerous mix of
    business logic and presentation logic, it's not. The display logic is
    self-contained in the decorators used to render the element -- and are not
    even necessary. But having them present also means that my views can be
    <em>much</em> simpler than they were in the past, as can my controller
    and/or model logic -- I can pull all the information in from the form, and
    simply use it.
</p>

<p>
    As an example, consider this controller action:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
class FooController extends Zend_Controller_Action
{
    public function formAction()
    {
        $request = $this-&gt;getRequest();
        $form    = $this-&gt;getForm(); // helper method to pull in form

        if (!$request-&gt;isPost()) {
            // display form
            $this-&gt;view-&gt;form = $form;
            return;
        }

        if (!$form-&gt;isValid($request-&gt;getPost())) {
            // form not valid; re-display
            $this-&gt;view-&gt;form = $form;
            return;
        }

        // Valid form; save data
        $this-&gt;getModel()-&gt;save($form-&gt;getValues());
        $this-&gt;_helper-&gt;redirector('success');
    }
}
?&gt;
</code></pre></div>

<p>And the related view script for displaying the form:</p>

<div class="example"><pre><code lang="php">
&lt;h2&gt;Please fill out the form&lt;/h2&gt;
&lt;?= $this-&gt;form ?&gt;
</code></pre></div>

<p>
    Seriously -- while there may be a fair amount of code to setup the form,
    it's self-contained, and leads to some seriously shorter methods and views
    -- making the rest of the application logic much cleaner and easier to read.
    I like clean and easily read code.
</p>

<h3>Zend_Search_Lucene</h3>

<p>
    Sure, this component has been around for a long time, but Alex has done some
    increcible stuff with it. You can now build queries programmatically, and
    have support for wildcard searches. Additionally, you now have the ability
    to modify the index in place, without re-building -- which means that as
    your site grows, you don't have to worry that indexing the site is going to
    consume more and more resources.
</p>

<h3>Zend_Db_Table Improvements</h3>

<p>
    One thing that has always been a thorn in my side is that I will often want
    to do a query that includes a JOIN from within a Zend_Db_Table... but it
    hasn't allowed this. This has led to having custom methods that return
    arrays, or adding logic that returns custom ResultSets... all of which is
    tedious. Simon has put in a ton of time in the past few months to make these
    headaches go away. You can now return <em>read-only</em> resultsets that
    contain joins, letting you access your JOIN'ed rowsets just as you would one
    containing only the current table's data.
</p>

<p>
    Additionally, instead of needing to build your SQL by hand, or build it with
    Zend_Db_Select and then cast it to a string, Simon has also added the
    ability to create Zend_Db_Select's directly in your table classes, and pass
    them to any method that performs a query. This improvement will also save me
    time -- particularly when coupled with the ability to return JOINed
    ResultSets.
</p>

<h3>Context Switching and REST</h3>

<p>
    Something I've been playing with for some time now is the ability to change
    view context by simply changing a parameter in the request. For example, you
    may want to return XML or JSON in addition to HTML from a given action. One
    thing I've added for 1.5.0 is a ContextSwitch action helper that helps
    automate exactly this.
</p>

<p>
    With your actions ContextSwitch enabled, you can then request an action and
    add a 'format' parameter with the desired context: /foo/bar/format/xml. The
    parameter is detected, checked against a list of allowed contexts, and then,
    if available, a separate view is rendered and the response headers changed
    to match the format.
</p>

<p>
    In the above example, /foo/bar/format/xml, this would mean that a response
    Content-Type header of 'text/xml' would be returned, and instead of
    rendering the foo/bar.phtml view script, the foo/bar.xml.phtml script would
    be rendered. This allows you to have separate display logic for different
    contexts.
</p>

<p>
    In addition to context switching, at the request of 
    <a href="http://benramsey.com/">some vocal contributors</a>, I've added a
    variety of methods to Zend_Controller_Request to allow detecting the various
    HTTP request types (HEAD, GET, POST, PUT, DELETE, etc.), as well as
    retrieving raw post data. These, combined with context switching, will allow
    you to build REST services into your existing MVC apps trivially.
</p>

<h3>Conclusion</h3>

<p>
    There are a ton more features coming out with 1.5.0. 
    <a href="http://framework.zend.com/">Visit the framework site</a> 
    in the coming days to see what all is new, and download the new release to
    give it a try!
</p>
EOT;
$entry->setExtended($extended);

return $entry;