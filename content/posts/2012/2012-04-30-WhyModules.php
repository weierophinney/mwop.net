<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-04-30-why-modules');
$entry->setTitle('Why Modules?');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(false);
$entry->setCreated(new \DateTime('2012-04-30 16:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-04-30 16:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zf2',
));

$body =<<<'EOT'
<p>
    I've blogged <a href="/blog/267-Getting-started-writing-ZF2-modules.html">about 
    getting started with ZF2 modules</a>, as well as <a href="/blog/zf2-modules-you-can-use-today.html">about 
    ZF2 modules you can already use</a>. But after fielding some questions recently, 
    I realized I should talk about <em>why</em> modules are important for the 
    ZF2 ecosystem.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>History</h2>

<p>
    In the autumn of 2006, <a href="http://andigutmans.blogspot.com/">Andi</a> 
    asked me to spearhead a refactor of the Zend Framework MVC, prior to a 
    stable release. The idea was to address the growing number of issues and
    feature requests, get it well-tested, and document it thoroughly before
    we were ready for a 1.0.0 stable release.
</p>

<p>
    Late in that refactoring, a few folks approached me saying they wanted
    support for "modules". The idea would be to have self-contained directories
    containing discrete MVC functionality -- controllers, views, related models,
    etc. Additionally, they wanted routing to take into account the module, so 
    that we could have controllers with the same "name", but resolving to separate,
    discrete classes.
</p>

<p>
    The "solution" I came up with basically worked, but was quite limited. You 
    could drop modules into a directory, which the front controller would scan
    in order to be able to resolve URLs of the form "/:module/:controller/:action/*". 
    (You could also explicitly define a module in the route configuration if desired).
</p>

<p>
    This mostly worked, until we introduced <code>Zend_Application</code>, at which
    point it fell apart. Why? Because we couldn't quite get bootstrapping to work.
    Bootstrapping the application was easy, but adding modules and their bootstraps,
    and sharing dependencies between all of them, proved to be quite difficult, and 
    we never truly solved it.
</p>

<p>
    Add to this the fact that the only way to get dependencies into controllers was
    via <code>Zend_Registry</code> or the front controller singleton, and the end 
    result were modules that could never truly be shared or simply dropped into an
    application.
</p>

<h2>Modules in ZF2</h2>

<p>
    One of the very first requirements for ZF2, therefor, was to solve the module 
    problem. The goals were fairly simple:
</p>

<blockquote>
    Modules should be re-usable. Developers should be able to drop in 
    third-party modules easily, and immediately utilize them with zero or small 
    amounts of configuration.  Developers should never have to directly alter 
    module code, ever, to get them to work in their applications; customization 
    should be easily achieved via configuration or substitution.
</blockquote>

<p>
    Why?
</p>

<p>
    The goal of any good application framework or content system should be to make
    development of websites as easy as possible. Good systems make it possible to 
    use as little or as much of the framework as needed, and to make extension of 
    the framework trivial. This latter point is perhaps the most important aspect: 
    the quality of any good application ecosystem can typically be judged by the 
    amount and quality of third-party plugins developed for it. 
</p>

<p>
    If your framework is making you write boilerplate code to handle authentication
    for every site you write, or making you write code for common application 
    features such as blogs, comment systems, contact forms, etc., then something 
    is wrong. These sorts of tasks should be done at most a handful of times, and
    <em>shared</em> with the community.
</p>

<p>
    The end-goal is to be able to pull in a handful or more of plugins that do these
    tasks for you, configure them to suit your needs, and then focus on
    building out the functionality that is truly unique to your website.
</p>

<h2>Building Blocks</h2>

<p>
    I'll give a concrete example. In parallel with ZF2 development, I've been 
    rebuilding this very site. I've needed the following pieces:
</p>

<ul>
    <li>A handful of static pages (home page, r&eacute;sum&eacute;, etc.)</li>
    <li>A contact form</li>
    <li>A blog</li>
    <li>Authentication in order to "password protect" a few pages</li>
    <li>A few view helpers (github status, disqus display, etc)</li>
</ul>

<p>
    How much of this functionality is unique to my site, other than the content? Pretty
    much none of it. Ideally, I should be able to find some modules, drop them in, and 
    create some custom view scripts.
</p>

<p>
    Which is what I did. That said, I developed several of the modules, but in some cases,
    such as authentication, I was able to grab modules from elsewhere. The beauty, though,
    is that in the future, I or others can re-use what I've created, and quite easily.
</p>

<p>
    This kind of building-block development makes <em>your</em> job easier as a developer -- 
    and allows you to focus on the bits and pieces that make your site unique. As such, I 
    truly feel that <em><strong>modules are the most important new feature of ZF2</strong></em>.
</p>

<h2>Fin</h2>

<p>
    If you're developing on top of ZF2 today, I have one piece of advice: 
    create and consume modules. Share your modules.  Help make ZF2 a productive, 
    fun, collaborative ecosystem that allows developers to get
    things done and create fantastic new applications.
</p>
EOT;
$entry->setExtended($extended);

return $entry;

