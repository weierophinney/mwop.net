<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-11-05-zend-server-caching');
$entry->setTitle('Zend Server, ZF2, and Page Caching');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-11-05 15:25', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-11-05 15:25', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'zf2',
));

$body =<<<'EOT'
<p>
    Zend Server has a very cool
    <a href="http://www.youtube.com/watch_v=i2XXn2SA5zM.html" target="_blank">Page Caching feature</a>. Basically, you can provide
    URLs or URL regular expressions, and tell Zend Server to provide full-page
    caching of those pages. This can provide a tremendous performance boost, without
    needing to change anything in your application structure; simply enable it for a
    set of pages, and sit back and relax.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p style="text-align: center;">
    <img 
        src="/images/blog/2012-11-04-Server-CachingRule.png"
        alt="Zend Server Page Caching"
        title="Zend Server Page Caching" />
</p>

<p>
    However, this feature is not entirely straight-forward when using a framework
    that provides its own routing, such as ZF2. The reason is because it assumes by
    default that each match maps to a specific file on the filesystem, and prepares
    the caching based on the actual <em>file</em> it hits. What this means for ZF2 and other
    similar frameworks is that any page that matches will return the cached version
    for the <em>first</em> match that also matches the same <em>file</em> -- i.e., <code>index.php</code> in
    ZF2. That's every page the framework handles. As an example, if I match on <code>/article/\d+</code>, it matches
    this to the file <code>index.php</code>, and then any other match that resolves to
    <code>index.php</code> gets served that same page. Not handy.
</p>

<p>
    The good part is that there's a way around this.
</p>

<p>
    When creating or modifying a caching rule, simply look for the text, "Create a
    separate cached page for each value of:" and click the "Add Parameter" button.
    Select <code>_SERVER</code> from the dropdown, and type <code>[REQUEST_URI]</code> for the value. Once
    saved, each page that matches the pattern will be cached separately.
</p>

<p>
    <img 
        src="/images/blog/2012-11-04-Server-Caching-Request.png"
        alt="Zend Server Page Caching by Request"
        title="Zend Server Page Caching by Request" />
</p>

<p>
    Note: the <code>_SERVER</code> key may vary based on what environment/OS you're deployed
    in. Additionally, it may differ based on how you define rewrite rules -- some
    frameworks and CMS systems will append to the query string, for instance, in
    which case you may want to select the "entire query string" parameter instead of
    <code>_SERVER</code>; the point is, there's likely a way for you to configure it.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
