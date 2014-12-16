<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('217-Creating-composite-elements');
$entry->setTitle('Creating composite elements');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1239625800);
$entry->setUpdated(1239967874);
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
    In my <a href="/matthew/archives/215-Rendering-Zend_Form-decorators-individually.html">last post on decorators</a>,
    I had an example that showed rendering a "date of birth" element:
</p>

<div class="example"><pre><code class="language-php">
&lt;div class=\&quot;element\&quot;&gt;
    &lt;?php echo $form-&gt;dateOfBirth-&gt;renderLabel() ?&gt;
    &lt;?php echo $this-&gt;formText('dateOfBirth[day]', '', array(
        'size' =&gt; 2, 'maxlength' =&gt; 2)) ?&gt;
    /
    &lt;?php echo $this-&gt;formText('dateOfBirth[month]', '', array(
        'size' =&gt; 2, 'maxlength' =&gt; 2)) ?&gt;
    /
    &lt;?php echo $this-&gt;formText('dateOfBirth[year]', '', array(
        'size' =&gt; 4, 'maxlength' =&gt; 4)) ?&gt;
&lt;/div&gt;
</code></pre></div>

<p>
    This has prompted some questions about how this element might be represented
    as a <code>Zend_Form_Element</code>, as well as how a decorator might be
    written to encapsulate this logic. Fortunately, I'd already planned to
    tackle those very subjects for this post!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>The Element</h2>

<p>
    The questions about how the element would work include:
</p>

<ul>
    <li>How would you set and retrieve the value?</li>
    <li>How would you validate the value?</li>
    <li>Regardless, how would you then allow for discrete form inputs for the
    three segments (day, month, year)?</li>
</ul>

<p>
    The first two questions center around the form element itself: how would
    <code>setValue()</code> and <code>getValue()</code> work? There's actually
    another question implied by the question about the decorator: how would you
    retrieve the discrete date segments from the element and/or set them?
</p>

<p>
    The solution is to override the <code>setValue()</code> method of your
    element to provide some custom logic. In this particular case, our element
    should have three discrete behaviors:
</p>

<ul>
    <li>If an integer timestamp is provided, it should be used to determine and
        store the day, month, and year</li>
    <li>If a textual string is provided, it should be cast to a timestamp, and
        then that value used to determine and store the day, month, and
        year</li>
    <li>If an array containing keys for date, month, and year is provided, those
        values should be stored</li>
</ul>

<p>
    Internally, the day, month, and year will be stored discretely. When the
    value of the element is retrieved, it will be done so in a normalized
    string format. We'll override <code>getValue()</code> as well to assemble
    the discrete date segments into a final string.
</p>

<p>
    Here's what the class would look like:
</p>

<div class="example"><pre><code class="language-php">

&lt;?php
class My_Form_Element_Date extends Zend_Form_Element_Xhtml
{
    protected $_dateFormat = '%year%-%month%-%day%';
    protected $_day;
    protected $_month;
    protected $_year;

    public function setDay($value)
    {
        $this-&gt;_day = (int) $value;
        return $this;
    }

    public function getDay()
    {
        return $this-&gt;_day;
    }

    public function setMonth($value)
    {
        $this-&gt;_month = (int) $value;
        return $this;
    }

    public function getMonth()
    {
        return $this-&gt;_month;
    }

    public function setYear($value)
    {
        $this-&gt;_year = (int) $value;
        return $this;
    }

    public function getYear()
    {
        return $this-&gt;_year;
    }

    public function setValue($value)
    {
        if (is_int($value)) {
            $this-&gt;setDay(date('d', $value))
                 -&gt;setMonth(date('m', $value))
                 -&gt;setYear(date('Y', $value));
        } elseif (is_string($value)) {
            $date = strtotime($value);
            $this-&gt;setDay(date('d', $date))
                 -&gt;setMonth(date('m', $date))
                 -&gt;setYear(date('Y', $date));
        } elseif (is_array($value)
            &amp;&amp; (isset($value['day']) 
                &amp;&amp; isset($value['month']) 
                &amp;&amp; isset($value['year'])
            )
        ) {
            $this-&gt;setDay($value['day'])
                 -&gt;setMonth($value['month'])
                 -&gt;setYear($value['year']);
        } else {
            throw new Exception('Invalid date value provided');
        }

        return $this;
    }

    public function getValue()
    {
        return str_replace(
            array('%year%', '%month%', '%day%'),
            array($this-&gt;getYear(), $this-&gt;getMonth(), $this-&gt;getDay()),
            $this-&gt;_dateFormat
        );
    }
}
</code></pre></div>

<p>
    This class gives some nice flexibility -- we can set default values from our
    database, and be certain that the value will be stored and represented
    correctly.  Additionally, we can allow for the value to be set from an array
    passed via form input. Finally, we have discrete accessors for each date
    segment, which we can now use in a decorator to create a composite element.
</p>

<h2>The Decorator</h2>

<p>
    Revisiting the example from the last post, let's assume that we want users
    to input each of the year, month, and day separately. PHP fortunately allows
    us to use array notation when creating elements, so it's still possible to
    capture these three entities into a single value -- and we've now created a
    <code>Zend_Form</code> element that can handle such an array value.
</p>

<p>
    The decorator is relatively simple: it will grab the day, month, and year
    from the element, and pass each to a discrete view helper to render
    individual form inputs; these will then be aggregated to form the final
    markup.
</p>

