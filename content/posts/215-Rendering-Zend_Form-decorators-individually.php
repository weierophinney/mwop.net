<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('215-Rendering-Zend_Form-decorators-individually');
$entry->setTitle('Rendering Zend_Form decorators individually');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1239283680);
$entry->setUpdated(1286280220);
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
    In the <a href="/matthew/archives/213-From-the-inside-out-How-to-layer-decorators.html">previous installment</a> of this series on
    <code>Zend_Form</code> decorators, I looked at how you can combine
    decorators to create complex output. In that write-up, I noted that while
    you have a ton of flexibility with this approach, it also adds some
    complexity and overhead. In this article, I will show you how to render
    decorators individually in order to create custom markup for your form
    and/or individual elements. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Once you have registered your decorators, you can later retrieve them by
    name from the element. Let's review our previous example:
</p>

<div class="example"><pre><code lang="php">
$element = new Zend_Form_Element('foo', array(
    'label'      =&gt; 'Foo',
    'belongsTo'  =&gt; 'bar',
    'value'      =&gt; 'test',
    'prefixPath' =&gt; array('decorator' =&gt; array(
        'My_Decorator' =&gt; 'path/to/decorators/',
    )),
    'decorators' =&gt; array(
        'SimpleInput'
        array('SimpleLabel', array('placement' =&gt; 'append')),
    ),
));
</code></pre></div>

<p>
    If we wanted to pull and render just the "SimpleInput" decorator, we can do
    so using the <code>getDecorator()</code> method:
</p>

<div class="example"><pre><code lang="php">
$decorator = $element-&gt;getDecorator('SimpleInput');
echo $decorator-&gt;render('');
</code></pre></div>

<p>
    This is pretty easy, but it can be made even easier; let's do it in a single
    line:
</p>

<div class="example"><pre><code lang="php">
echo $element-&gt;getDecorator('SimpleInput')-&gt;render('');
</code></pre></div>

<p>
    Not too bad, but still a little complex. To make this easier, we introduced
    a shorthand notation into <code>Zend_Form</code> in 1.7: you can now render
    any registered decorator by calling a method
    <code>renderDecoratorName()</code>. This will effectively perform what you
    see above, but makes the <code>$content</code> argument optional and
    simplifies the usage:
</p>

<div class="example"><pre><code lang="php">
echo $element-&gt;renderSimpleInput();
</code></pre></div>

<p>
    This is a neat trick, but how and why would you use it?
</p>

<p>
    Many developers and designers have very precise markup needs for their
    forms. They would rather have full control over the output than rely on a
    more automated solution which may or may not conform to their design. In
    other cases, the form layout may require a lot of specialized markup --
    grouping arbitrary elements, making some invisible unless a particular link
    is selected, etc.
</p>

<p>
    Let's utilize the ability to render individual decorators to create some
    specialized markup.
</p>

<p>
    First, let's define a form. Our form will capture a user's demographic
    details. The markup will be highly customized, and in some cases use view
    helpers directly instead of form elements in order to achieve its goals.
    Here is the basic form definition:
</p>

<div class="example"><pre><code lang="php">
class My_Form_UserDemographics extends Zend_Form
{
    public function init()
    {
        // Add a path for my own decorators
        $this-&gt;addElementPrefixPaths(array(
            'decorator' =&gt; array('My_Decorator' =&gt; 'My/Decorator'),
        ));

        $this-&gt;addElement('text', 'firstName', array(
            'label' =&gt; 'First name: ',
        ));
        $this-&gt;addElement('text', 'lastName', array(
            'label' =&gt; 'Last name: ',
        ));
        $this-&gt;addElement('text', 'title', array(
            'label' =&gt; 'Title: ',
        ));
        $this-&gt;addElement('text', 'dateOfBirth', array(
            'label' =&gt; 'Date of Birth (DD/MM/YYYY): ',
        ));
        $this-&gt;addElement('text', 'email', array(
            'label' =&gt; 'Your email address: ',
        ));
        $this-&gt;addElement('password', 'password', array(
            'label' =&gt; 'Password: ',
        ));
        $this-&gt;addElement('password', 'passwordConfirmation', array(
            'label' =&gt; 'Confirm Password: ',
        ));
    }
}
</code></pre></div>

