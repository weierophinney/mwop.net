<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('181-Migrating-OOP-Libraries-and-Frameworks-to-PHP-5.3');
$entry->setTitle('Migrating OOP Libraries and Frameworks to PHP 5.3');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1214830800);
$entry->setUpdated(1215402554);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'oop',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    With PHP 5.3 coming up on the horizon, I'm of course looking forward to
    using namespaces. Let's be honest, who wants to write the following line?
</p>

<div class="example"><pre><code lang="php">
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
</code></pre></div>

<p>
    when the more succinct:
</p>

<div class="example"><pre><code lang="php">
$viewRenderer = HelperBroker::getStaticHelper('viewRenderer');
</code></pre></div>

<p>
    could be used? (Assuming you've executed <code>'use
    Zend::Controller::Action;'</code> somewhere earlier...)
</p>

<p>
    However, while namespaces will hopefully lead to more readable code,
    particularly code in libraries and frameworks, PHP developers will finally
    need to start thinking about sane standards for abstract classes and
    interfaces. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    For instance, we've been doing things like the following in Zend
    Framework:
</p>

<ul>
     <li>Zend_Controller_Request_Abstract</li>
     <li>Zend_View_Interface</li>
</ul>

<p>
    These conventions make it really easy to find Abstract classes and
    Interfaces using <code>find</code> or <code>grep</code>, and also are
    predictable and easy to understand. However, they won't play well with
    namespaces. Why? Consider the following:
</p>

<div class="example"><pre><code lang="php">
namespace Zend::Controller::Request

class Http extends Abstract
{
    // ...
}
</code></pre></div>

<p>
    Spot the problem? 'Abstract' is a reserved word in PHP. The same goes for
    interfaces. Consider this particularly aggregious example:
</p>


<div class="example"><pre><code lang="php">
namespace Zend::View

abstract class Abstract implements Interface
{
    // ...
}
</code></pre></div>

<p>
    We've got two reserved words there: Abstract <em>and</em> Interface.
</p>

<p>
    <a href="http://php100.wordpress.com/">Stas</a>, Dmitry, and I sat down to
    discuss this a few weeks ago to come up with a plan for migrating to PHP
    5.3. In other OOP languages, such as Python, C#, interfaces are denoted by
    prefixing the interface with a capital 'I'; in the example above, we would
    then have <code>Zend::View::IView</code>. We decided this would be a sane
    step, as it would keep the interface within the namespace, and visually
    denote it as well. We also decided that this convention made sense for
    abstract classes: <code>Zend::View::AView</code>. So, our two examples
    become:
</p>

<div class="example"><pre><code lang="php">
namespace Zend::Controller::Request

class Http extends ARequest
{
    // ...
}
</code></pre></div>

<p>and:</p>

<div class="example"><pre><code lang="php">
namespace Zend::View

abstract class AView implements IView
{
    // ...
}
</code></pre></div>

<p>
    Another thing that looks likely to affect OOP libraries and frameworks is
    autoloading, specifically when using exceptions. For instance, consider
    this:
</p>

<div class="example"><pre><code lang="php">
namespace Foo::Bar

class Baz
{
    public function status()
    {
        throw new Exception(\&quot;This isn't what you think it is\&quot;);
    }
}
</code></pre></div>

<p>
    You'd expect the exception to be of class <code>Foo::Bar::Exception</code>,
    right? Wrong; it'll be a standard <code>Exception</code>. To get around
    this, you can do the following:
</p>

<div class="example"><pre><code lang="php">
namespace Foo::Bar

class Baz
{
    public function status()
    {
        throw new namespace::Exception(\&quot;This is exactly what you think it is\&quot;);
    }
}
</code></pre></div>

<p>
    By using the <code>namespace</code> keyword, you're telling the PHP engine
    to explicitly use the Exception class from the current namespace. I also
    find this to be more semantically correct -- it's more explicit that you're
    throwing a particular type of exception, and makes it easy to find and
    replace these with alternate declarations at a later date.
</p>

<p>
    I'd like to recommend other libraries adopt similar standards -- they're
    sensible, and fit already within PEAR/Horde/ZF coding standards. What say
    you?
</p>
EOT;
$entry->setExtended($extended);

return $entry;