<div class="example"><pre><code class="language-php">
class My_Form_Decorator_Date extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this-&gt;getElement();
        if (!$element instanceof My_Form_Element_Date) {
            // only want to render Date elements
            return $content;
        }

        $view = $element-&gt;getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

        $day   = $element-&gt;getDay();
        $month = $element-&gt;getMonth();
        $year  = $element-&gt;getYear();
        $name  = $element-&gt;getFullyQualifiedName();

        $params = array(
            'size'      =&gt; 2,
            'maxlength' =&gt; 2,
        );
        $yearParams = array(
            'size'      =&gt; 4,
            'maxlength' =&gt; 4,
        );

        $markup = $view-&gt;formText($name . '[day]', $day, $params)
                . ' / ' . $view-&gt;formText($name . '[month]', $month, $params)
                . ' / ' . $view-&gt;formText($name . '[year]', $year, $yearParams);

        switch ($this-&gt;getPlacement()) {
            case self::PREPEND:
                return $markup . $this-&gt;getSeparator() . $content;
            case self::APPEND:
            default:
                return $content . $this-&gt;getSeparator() . $markup;
        }
    }
}
</code></pre></div>

<p>
    We now have to do a minor tweak to our form element, and tell it that we
    want to use the above decorator as a default. That takes two steps. First,
    we need to inform the element of the decorator path. We can do that in the
    constructor:
</p>

<div class="example"><pre><code class="language-php">
class My_Form_Element_Date extends Zend_Form_Element_Xhtml
{
    // ...

    public function __construct($spec, $options = null)
    {
        $this-&gt;addPrefixPath(
            'My_Form_Decorator', 
            'My/Form/Decorator', 
            'decorator'
        );
        parent::__construct($spec, $options);
    }

    // ...
}
</code></pre></div>

<p>
    Note that I'm doing this in the constructor and not in <code>init()</code>.
    This is for two reasons. First, it allows me to extend the element later to
    add logic in <code>init</code> without needing to worry about calling
    <code>parent::init()</code>. Second, it allows me to pass additional plugin
    paths via configuration or within an <code>init</code> method that will then
    allow me to override the default Date decorator with my own replacement.
</p>

<p>
    Next, we need to override the <code>loadDefaultDecorators()</code> method to
    use our new Date decorator:
</p>

<div class="example"><pre><code class="language-php">
class My_Form_Element_Date extends Zend_Form_Element_Xhtml
{
    // ...

    public function loadDefaultDecorators()
    {
        if ($this-&gt;loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this-&gt;getDecorators();
        if (empty($decorators)) {
            $this-&gt;addDecorator('Date')
                 -&gt;addDecorator('Errors')
                 -&gt;addDecorator('Description', array(
                    'tag' =&gt; 'p', 
                    'class' =&gt; 'description')
                 )
                 -&gt;addDecorator('HtmlTag', array(
                    'tag' =&gt; 'dd',
                    'id'  =&gt; $this-&gt;getName() . '-element')
                 )
                 -&gt;addDecorator('Label', array('tag' =&gt; 'dt'));
        }
    }

    // ...
}
</code></pre></div>

<p>
    What does the final output look like? Let's consider the following element:
</p>

<div class="example"><pre><code class="language-php">
$d = new My_Form_Element_Date('dateOfBirth');
$d-&gt;setLabel('Date of Birth: ')
  -&gt;setView(new Zend_View());

// These are equivalent:
$d-&gt;setValue('20 April 2009');
$d-&gt;setValue(array('year' =&gt; '2009', 'month' =&gt; '04', 'day' =&gt; '20'));
</code></pre></div>

<p>
    If you then echo this element, you get the following markup (with some
    slight whitespace modifications for readability):
</p>

<div class="example"><pre><code class="language-php">
&lt;dt id=\&quot;dateOfBirth-label\&quot;&gt;&lt;label for=\&quot;dateOfBirth\&quot; class=\&quot;optional\&quot;&gt;
    Date of Birth:
&lt;/label&gt;&lt;/dt&gt;
&lt;dd id=\&quot;dateOfBirth-element\&quot;&gt;
    &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[day]\&quot; id=\&quot;dateOfBirth-day\&quot; value=\&quot;20\&quot;
        size=\&quot;2\&quot; maxlength=\&quot;2\&quot;&gt; / 
    &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[month]\&quot; id=\&quot;dateOfBirth-month\&quot;
        value=\&quot;4\&quot; size=\&quot;2\&quot; maxlength=\&quot;2\&quot;&gt; / 
    &lt;input type=\&quot;text\&quot; name=\&quot;dateOfBirth[year]\&quot; id=\&quot;dateOfBirth-year\&quot;
        value=\&quot;2009\&quot; size=\&quot;4\&quot; maxlength=\&quot;4\&quot;&gt;
&lt;/dd&gt;
</code></pre></div>

<h2>Conclusion</h2>

<p>
    We now have an element that can render multiple related form input fields,
    and then handle the aggregated fields as a single entity -- the
    <code>dateOfBirth</code> element will be passed as an array to the element,
    and the element will then, as we noted earlier, create the appropriate date
    segments and return a value we can use for most backends.
</p>

<p>
    Additionally, we can use different decorators with the element. If we wanted
    to use a <a href="http://dojotoolkit.org/">Dojo</a> DateTextBox dijit
    decorator -- which accepts and returns string values -- we can, with no
    modifications to the element itself.
</p>

<p>
    In the end, you get a uniform element API you can use to describe an element
    representing a composite value.
</p>

<h3>Other posts in this series:</h3>

<ul>
    <li><a href="/matthew/archives/212-The-simplest-Zend_Form-decorator.html">The simplest Zend_Form decorator</a></li>
    <li><a href="/matthew/archives/213-From-the-inside-out-How-to-layer-decorators.html">From the inside out: How to layer decorators</a></li>
    <li><a href="/matthew/archives/215-Rendering-Zend_Form-decorators-individually.html">Rendering Zend_Form decorators individually</a></li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
