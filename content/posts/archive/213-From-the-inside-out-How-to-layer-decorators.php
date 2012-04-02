<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('213-From-the-inside-out-How-to-layer-decorators');
$entry->setTitle('From the inside-out: How to layer decorators');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1239021000);
$entry->setUpdated(1239543660);
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
    This marks the second in an on-going series on <code>Zend_Form</code>
    decorators.
</p>

<p>
    You may have noticed in the <a href="/matthew/archives/212-The-simplest-Zend_Form-decorator.html">previous installment</a> 
    that the decorator's <code>render()</code> method takes a single argument,
    <code>$content</code>. This is expected to be a string.
    <code>render()</code> will then take this string and decide to either
    replace it, append to it, or prepend it. This allows you to have a chain of
    decorators -- which allows you to create decorators that render only a
    subset of the element's metadata, and then layer these decorators to build
    the full markup for the element. 
</p>

<p>
    Let's look at how this works in practice.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    For most form element types, the following decorators are used:
</p>

<ul>
    <li>ViewHelper (render the form input using one of the standard form view
        helpers)</li>
    <li>Errors (render validation errors via an unordered list)</li>
    <li>Description (render any description attached to the element; often used
        for tooltips)</li>
    <li>HtmlTag (wrap all of the above in a <code>&lt;dd&gt;</code> tag</li>
    <li>Label (render the label preceding the above, wrapped in a
        <code>&lt;dt&gt; tag</li>
</ul>

<p>
    You'll notice that each of these decorators does just one thing, and
    operates on one specific piece of metadata stored in the form element: the
    "Errors" decorator pulls validation errors and renders them; the "Label"
    decorator pulls just the label and renders it. This allows the individual
    decorators to be very succinct, repeatable, and, more importantly, testable.
</p>

<p>
    It's also where that <code>$content</code> argument comes into play: each
    decorator's <code>render()</code> method is designed to accept content, and
    then either replace it (usually by wrapping it), prepend to it, or append
    to it.
</p>

<p>
    So, it's best to think of the process of decoration as one of building an
    onion from the inside out.
</p>

<p>
    To simplify the process, we'll take a look at the example from the previous
    entry in this series. Recall:
</p>

<div class="example"><pre><code lang="php">
class My_Decorator_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $_format = '&lt;label for=\&quot;%s\&quot;&gt;%s&lt;/label&gt;&lt;input id=\&quot;%s\&quot; name=\&quot;%s\&quot; type=\&quot;text\&quot; value=\&quot;%s\&quot;/&gt;';

    public function render($content)
    {
        $element = $this-&gt;getElement();
        $name    = htmlentities($element-&gt;getFullyQualifiedName());
        $label   = htmlentities($element-&gt;getLabel());
        $id      = htmlentities($element-&gt;getId());
        $value   = htmlentities($element-&gt;getValue());

        $markup  = sprintf($this-&gt;_format, $id, $label, $id, $name, $value);
        return $markup;
    }
}
</code></pre></div>

<p>
    Let's now remove the label functionality, and build a separate decorator
    for that.
</p>

<div class="example"><pre><code lang="php">
class My_Decorator_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $_format = '&lt;input id=\&quot;%s\&quot; name=\&quot;%s\&quot; type=\&quot;text\&quot; value=\&quot;%s\&quot;/&gt;';

    public function render($content)
    {
        $element = $this-&gt;getElement();
        $name    = htmlentities($element-&gt;getFullyQualifiedName());
        $id      = htmlentities($element-&gt;getId());
        $value   = htmlentities($element-&gt;getValue());

        $markup  = sprintf($this-&gt;_format, $id, $name, $value);
        return $markup;
    }
}

class My_Decorator_SimpleLabel extends Zend_Form_Decorator_Abstract
{
    protected $_format = '&lt;label for=\&quot;%s\&quot;&gt;%s&lt;/label&gt;';

    public function render($content)
    {
        $element = $this-&gt;getElement();
        $id      = htmlentities($element-&gt;getId());
        $label   = htmlentities($element-&gt;getLabel());

        $markup = sprintf($this-&gt;_format, $id, $label);
        return $markup;
    }
}
</code></pre></div>

<p>
    Now, this may look all well and good, but here's the problem: as written
    currently, the last decorator to run wins, and overwrites everything.
    You'll end up with just the input, or just the label, depending on which
    you register last.
</p>

<p>
    To overcome this, simply concatenate the passed in <code>$content</code>
    with the markup somehow:
</p>

<div class="example"><pre><code lang="php">
return $content . $markup;
</code></pre></div>

<p>
    The problem with the above approach comes when you want to programmatically
    choose whether the original content should precede or append the new
    markup. Fortunately, there's a standard mechanism for this already;
    <code>Zend_Form_Decorator_Abstract</code> has a concept of placement and
    defines some constants for matching it. Additionally, it allows specifying
    a separator to place between the two. Let's make use of those:
</p>

