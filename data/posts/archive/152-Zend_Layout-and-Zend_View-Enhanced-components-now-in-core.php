<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('152-Zend_Layout-and-Zend_View-Enhanced-components-now-in-core');
$entry->setTitle('Zend_Layout and Zend_View Enhanced components now in core');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1198071851);
$entry->setUpdated(1198085073);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framewor',
));

$body =<<<'EOT'
<p>
    I'm pleased to announce that the 
    <a href="http://framework.zend.com/wiki/pages/viewpage.action?pageId=33071">Zend_View Enhanced</a> 
    and <a href="http://framework.zend.com/wiki/display/ZFPROP/Zend_Layout">Zend_Layout</a>
    components are now in the <a href="http://framework.zend.com/">Zend Framework</a> 
    core. With these two components, you can now create some truly 
    <a href="http://blog.astrumfutura.com/archives/291-Complex-Views-with-the-Zend-Framework-Part-6-Setting-The-Terminology.html">complex views</a>
    for your application with relative ease.
</p>

<p>
    The two components tackle several view related tasks:
</p>

<ul>
    <li>Layouts, or Two Step Views</li>
    <li>Partials (view fragment scripts with their own variable scope)</li>
    <li>Placeholders (store data and/or markup for later retrieval)</li>
    <li>Actions (dispatch a controller action)</li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    So, what's the big deal? Much, if not all of this, was already possible, I
    hear some people saying. Well, yes, technically it was; in fact, all of
    these, except layouts, were accomplished by the addition of extra view
    helpers, which anybody could have written (and, in fact, some did). However,
    by having these as a standard part of the library, there are now standard
    ways to perform these tasks -- meaning consistency between applications.
</p>

<p>
    Plus, these helpers just make things so much simpler!
</p>

<p>
    For instance, who out there has all the DOCTYPE declarations memorized? I
    personally know all the types, but can't rattle off the entire declarations
    associated with each to save my life. With the <kbd>doctype()</kbd> helper,
    all I have to do is:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;doctype('XHTML1_TRANSITIONAL') ?&gt;
</code></pre></div>

<p>
    and it's now present. Furthermore, by putting this at the top of my layout,
    when I display my scripts as aggregated in the <kbd>headScript()</kbd>
    helper, they'll now be properly escaped as XML CDATA, as helpers that need
    to be DOCTYPE aware now determine this information from that helper.
</p>

<p>
    Speaking of the <kbd>headScript()</kbd> helper, it's pretty handy. Let's say
    you have an application that requires javascript. Instead of unconditionally
    specifying the javascript include for every controller, or setting up
    complex logic for determining when to include it, you can have your
    application view specify it's needed:
</p>

<div class="example"><pre><code lang="php">
&lt;?php $this-&gt;headScript()-&gt;appendFile('/js/foo.js') ?&gt;
</code></pre></div>

<p>
    Then, in your master layout script, you tell it to include any scripts
    aggregated:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;headScript() ?&gt;
</code></pre></div>

<p>
    You can do similarly for specifying feeds (via <kbd>headLink()</kbd>),
    stylesheets (via <kbd>headLink()</kbd> for external files,
    <kbd>headStyle()</kbd> for inline stylesheets), and even your title element
    (for instance, you could aggregate your various breadcrumbs, and then
    specify a custom separator to use between them).
</p>

<p>
    This is really just the tip of the iceberg. Using a combination of
    placeholders, partials, actions, and normal view helpers, you can then
    create some pretty complex layouts using minimal markup. As an example:
</p>

<div class="example"><pre><code lang="php">
&lt;?= $this-&gt;doctype('XHTML1_TRANSITIONAL') ?&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;?= $this-&gt;headTitle() ?&gt;
        &lt;?= $this-&gt;headMeta()-&gt;setIndent(8) ?&gt;
        &lt;?= $this-&gt;headLink()-&gt;setIndent(8) ?&gt;
        &lt;?= $this-&gt;headStyle()-&gt;setIndent(8) ?&gt;
        &lt;?= $this-&gt;headScript()-&gt;setIndent(8) ?&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;?= $this-&gt;partial('header.phtml') ?&gt;
        &lt;div id=\&quot;content\&quot;&gt;
            &lt;?= $this-&gt;layout()-&gt;content ?&gt;
        &lt;/div&gt;
        &lt;?= $this-&gt;subnav() ?&gt;
        &lt;?= $this-&gt;partial('footer.phtml') ?&gt;
        &lt;?= $this-&gt;inlineScript() ?&gt;
    &lt;/body&gt;
&lt;/html&gt;
</code></pre></div>

<p>
    The example above makes use of several placeholders (<kbd>doctype</kbd>,
    <kbd>HeadTitle</kbd>, <kbd>HeadMeta</kbd>, <kbd>HeadLink</kbd>,
    <kbd>HeadStyle</kbd>, <kbd>HeadScript</kbd>, and <kbd>InlineScript</kbd>),
    two partials (for the header and footer), layout content, and a custom view
    helper (for navigation); the entire thing is less than 20 lines long, yet
    contains everything necessary for your site layout.
</p>

<p>
    The functionality of these new components is not only broad, but deep as
    well, and can't be covered in a single blog post. Look for a series of
    tutorials on the <a href="http://devzone.zend.com/">Zend Developer Zone</a>
    detailing them in the coming weeks. In the meantime, you can read the
    documentation available in the ZF subversion repository.
</p>
EOT;
$entry->setExtended($extended);

return $entry;