<p>
    Note: I'm not defining any validators or filters at this time, as they are
    not relevant to the discussion of decoration. In a real-world scenario, you
    should define them.
</p>

<p>
    With that out of the way, let's consider how we might want to display this
    form. One common idiom with first/last names is to display them on a single
    line; when a title is provided, that is often on the same line as well.
    Dates, when not using a JavaScript date chooser, will often be separated
    into three fields displayed side by side.
</p>

<p>
    Let's use the ability to render an element's decorators one by one to
    accomplish this. First, I'll note that I did not set any explicit decorators
    for the given elements. As a refresher, the default decorators for (most)
    elements are:
</p>

<ul>
    <li>ViewHelper: utilize a view helper to render a form input</li>
    <li>Errors: utilize the FormErrors view helper to render validation errors</li>
    <li>Description: utilize the FormNote view helper to render the element
    description (if any)</li>
    <li>HtmlTag: wrap the above three items in a &lt;dd&gt; tag</li>
    <li>Label: render the element label using the FormLabel view helper (and
    wrap it in a &lt;dt&gt; tag)</li>
</ul>

<p>
    Also, as a refresher, you can access any element of a form as if it were a
    class property; simply reference the element by the name you assigned it.
</p>

<p>
    Our view script might then look like this:
</p>

<div class="example"><pre><code lang="php">
&lt;?php 
$form = $this-&gt;form; 
// Remove &lt;dt&gt; from label generation
foreach ($form-&gt;getElements() as $element) {
    $element-&gt;getDecorator('label')-&gt;setTag(null);
}
?&gt;
&lt;form method=\&quot;&lt;?php echo $form-&gt;getMethod() ?&gt;\&quot; action=\&quot;&lt;?php echo
    $form-&gt;getAction()?&gt;\&quot;&gt;
    &lt;div class=\&quot;element\&quot;&gt;
        &lt;?php echo $form-&gt;title-&gt;renderLabel() . $form-&gt;title-&gt;renderViewHelper() ?&gt;
        &lt;?php echo $form-&gt;firstName-&gt;renderLabel() . $form-&gt;firstName-&gt;renderViewHelper() ?&gt;
        &lt;?php echo $form-&gt;lastName-&gt;renderLabel() . $form-&gt;lastName-&gt;renderViewHelper() ?&gt;
    &lt;/div&gt;
    &lt;div class=\&quot;element\&quot;&gt;
        &lt;?php echo $form-&gt;dateOfBirth-&gt;renderLabel() ?&gt;
        &lt;?php echo $this-&gt;formText('dateOfBirth['day']', '', array(
            'size' =&gt; 2, 'maxlength' =&gt; 2)) ?&gt;
        /
        &lt;?php echo $this-&gt;formText('dateOfBirth['month']', '', array(
            'size' =&gt; 2, 'maxlength' =&gt; 2)) ?&gt;
        /
        &lt;?php echo $this-&gt;formText('dateOfBirth['year']', '', array(
            'size' =&gt; 4, 'maxlength' =&gt; 4)) ?&gt;
    &lt;/div&gt;
    &lt;div class=\&quot;element\&quot;&gt;
        &lt;?php echo $form-&gt;password-&gt;renderLabel() . $form-&gt;password-&gt;renderViewHelper() ?&gt;
    &lt;/div&gt;
    &lt;div class=\&quot;element\&quot;&gt;
        &lt;?php echo $form-&gt;passwordConfirmation-&gt;renderLabel() . $form-&gt;passwordConfirmation-&gt;renderViewHelper() ?&gt;
    &lt;/div&gt;
    &lt;?php echo $this-&gt;formSubmit('submit', 'Save') ?&gt;
&lt;/form&gt;
</code></pre></div>