<div class="example"><pre><code lang="php">
class My_Decorator_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $_format = '&lt;input id=\&quot;%s\&quot; name=\&quot;%s\&quot; type=\&quot;text\&quot; value=\&quot;%s\&quot;/&gt;';

    public function render($content)
    {
        $element = $this-&gt;getElement();
        $name    = htmlentities($element-&gt;getFullyQualifiedName());
        $id      = htmlentities($element-&gt;getId());
        $value   = htmlentities($element-&gt;getValue());

        $markup  = sprintf($this-&gt;_format, $id, $name, $value);

        $placement = $this-&gt;getPlacement();
        $separator = $this-&gt;getSeparator();
        switch ($placement) {
            case self::PREPEND:
                return $markup . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $markup;
        }
    }
}

class My_Decorator_SimpleLabel extends Zend_Form_Decorator_Abstract
{
    protected $_format = '&lt;label for=\&quot;%s\&quot;&gt;%s&lt;/label&gt;';

    public function render($content)
    {
        $element = $this-&gt;getElement();
        $id      = htmlentities($element-&gt;getId());
        $label   = htmlentities($element-&gt;getLabel());

        $markup = sprintf($this-&gt;_format, $id, $label);

        $placement = $this-&gt;getPlacement();
        $separator = $this-&gt;getSeparator();
        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $markup;
            case self::PREPEND:
            default:
                return $markup . $separator . $content;
        }
    }
}
</code></pre></div>

<p>
    Notice in the above that I'm switching the default case for each; the
    assumption will be that labels prepend content, and input appends.
</p>

<p>
    Now, let's create a form element that uses these:
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
        'SimpleInput',
        'SimpleLabel',
    ),
));
</code></pre></div>

<p>
    How will this work? When we call <code>render()</code>, the element will
    iterate through the various attached decorators, calling
    <code>render()</code> on each. It will pass an empty string to the very
    first, and then whatever content is created will be passed to the next, and
    so on:
</p>

<ul>
    <li>Initial content is an empty string: ''</li>
    <li>'' is passed to the SimpleInput decorator, which then generates a form
        input that it appends to the empty string: <code>&lt;input id="bar-foo"
        name="bar[foo]" type="text" value="test"/&gt;</code></li>
    <li>The input is then passed as content to the SimpleLabel decorator, which
        generates a label and prepends it to the original content; the default
        separator is a PHP_EOL character, giving us this: <code>&lt;label
        for="bar-foo"&gt;\n&lt;input id="bar-foo" name="bar[foo]" type="text"
        value="test"/&gt;</code></li>
</ul>

<p>
    But wait a second! What if you wanted the label to come after the input for
    some reason? Remember that "placement" flag? You can pass it as an option
    to the decorator. The easiest way to do this is to pass an array of options
    with the decorator during element creation:
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
        'SimpleInput',
        array('SimpleLabel', array('placement' =&gt; 'append')),
    ),
));
</code></pre></div>

<p>
    Notice that when passing options, you must wrap the decorator within an
    array; this hints to the constructor that options are available. The
    decorator name is the first element of the array, and options are passed in
    an array to the second element of the array.
</p>

<p>
    The above results in the markup <code>&lt;input id="bar-foo"
    name="bar[foo]" type="text" value="test"/&gt;\n&lt;label
    for="bar-foo"&gt;</code>.
</p>

<p>
    Using this technique, you can have decorators that target specific metadata
    of the element or form and create only the markup relevant to that
    metadata; by using mulitiple decorators, you can then build up the complete
    element markup. Our onion is the result.
</p>

<p>
    There are pros and cons to this approach. First, the cons:
</p>

<ul>
    <li>More complex to implement. You have to pay careful attention to the
        decorators you use and what placement you utilize in order to build up
        the markup in the correct sequence.</li>
    <li>More resource intensive. More decorators means more objects; multiply
        this by the number of elements you have in a form, and you may end up
        with some serious resource usage. Caching can help here.</li>
</ul>

<p>
    The advantages are compelling, though:
</p>

<ul>
    <li>Reusable decorators. You can create truly re-usable decorators with
        this technique, as you don't have to worry about the complete markup,
        but only markup for one or a few pieces of element/form metadata.</li>
    <li>Ultimate flexibility. You can theoretically generate any markup
        combination you want from a small number of decorators.</li>
</ul>

<p>
    While the above examples are the intended usage of decorators within
    <code>Zend_Form</code>, it's often hard to wrap your head around how the
    decorators interact with one another to build the final markup. For this
    reason, some flexibility was added in the 1.7 series to make rendering
    individual decorators possible -- which gives some Rails-like simplicity to
    rendering forms. Tune in to the next installment to see some of these
    techniques.
</p>

<h4>Updates</h4>
<ul>
    <li>2009-04-06 16:00-0500: updated append/prepend in SimpleLabel based on
        Mark's comment</li>
    <li>2009-04-07 08:50-0500: fixed typos in two examples, per mzeis</li>
    <li>2009-04-12 09:35-0500: fixed sprint to sprintf in two examples, per
        note from Joseph M.</li>
</ul>

<h4>Other articles in this series</h4>
<ul>
    <li><a href="/matthew/archives/212-The-simplest-Zend_Form-decorator.html">The simplest Zend_Form decorator</a></li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;