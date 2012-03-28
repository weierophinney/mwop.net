<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('212-The-simplest-Zend_Form-decorator');
$entry->setTitle('The simplest Zend_Form decorator');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1238761800);
$entry->setUpdated(1239114030);
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
    I've been seeing ranting and general confusion about 
    <a href="http://framework.zend.com/manual/en/zend.form.html">Zend_Form</a> 
    decorators (as well as the occasional praises), and thought I'd do a
    mini-series of blog posts showing how they work.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First, some background on the <a href="http://en.wikipedia.org/wiki/Decorator_pattern">Decorator design pattern</a>. 
    One common technique is to define a common interface that both your
    originating object and decorator will implement; your decorator than accepts
    the originating object as a dependency, and will either proxy to it or
    override its methods. Let's put that into code to make it more easily
    understood:
</p>

<div class="example"><pre><code lang="php">
interface Window
{
    public function isOpen();
    public function open();
    public function close();
}

class StandardWindow implements Window
{
    protected $_open = false;

    public function isOpen()
    {
        return $this-&gt;_open;
    }

    public function open()
    {
        if (!$this-&gt;_open) {
            $this-&gt;_open = true;
        }
    }

    public function close()
    {
        if ($this-&gt;_open) {
            $this-&gt;_open = false;
        }
    }
}

class LockedWindow implements Window
{
    protected $_window;

    public function __construct(Window $window)
    {
        $this-&gt;_window = $window;
        $this-&gt;_window-&gt;close();
    }

    public function isOpen()
    {
        return false;
    }

    public function open()
    {
        throw new Exception('Cannot open locked windows');
    }

    public function close()
    {
        $this-&gt;_window-&gt;close();
    }
}
</code></pre></div>

<p>
    You then create an object of type <code>StandardWindow</code>, pass it to
    the constructor of <code>LockedWindow</code>, and your window instance now
    has different behavior. The beauty is that you don't have to implement any
    sort of "locking" functionality on your standard window class -- the
    decorator takes care of that for you. In the meantime, you can pass your
    locked window around as if it were just another window.
</p>

<p>
    One particular place where the decorator pattern is useful is for creating
    textual representations of objects. As an example, you might have a "Person"
    object that, by itself, has no textual representation. By using the
    Decorator pattern, you can create an object that will act as if it were a
    Person, but also provide the ability to render that Person textually.
</p>

<p>
    In this particular example, we're going to use 
    <a href="http://en.wikipedia.org/wiki/Duck_typing">duck typing</a> instead 
    of an explicit interface. This allows our implementation to be a bit more
    flexible, while still allowing the decorator object to act exactly as if it
    were a Person object.
</p>

<div class="example"><pre><code lang="php">
class Person
{
    public function setFirstName($name) {}
    public function getFirstName() {}
    public function setLastName($name) {}
    public function getLastName() {}
    public function setTitle($title) {}
    public function getTitle() {}
}

class TextPerson
{
    protected $_person;

    public function __construct(Person $person)
    {
        $this-&gt;_person = $person;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this-&gt;_person, $method)) {
            throw new Exception('Invalid method called on TextPerson: ' .  $method);
        }
        return call_user_func_array(array($this-&gt;_person, $method), $args);
    }

    public function __toString()
    {
        return $this-&gt;_person-&gt;getTitle() . ' '
               . $this-&gt;_person-&gt;getFirstName() . ' '
               . $this-&gt;_person-&gt;getLastName();
    }
}
</code></pre></div>

<p>
    In this example, you pass your Person instance to the TextPerson
    constructor. By using method overloading, you are able to continue to call
    all the methods of Person -- to set the first name, last name, or title --
    but you also now gain a string representation via the
    <code>__toString()</code> method.
</p>

<p>
    This latter example is getting close to how <code>Zend_Form</code>
    decorators work. The key difference is that instead of a decorator wrapping
    the element, the element has one or more decorators attached to it that it
    then injects itself into in order to render.  The decorator then can access
    the element's methods and properties in order to create a representation of
    the element -- or a subset of it.
</p>

<p>
    <code>Zend_Form</code> decorators all implement a common interface,
    <code>Zend_Form_Decorator_Interface</code>. That interface provides the
    ability to set decorator-specific options, register and retrieve the
    element, and render. A base decorator,
    <code>Zend_Form_Decorator_Abstract</code>, provides most of the
    functionality you will ever need, with the exception of the rendering logic.
</p>

<p>
    Let's consider a situation where we simply want to render an element as a
    standard form text input with a label. We won't worry about error handling
    or whether or not the element should be wrapped within other tags for now --
    just the basics. Such a decorator might look like this:
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
    Let's create an element that uses this decorator:
</p>

<div class="example"><pre><code lang="php">
$decorator = new My_Decorator_SimpleInput();
$element   = new Zend_Form_Element('foo', array(
    'label'      =&gt; 'Foo',
    'belongsTo'  =&gt; 'bar',
    'value'      =&gt; 'test',
    'decorators' =&gt; array($decorator),
));
</code></pre></div>

<p>
    Rendering this element results in the following markup:
</p>

<pre>
&lt;label for="bar-foo"&gt;Foo&lt;/label&gt;&lt;input id="bar-foo" name="bar[foo]" type="text" value="test"/&gt;
</pre>

<p>
    You could also put this class in your library somewhere, inform
    your element of that path, and refer to the decorator as simply
    "SimpleInput" as well:
</p>

<div class="example"><pre><code lang="php">
$element = new Zend_Form_Element('foo', array(
    'label'      =&gt; 'Foo',
    'belongsTo'  =&gt; 'bar',
    'value'      =&gt; 'test',
    'prefixPath' =&gt; array('decorator' =&gt; array(
        'My_Decorator' =&gt; 'path/to/decorators/',
    )),
    'decorators' =&gt; array('SimpleInput'),
));
</code></pre></div>

<p>
    This gives you the benefit of re-use in other projects, and also opens the
    door for providing alternate implementations of that decorator later (a
    topic for another post).
</p>

<p>
    Hopefully, the above overview of the decorator pattern and this simple
    example will shed some light on how you can begin writing decorators.
    I'll be writing additional posts in the coming weeks showing how to leverage
    decorators to build more complex markup, and will update this post to link
    to them as they are written.
</p>

<p>
    <b>Update:</b> Fixed text in thrown exception to reflect actual class name;
    updated label generation to use id for "for" attribute, per comment from
    David.
</p>

<h4>Also in this series:</h4>
<ul>
    <li><a href="/matthew/archives/213-From-the-inside-out-How-to-layer-decorators.html">From the inside out: How to layer decorators</a></li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;