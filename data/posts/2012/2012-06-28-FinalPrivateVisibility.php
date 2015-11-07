<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-06-28-oop-visibility');
$entry->setTitle('On Visibility in OOP');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-06-28 21:20', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-06-30 10:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'php',
  'doctrine',
  'oop',
));

$body =<<<'EOT'
<p>
    I'm a big proponent of object oriented programming. OOP done right helps 
    ease code maintenance and enables code re-use.
</p>

<p>
    Starting in PHP, OOP enthusiasts got a whole bunch of new tools, and new
    tools keep coming into the language for us with each minor release. One
    feature that has had a huge impact on frameworks and libraries has been
    available since the earliest PHP 5 versions: visibility.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Theory</h2>

<p>
    The visibility keywords include <em>private</em>, <em>protected</em>, and 
    <em>public</em>, often referred to as <strong>PPP</strong>. There's an 
    additional keyword I often lump in with them, <em>final</em>.
</p>

<p>
    Public visibility is the default, and equivalent to the only visibility
    available to PHP prior to version 5: any member declared public is 
    accessible from any scope. This means the following:
</p>

<div class="example"><pre><code class="language-php">
class Foo
{
    public $bar = 'bar';

    public function baz() 
    {
        // I can access within my own scope
        return $this->bar;
    }
}

class FooBar extends Foo
{
    public function doThat()
    {
        // I have access to members in my parent
        return $this->bar . $this->baz();
    }
}

$foo = new Foo();

// I can access public members from an instance
echo $foo->bar . $foo->baz();
</code></pre></div>

<p>
    Basically, public visibility means that I can access the member from 
    within the object, within an extending class, or from simply an instance.
</p>

<p>
    Protected visibility starts to tighten things down a little. With protected 
    visibility, only the class itself, or an extending class, can access the 
    member:
</p>

<div class="example"><pre><code class="language-php">
class Foo
{
    protected $bar = 'bar';

    protected function baz() 
    {
        // I can access within my own scope
        return $this->bar;
    }
}

class FooBar extends Foo
{
    public function doThat()
    {
        // I can access protected members in my parent
        return $this->bar . $this->baz();
    }
}

$foo = new FooBar();

// This works, as I'm calling a public member of an extending class:
$foo->doThat();

// But these are both illegal:
echo $foo->bar . $foo->baz();
</code></pre></div>

<p>
    Protected visibility is nice for hiding things from those consuming your
    class. It can be used to hide implementation details, and to prevent direct
    modification of public properties -- something important to consider, if
    a property may be the product of calculation, or if a particular type is
    required.
</p>

<p>
    Private visibility locks things down further. With private visibility, the
    object member is only directly modifiable or callable within the declaring
    class.
</p>

<div class="example"><pre><code class="language-php">
class Foo
{
    private $bar = 'bar';

    private function baz() 
    {
        // I can access within my own scope
        return $this->bar;
    }
}

class FooBar extends Foo
{
    public function doThat()
    {
        // These are both illegal
        return $this->bar . $this->baz();
    }
}

$foo = new FooBar();

// These are also both illegal:
echo $foo->bar . $foo->baz();
</code></pre></div>

<p>
    Private visibility is generally of interest for locking down algorithms. 
    For instance, if you know that a particular value or operation must not
    change, even in extending classes, declaring the member private ensures
    that extending classes cannot directly call it. 
</p>

<p>
    At any point, you can redeclare a property in an extending class using 
    equal or more public visibility. The effect of doing so depends on what
    the visibility of the member was in the parent class.
</p>

<ul>
    <li><p>In the case of a <em>public</em> property, if an extending class re-declares with 
        public visibility, any access to the member within the extending class or
        an instance of the extending class will see only the new declaration. </p>

        <div class="example"><pre><code class="language-php">
class Foo
{
    public $bar = 'bar';

    public function baz() 
    {
        return $this->bar;
    }
}

