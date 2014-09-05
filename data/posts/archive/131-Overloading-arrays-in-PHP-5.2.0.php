<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('131-Overloading-arrays-in-PHP-5.2.0');
$entry->setTitle('Overloading arrays in PHP 5.2.0');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1169152740);
$entry->setUpdated(1169413239);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <b>Update:</b> I ran into issues with the ArrayObject solution, as there was
    a bug in PHP 5.2.0 (now fixed) with its interaction with empty() and isset()
    when used with the ARRAY_AS_PROPS flag. I tried a number of fixes, but
    eventually my friend <a href="http://mikenaberezny.com/">Mike</a> pointed
    out something I'd missed: as of PHP 5.1, setting undefined public properties
    no longer raises an E_STRICT notice. Knowing this, you can now do the
    following without raising any errors:
</p>
<div class="example"><pre><code lang="php">
class Foo
{
    public function __set($key, $value)
    {
        $this-&gt;$key = $value;
    }
}

$foo        = new Foo();
$foo-&gt;bar   = array();
$foo-&gt;bar[] = 42;
</code></pre></div>
<p>
    This is a much simpler solution, performs better, and solves all the issues
    I was presented. Thanks, Mike!
</p>
<hr />
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Several weeks back, a bug was reported against 
    <a href="http://framework.zend.com/manual/en/zend.view.html">Zend_View</a>
    that had me initially stumped. Basically, the following was now failing in
    PHP 5.2.0:
</p>
<div class="example"><pre><code lang="php">
$view-&gt;foo   = array();
$view-&gt;foo[] = 42;
</code></pre></div>
<p>
    A notice was raised stating, "Notice: Indirect modification of overloaded
    property Zend_View::$foo has no effect."
</p>
<p>
    I'd read about this some months back on the php internals list, but at the
    time hadn't understood the consequences. Basically, __get() no longer
    returns a reference and returns values in read mode, which makes modifying
    arrays using overloading impossible using traditional methods.
</p>
<p>
    Derick Rethans <a href="http://derickrethans.nl/overloaded_properties_get.php">blogged about the issue</a> 
    in August. His solution was to use a switch() statement in __get() to cast
    the returned value explicitly as an array:
</p>
<div class="example"><pre><code lang="php">
public function __get($key)
{
    if (is_array($this-&gt;_vars[$key])) {
        return (array) $this-&gt;_vars[$key];
    }

    return $this-&gt;_vars[$key];
}
</code></pre></div>
<p>
    The problem with this approach is that you then have issues with other array
    functionalities, such as assigning by reference.
</p>
<p>
    After some work, I found the best solution was to have the class extend
    ArrayObject, but with a slight twist:
</p>
<div class="example"><pre><code lang="php">
class My_Class extends ArrayObject
{
    public function __construct($config = array())
    {
        // ... some setup

        // Allow accessing properties as either array keys or object properties:
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
    }
}
</code></pre></div>
<p>
    This combination allows some very flexible access to properties in the
    object:
</p>
<div class="example"><pre><code lang="php">
// from the original example:
$view-&gt;foo   = array();
$view-&gt;foo[] = 42;

echo $view['foo'][0]; // '42'
echo $view-&gt;foo[0];   // same
</code></pre></div>
<p>
    One issue that was always difficult to work with in Zend_View was keeping
    'public' properties -- template variables -- separate from private/protected
    properties (things like the helper, filter, and script paths). Since those
    properties are pre-declared in the class, the
    <kbd>ArrayObject::ARRAY_AS_PROPS</kbd> setting prevented any such collision
    from happening -- and helped simplify the code.
</p>
<p>
    Moral of the story? If you need to be able to modify overloaded arrays in
    your class, and support PHP 5.2.0, extend ArrayObject.
</p>
EOT;
$entry->setExtended($extended);

return $entry;