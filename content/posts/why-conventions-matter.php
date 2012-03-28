<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('why-conventions-matter');
$entry->setTitle('Why Conventions Matter');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1326340708);
$entry->setUpdated(1326340708);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'perl',
  2 => 'zend framework',
  3 => 'zf2',
));

$body =<<<'EOT'
<p>
    When I started teaching myself scripting languages, I started with Perl. One
    Perl motto is <a href="http://en.wikipedia.org/wiki/TMTOWTDI">"TMTOWTDI"</a>
    -- "There's More Than One Way To Do It," and pronounced "tim-toady." The
    idea is that there's likely multiple ways to accomplish the very same thing,
    and the culture of the language encourages finding novel ways to do things.
</p>

<p>
    I've seen this principle used everywhere and in just about every programming
    situation possible, applied to logical operations, naming conventions,
    formatting, and even project structure.  Everyone has an opinion on these
    topics, and given free rein to implement as they see fit, it's rare that two
    developers will come up with the same conventions.
</p>

<p>
    TMTOWTDI is an incredibly freeing and egalitarian principle.
</p>

<p>
    Over the years, however, my love for TMTOWTDI has diminished some.  Freeing
    as it is, is also a driving force behind having coding standards and
    conventions -- because when everyone does it their own way, projects become
    quickly hard to maintain. Each person finds themselves reformatting code to
    their own standards, simply so they can read it and follow its flow.
</p>

<p>
    Additionally, TMTOWTDI can actually be a foe of simple, elegant solutions.
</p>

<p>
    Why do I claim this?
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Recently, discussing module structure in Zend Framework 2, some folks were
    arguing that our recommended directory structure invokes the <a
        href="http://en.wikipedia.org/wiki/YAGNI">YAGNI</a> principle: You Ain't
    Gonna Need It.  Our recommendation is this:
</p>

<div class="example"><pre>
ModuleName/
    autoload_classmap.php
    Module.php
    config/
        module.config.php
        (other config files)
    public/
        css/
        images/
        js/
    src/
        ModuleName/
            (source files)
    test/
    view/
</pre></div>

<p>
    The argument is that since most modules implement a single namespace, and
    because we recommend following <a
        href="https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md">PSR-0</a>
    for autoloaders, it makes sense to simply have the source files directly
    under the module directory.
</p>

<div class="example"><pre>
ModuleName/
    autoload_classmap.php
    Module.php
    (other source files)
    config/
        module.config.php
        (other config files)
    public/
    test/
    view/
</pre></div>

<p>
    The argument myself and others made was that it makes sense to group the
    files by responsibility. However, the module system ultimately <em>doesn't
        care</em> how you organize the module -- we've embraced TMTOWTDI, and
    our only requirement is that for your module to be consumed, you must define
    a <code>ModuleName\Module</code> class, and notify the module manager how to
    find it. Anything goes.
</p>

<p>
    How does that bolster my argument about the importance of conventions? It
    doesn't. What does is what following the recommended structure enabled me to
    do.
</p>

<p>
    One common concern identified with having a re-usable module system is that
    you should be able to expose public assets easily: things like
    module-specific CSS or JavaScript, or even images. The first question that
    arises when you consider this is: where do I put them in my module? That's
    why the recommendation includes a <code>public</code> directory. In fact,
    the recommendation goes a step further, and suggests <code>css</code>,
    <code>images</code>, and <code>js</code> directories as well.
</p>

<p>
    Now, your modules are typically <em>outside</em> the document root. This is
    a rudimentary and fundamental security measure, and also simplifies
    deployment to a degree -- you don't need to worry about telling the web
    server about what it <em>shouldn't</em> serve. But if the modules are
    outside the document root, how can I expose their public assets?
</p>

<p>
    There are a two possibilities that immediately jump to mind:
</p>

<ul>
    <li>Install scripts for modules, which copy the files into the document root.</li>
    <li>Symlink the files into the document root.</li>
</ul>

<p>
    Both are valid, and easy to accomplish. Both raise the same question: where,
    exactly? What if multiple modules have public assets named the same? how do
    I refer to my assets withing things like view scripts?
</p>

<p>
    This is where having a convention starts to make sense: having a convention
    should answer these questions unambiguously.
</p>

<p>
    My answer: public access should be at
    <code>/css/ModuleName/stylesheetname</code>, or
    <code>/js/ModuleName/scriptname</code> or
    <code>/images/Modulename/imagename</code>. It's a dirt-simple rule that
    fulfills the use cases.
</p>

<p>
    However, I'm now stuck with having to develop either install scripts or
    remembering to create symlinks -- ugh. And that's where having conventions
    led me to a simple, elegant solution.
</p>

<p>
    I added one line to my Apache vhost definition:
</p>

<div class="example"><pre>
AliasMatch /(css|js|images)/([^/]+)/(.*) /path/to/module/$2/public/$1/$3
</pre></div>

<p>
    The translation:
</p>

<blockquote>
    When I come across a path to CSS, JS, or image files that are in a
    subdirectory, alias it to the corresponding public asset of the matched
    module directory.
</blockquote>

<p>
    I dropped this into my vhost, restarted Apache, and now not only were the
    assets I'd created already served, but any new ones I create are immediately
    available as well. Having a convention actually simplified my choices and my
    solutions.
</p>

<p>
    Rapid application development at its finest.
</p>

<p>
    My point is this: there will always be more than one way to do things when
    you're programming, and you may not always agree with the decisions your
    team has made, or the component library or framework you're using has made.
    However, if you poke around a little and play within those confines, you may
    find that those decisions make other decisions easier, or disappear
    altogether.
</p>
EOT;
$entry->setExtended($extended);

return $entry;