class FooBar extends Foo
{
    public $bar = 'foobar';
}

$foo = new FooBar();
echo $foo->bar;   // "foobar"
echo $foo->baz(); // "foobar"
        </code></pre></div>
    </li>

    <li>
        <p>
            In the instance of a <em>protected</em> property, if the extending class 
            re-declares with either public or protected visibility, you get the
            same behavior as public -&gt; public.
        </p>

        <div class="example"><pre><code class="language-php">
class Foo
{
    protected $bar = 'bar';

    public function baz() 
    {
        return $this->bar;
    }
}

class FooBar extends Foo
{
    public $bar = 'foobar';
}

$foo = new FooBar();
echo $foo->bar;   // "foobar"
echo $foo->baz(); // "foobar"
        </code></pre></div>
    </li>

    <li>
        <p>
            In the instance of a <em>private</em> property, things get interesting. The
            private value or method will be used for any access made within code
            declared in the parent class, but not overridden in the child. However,
            if the child class overrides any code, the value of the re-declared 
            instance will be used. This is far easier to understand via an example.
        </p>

        <div class="example"><pre><code class="language-php">
class Foo
{
    private $bar = 'bar';
    private $baz = 'baz';

    public function baz() 
    {
        return $this->bar;
    }
}

class FooBar extends Foo
{
    protected $bar = 'foobar';
    private $baz = 'foobaz';

    public function myBaz() 
    {
        return $this->bar;
    }

    public function myBaz2()
    {
        return $this->baz;
    }
}

$foo = new FooBar();
echo $foo->baz();    // "bar"
echo $foo->myBaz();  // "foobar"
echo $foo->myBaz2(); // "foobaz"
        </code></pre></div>
    </li>
</ul>

<p>
    My personal takeaway from this is:
</p>

<ul>
    <li>Use <em>public</em> for members that are safe for anything to call.</li>
    <li>Use <em>protected</em> for anything you don't want called from instance methods, 
        not important to the public API (implementation details), and anything you 
        feel is safe for extending classes to muck about with.</li>
    <li>Use <em>private</em> for any important implementation details that could 
        adversely affect execution if overridden by an extending class.</li>
</ul>

<p>
    Those paying attention will note that I skipped <em>final</em>. Actually, I 
    saved that for last. Marking a class or method <em>final</em> tells PHP that
    the class or method may not be extended or re-declared/overridden. At all. I
    lump this with visibility, because it's another way of locking down access to
    an API; marking something <em>final</em> is saying, "you cannot extend this", 
    similar to using <em>private</em>, but without even the possibility of 
    redeclaring.
</p>

<h2>Applied</h2>

<p>
    What got me to thinking about all this was a turn of events with Zend 
    Framework 2. We've had an annotation parser since last summer. <a href="http://ralphschindler.com/">Ralph
    Schindler</a> developed it in order to facilitate automatic discovery of
    injection points for our Dependency Injection container. Classes could
    mark a method with the "Inject" annotation, and the various DI compilers
    would know that that method needed to be injected.
</p>

<div class="example"><pre><code class="language-php">
use Zend\Di\Definition\Annotation\Inject;

class Foo
{
    protected $bar;

    /**
     * @Inject()
     * @param  Bar $bar
     * @return void
     */
    public function setBar(Bar $bar)
    {
        $this->bar = $bar;
    }
}

class Bar {}
</code></pre></div>

<p>
    Recently, part of our Forms RFC included a feature to allow creating
    forms and their related input filters by using annotations. Basically,
    this allows developers to hint on their domain entities how specific
    properties should be filtered, validated, and potentially represented
    at the form level.
</p>

<div class="example"><pre><code class="language-php">
use Zend\Form\Annotation;

class Foo
{
    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"Between","options":{"min":5,"max":20}})
     * @Annotation\Attributes({"type":"range"})
     */
    protected $bar;
}
</code></pre></div>