<p>
    If you use the above view script, you'll get approximately the following
    HTML (approximate, as the HTML below is formatted):
</p>

<div class="example"><pre><code lang="html">
&lt;form method=\&quot;post\&quot; action=\&quot;\&quot;&gt;
    &lt;div class=\&quot;element\&quot;&gt;
        &lt;label for=\&quot;title\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;Title:&lt;/label&gt;
        &lt;input type=\&quot;text\&quot; name=\&quot;title\&quot; id=\&quot;title\&quot; value=\&quot;\&quot;/&gt;

        &lt;label for=\&quot;firstName\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;First name:&lt;/label&gt;
        &lt;input type=\&quot;text\&quot; name=\&quot;firstName\&quot; id=\&quot;firstName\&quot; value=\&quot;\&quot;/&gt;
        
        &lt;label for=\&quot;lastName\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;Last name:&lt;/label&gt;
        &lt;input type=\&quot;text\&quot; name=\&quot;lastName\&quot; id=\&quot;lastName\&quot; value=\&quot;\&quot;/&gt;
    &lt;/div&gt;

    &lt;div class=\&quot;element\&quot;&gt;
        &lt;label for=\&quot;dateOfBirth\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;Date of Birth
            (DD/MM/YYYY):&lt;/label&gt;
        &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[day]\&quot; id=\&quot;dateOfBirth-day\&quot; value=\&quot;\&quot;
            size=\&quot;2\&quot; maxlength=\&quot;2\&quot;/&gt;
        /
        &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[month]\&quot; id=\&quot;dateOfBirth-month\&quot;
            value=\&quot;\&quot; size=\&quot;2\&quot; maxlength=\&quot;2\&quot;/&gt;
        /
        &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[year]\&quot; id=\&quot;dateOfBirth-year\&quot;
            value=\&quot;\&quot; size=\&quot;4\&quot; maxlength=\&quot;4\&quot;/&gt;
    &lt;/div&gt;

    &lt;div class=\&quot;element\&quot;&gt;
        &lt;label for=\&quot;password\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;Password:&lt;/label&gt;
        &lt;input type=\&quot;password\&quot; name=\&quot;password\&quot; id=\&quot;password\&quot; value=\&quot;\&quot;/&gt;
    &lt;/div&gt;

    &lt;div class=\&quot;element\&quot;&gt;
        &lt;label for=\&quot;passwordConfirmation\&quot; tag=\&quot;\&quot; class=\&quot;optional\&quot;&gt;Confirm
            Password:&lt;/label&gt;
        &lt;input type=\&quot;password\&quot; name=\&quot;passwordConfirmation\&quot;
            id=\&quot;passwordConfirmation\&quot; value=\&quot;\&quot;/&gt;
    &lt;/div&gt;

    &lt;input type=\&quot;submit\&quot; name=\&quot;submit\&quot; id=\&quot;submit\&quot; value=\&quot;Save\&quot;/&gt;
&lt;/form&gt;
</code></pre></div>

<p>
    Which looks like the following screenshot:
</p>

<p style="text-align: center;">
    <img src="/uploads/Form-Markup.png" alt="Demographics form" />
</p>

<p>
    Maybe not truly pretty, but with some CSS, it could be made to look exactly
    how you might want to see it. The main point, however, is that this form was
    generated using almost entirely custom markup, while still leveraging
    decorators for the most common markup (and to ensure things like escaping
    with htmlentities and value injection occur).
</p>

<p>
    By this point in the series, you should be getting fairly comfortable with
    the markup possibilities using <code>Zend_Form<code>'s decorators. In the
    next installment, I'll revisit the date element from above, and demonstrate
    how to create a custom element and decorator for composite elements.
</p>

<h4>Also in this series:</h4>
<ul>
    <li><a href="/matthew/archives/212-The-simplest-Zend_Form-decorator.html">The simplest Zend_Form decorator</a></li>
    <li><a href="/matthew/archives/213-From-the-inside-out-How-to-layer-decorators.html">From the inside out: How to layer decorators</a></li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;