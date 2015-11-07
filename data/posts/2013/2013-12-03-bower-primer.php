<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-12-03-bower-primer');
$entry->setTitle('A Bower Primer');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2013-12-03 09:50', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-12-03 09:50', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'javascript',
  'bower',
));

$body =<<<'EOT'
<p>
    Recently, I've been doing a fair bit of frontend development with my team 
    as we've worked on the <a href="http://apigility.org/">Apigility</a> admin.
    This has meant working with a variety of both JavaScript and CSS
    libraries, often trying something out only to toss it out again later.
    Working with frontend libraries has been quite a hassle, due to a combination
    of discovery, installation issues, and build issues (minimization, primarily).
    I figured there must a better way.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Background</h2>

<p>
    Until recently, discovery of JS and CSS libraries has gone something like this:
</p>

<ol>
    <li>Search for functionality via Google</li>
    <li>Generally find a solution on StackOverflow</li>
    <li>Discover said solution relies on a third-party library</li>
    <li>Google for said library</li>
    <li>Generally find said library on GitHub</li>
    <li>Clone the library locally</li>
    <li>Either build the final assets, or try and locate them in the repo</li>
    <li>Minimize the assets</li>
    <li>Copy the assets into the project</li>
</ol>

<p>
    Frontend development sucks.
</p>

<p>
    Then I started noticing these files called <code>.bowerrc</code> and 
    <code>bower.json</code> in many of the aforementioned libraries, and also
    that <a href="http://ralphschindler.com/">Ralph</a> had put some inside our
    Apigility skeleton. I got curious as to what this "bower" might be.
</p>

<h2>Bower: Package management for the web</h2>

<p>
    Essentially, <a href="http://bower.io/">Bower</a> is, to use the project's
    words, "a package manager for the web." Written in JavaScript, and running
    on <a href="http://nodejs.org/">node.js</a>, it is to frontend assets what
    <a href="https://npmjs.org/">npm</a> is to node, or <a href="https://getcomposer.org">Composer</a> 
    is to PHP. It allows you to define what assets you need in your application,
    including the versions, and then install them. If any of those assets have
    other dependencies, those, too, will be installed.
</p>

<p>
    Later, you can update the dependencies, add or remove dependencies, and more.
</p>

<p>
    On top of that, bower allows you to <em>search</em> for packages, which
    essentially allows you to eliminate most of the steps 4 and on in my list
    above.
</p>

<h2>A Bower Primer</h2>

<p>So, how do you use bower?</p>

<p>
    In my experience, which is not extensive by any stretch, the usage is like this:
</p>

<ol>
    <li>Search for functionality via Google</li>
    <li>Generally find a solution on StackOverflow</li>
    <li>Discover said solution relies on a third-party library</li>
    <li>Use bower to search for said library</li>
    <li>Add the discovered library to your <code>bower.json</code> file</li>
    <li>Run <code>bower install</code> or <code>bower update</code></li>
</ol>

<p>
    I've found that most projects registered with bower have minimized builds
    available (as well as the full source build), which is a huge boon in
    terms of performance. It also eliminates the "minimize the assets" step from
    my original list.
</p>

<p>
    To use bower, you'll need two files. The first is <code>.bowerrc</code> 
    which goes in your project root; you'll run <code>bower</code> from this 
    same directory.  This file tells bower how to run, and where to install 
    things, and, despite being an RC file, is written in JSON. Mine usually 
    looks like this:
</p>

<div class="example"><pre><code class="language-javascript">
{
    "directory": "public/assets/vendor"
}
</code></pre></div>

<p>
    The above tells bower to install dependencies in the <code>public/assets/vendor</code>
    subdirectory.
</p>

<p>
    The second file you need is <code>bower.json</code>. This file tells bower
    what asset packages you want to install, and the preferred version. (The file
    can also be used to define a package, just like with Composer or npm.) As an
    example, the following is a definition I used for an Apigility example:
</p>

<div class="example"><pre><code class="language-javascript">
{
    "name": "ag-contacts-demo",
    "version": "0.0.1",
    "ignore": [
        "**/.*"
    ],
    "dependencies": {
        "angular": "~1.2",
        "angular-resource": "~1.2",
        "angular-route": "~1.2",
        "bootstrap": ">=3.0.0",
        "font-awesome": "~3.2.1"
    }
}
</code></pre></div>

<p>
    Bower requires that packages use <a href="http://semver.org/">Semantic
    Versioning</a>. You can specify exact versions, minor versions, or major
    versions, combine them with comparison operators (<code>&lt;</code>, 
    <code>&gt;</code>, <code>=</code>, etc.), or use the "next significant release"
    operator ("~") to indicate a given version up to the next more general
    release (e.g., "~1.2" is equivalent to "&gt;=1.2,&lt;2.0").
</p>

<p>
    Once you have these defined, you should also add an entry to your 
    <code>.gitignore</code> file to exclude the directory you list in your
    <code>.bowerrc</code>; these files can be installed at build time,
    and thus help you keep your project repository lean. Per the above
    example:
</p>

<div class="example"><pre><code class="language-text">
public/assets/vendor/
</code></pre></div>

<p>
    At this point, run <code>bower install</code>, and bower will resolve
    all dependencies and install them where you want.
</p>

<p>
    At any point, you can list what packages bower has installed, as well
    as the versions it has installed. The <code>bower help</code> command
    is your friend should those needs arise.
</p>

<h2>Closing Thoughts</h2>

<p>
    I'm quite happy with the various tools emerging to make modern
    web development easier by allowing developers to more easily
    share their work, as well as ensure that all dependencies are
    easily installable. Bower is another tool in my arsenal as a web
    developer, giving me a consistent set of dependency management tools from my 
    server-side development all the way to my client-side application.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