<p>
    One developer testing the support wanted to use a combination of <a 
    href="http://doctrine-project.org">Doctrine</a> annotations and ZF2 form
    annotations -- that way his entities could also describe validation and
    representation.
</p>

<p>
    I did some work to make this happen, and everybody was happy. Except then
    that same developer went to use that entity with Doctrine, and Doctrine's
    annotation parser started raising exceptions on all the ZF2 annotations.
</p>

<p>
    After some debate, I realized: (a) we were basically just making up syntax
    for our annotations; it'd be better to use an established syntax; but (b)
    we should still retain the ability to use arbitrary syntax, as we can't 
    really know what sorts of annotations developers may already be using.
</p>

<p>
    So, we decided to make our annotation component depend on the annotations
    support in Doctrine\\Common, and to use the annotation syntax they utilize.
    ZF2 would provide some code to make it possible to plug in arbitrary 
    parsers, and use the Doctrine\\Common annotation parser to parse annotations
    officially supported by ZF2.
</p>

<p>
    However, when I went to start making this happen, I ran into immediate issues.
</p>

<p>
    Remember how this post is about visibility? Well, the class I was directly
    interested in, <code>Doctrine\Common\Annotations\DocParser</code>, not only
    contains private members, but is marked <em>final</em>. 
</p>

<p>
    My immediate response was to start dissecting the class, cutting and pasting 
    the bits interesting to my solution into a new class in ZF2. I went down this
    route for several hours, gradually pulling in more and more methods as I
    discovered how far down the rabbit hole I needed to go to accomplish my task.
</p>

<p>
    But at the back of my head, I kept thinking this was a bad idea. If any
    patches ever came in for the original class, I'd need to port them into
    our ZF2 solution. And I couldn't help but think that I'd miss a crucial
    piece.
</p>

<p>
    So I started playing with its public API, to see if there were any shortcuts
    I might be able to take. And there were.
</p>

<p>
    The class has a public <code>parse()</code> method. Based on how Doctrine uses
    the code, I assumed I needed to pass a full PHP docblock in -- which ran
    counter to how I wanted to use the code. I wanted to pass in an annotation
    at a time. But when I looked closer, I realized that the parser didn't
    require a full docblock; any fragment would do.
</p>

<p>
    To make a long story short: I was able to feed the parser a single annotation
    at a time from ZF2's <code>AnnotationScanner</code>. This allowed me to build
    a very simple class that allows registering a set of annotations it can handle,
    and feeding it a single annotation string at a time to decide (a) if it supports
    it, and (b) to parse it and return the associated annotation object.
</p>

<p>
    In sum: because the class in question was marked final and had private 
    members, I found myself forced to think critically about what I wanted
    to accomplish, and then thoroughly understand the public API to see how
    I might accomplish that task without the ability to extend.
</p>

<h2>Conclusions</h2>

<p>
    Doctrine has a policy that encourages <a 
    href="http://en.wikipedia.org/wiki/Poka-yoke"><em>poka-yoke</em></a> solutions:
    code should be executable in a specific way. The policy was developed to
    both aid users (having multiple ways of doing something is often confusing),
    as well as to ease maintenance (fewer extension points means less liklihood
    of developers doing hard-to-debug things in extending code and reporting it
    back to the project). These have led them to heavily use <em>private</em>
    and <em>final</em> visibility.
</p>

<p>
    I've said it before, and I'll say it again: I feel that frameworks and libraries
    should use <em>private</em> and <em>final</em> sparingly. Over the years, I've
    seen code repurposed in simply wondrous ways -- largely due to keeping the
    code as open as possible to extension. I like to enable my users as much as 
    possible.
</p>

<p>
    That said, I can also see Doctrine's argument -- and can see where, while it
    can often be frustrating, it can also lead to potentially more sound and
    elegant solutions.
</p>

<p>
    I'll probably continue shying away from <em>private</em> and <em>final</em> 
    visibility, but I do plan to experiment with it more in the future. What 
    about you?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
