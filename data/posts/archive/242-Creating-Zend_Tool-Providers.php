<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('242-Creating-Zend_Tool-Providers');
$entry->setTitle('Creating Zend_Tool Providers');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1277989500);
$entry->setUpdated(1278534679);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    When I was at <a href="http://www.symfony-live.com/">Symfony Live</a> this
    past February, I assisted <a
        href="http://www.leftontheweb.com/">Stefan Koopmanschap</a>
    in a full-day workshop on integrating Zend Framework in Symfony
    applications. During that workshop, Stefan demonstrated creating Symfony
    "tasks". These are classes that tie in to the Symfony command-line
    tooling -- basically allowing you to tie in to the CLI tool in order to
    create cronjobs, migration scripts, etc.
</p>

<p>
    Of course, Zend Framework has an analogue to Symfony tasks in the <a
        href="http://framework.zend.com/manual/en/zend.tool.html">Zend_Tool</a>
    component's "providers". In this post, I'll demonstrate how you can create a
    simple provider that will return the most recent entry from an RSS or Atom
    feed. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>First things first</h2>

<p><em>
    Caveat: this entire post assumes you are using a unix-like operating system,
    such as a Linux distribution or Mac OSX. Most of the instructions should
    work in Windows, but I have not tested on that platform.
</em></p>

<p>
    First, a little setup. <code>Zend_Tool</code> needs some configuration.
    To get started, you need to run the following command (if you haven't
    already):
</p>

<div class="example"><pre><code class="language-bash">
% zf create config
</code></pre></div>

<p>
    This will create a configuration in <code>$HOME/.zf.ini</code>. If you pop
    that file open, you should see an entry for <code>php.include_path</code>.
    This is the <code>include_path</code> <code>Zend_Tool</code> will use, and
    should include your ZF installation; any providers you create should be on
    this path -- or you should modify it to add a path to your providers.
</p>

<h2>Create the provider</h2>

<p>
    Providers are incredibly simple. The easiest way to create one is to create
    a class extending <code>Zend_Tool_Framework_Provider_Abstract</code>, and
    then to simply start creating methods.
</p>

<p>
    A few rules are good to know, however:
</p>

<ul>
    <li>If you need to throw an exception, throw a
        <code>Zend_Tool_Project_Exception</code>. This integrates with the CLI
        tooling to provide nice, colorful error messages.</li>

    <li>While you <em>can</em> <code>echo</code> directly from your methods, the
        suggested practice is to use the response object and append content to
        it. This will ensure that if we later write an XML-RPC, SOAP, or web
        frontend to <code>Zend_Tool</code>, you will not need to make any
        changes to your code. This is as easy as:

        <div class="example"><pre><code class="language-php">
$response = $this-&gt;_registry-&gt;getResponse();
$response-&gt;appendContent($content);
</code></pre></div>
    </li>
</ul>

<p>
    In my provider, I'm wanting to grab the first entry of a given feed. Instead
    of needing to remember the feed URL, I'd like to use a mnemonic; this will
    be my sole argument to the provider. I'll have it default to my own feed.
    The code ends up looking like this:
</p>

<div class="example"><pre><code class="language-php">
class Phly_Tool_Feed extends Zend_Tool_Framework_Provider_Abstract
{
    protected $_feeds = array(
        'weierophinney' =&gt; 'http://weierophinney.net/matthew/feeds/index.rss1',
        'planetphp'     =&gt; 'http://www.planet-php.net/rdf/',
    );

    /**
     * Read the first item of a feed
     * 
     * @param  string $feed Named identifier for a feed
     * @return bool
     */
    public function read($feed = 'weierophinney')
    {
        if (!array_key_exists($feed, $this-&gt;_feeds)) {
            throw new Zend_Tool_Project_Exception(sprintf(
                'Unknown feed \&quot;%s\&quot;', 
                $feed
            ));
        }

        $feed = Zend_Feed_Reader::import($this-&gt;_feeds[$feed]);
        $title = $desc = $link = '';
        foreach ($feed as $entry) {
            $title = $entry-&gt;getTitle();
            $desc  = $entry-&gt;getDescription();
            $link  = $entry-&gt;getLink();
            break;
        }
        $content = sprintf(\&quot;%s\n%s\n\n%s\n\&quot;, $title, strip_tags($desc), $link);

        $response = $this-&gt;_registry-&gt;getResponse();
        $response-&gt;appendContent($content);
        return true;
    }
}
</code></pre></div>

<p>
    I'm leveraging <code>Zend_Feed_Reader</code> here, and simply creating some
    formatted text output.
</p>

<p>
    Now that the provider is created, I need to put it in the file
    <code>Phly/Tool/Feed.php</code>, relative to a directory in the 
    <code>include_path</code> configured by <code>Zend_Tool</code>.
</p>

<h2>Tying the provider to the tool</h2>

<p>
    Now that we've got the provider written and somewhere <code>Zend_Tool</code>
    can potentially find it, we need to tell <code>Zend_Tool</code> about it.
    Open up the <code>$HOME/.zf.ini</code> file again, and add the following
    line:
</p>

<div class="example"><pre><code class="language-ini">
basicloader.classes.1 = \&quot;Phly_Tool_Feed\&quot;
</code></pre></div>

<p>
    This tells <code>Zend_Tool</code> that there's an additional provider it
    should be aware of. Note in particular the ".1" portion of the key;
    "basicloader.classes" is an array. One gotcha I discovered is that, unlike
    <code>Zend_Config</code>, you cannot use the "[]" notation. In other words,
    the following <em><strong>does not work</strong></em>:
</p>

<div class="example"><pre><code class="language-ini">
basicloader.classes[] = \&quot;Phly_Tool_Feed\&quot;
</code></pre></div>

<p>
    You need to specify keys manually, and they need to be unique.
</p>

<h2>Getting help</h2>

<p>
    Now, time to test out if it all works. If you've done the above steps, you
    can now execute the following:
</p>

<div class="example"><pre><code class="language-bash">
% zf \? feed
</code></pre></div>

<p>
    <em>Note: I use zsh, and need to escape the question mark; you may not need
    to in other shells.</em>
</p>

<p>
    If all is well, you'll get the following:
</p>

<pre>
Actions supported by provider "Feed"
  Feed
    zf read feed feed[=weierophinney]
</pre>

<p>
    If you're not seeing this, check to make sure that your provider is on an
    <code>include_path</code> as defined in your <code>.zf.ini</code> file; if
    you still have issues, ask on the <a
        href="http://zend-framework-community.634137.n4.nabble.com/Zend-Framework-f634138.html">fw-general</a>
    mailing list or in the #zftalk IRC channel on <a
        href="http://freenode.net/">Freenode</a>.
</p>

<h2>Using the provider</h2>

<p>
    Once your provider is working, fire it up:
</p>

<div class="example"><pre><code class="language-bash">
% zf read feed
</code></pre></div>

<p>
    or
</p>

<div class="example"><pre><code class="language-bash">
% zf read feed planetphp
</code></pre></div>

<p>
    You should get something that looks like this (the actual entry will vary):
</p>

<pre>
State of Zend Framework 2.0

    
    The past few months have kept myself and my team quite busy, as we've turned
    our attentions from maintenance of the Zend Framework 1.X series to Zend
    Framework 2.0. I've been fielding questions regularly about ZF2 lately, and
    felt it was time to talk about the roadmap for ZF2, what we've done so far,
    and how the community can help.

 Continue reading "State of Zend Framework 2.0"
    

http://weierophinney.net/matthew/archives/241-State-of-Zend-Framework-2.0.html
</pre>

<h2>Closing notes</h2>

<p>
    One "gotcha" you may experience is that there is currently no support for
    specifying project-specific providers within applications created with
    <code>Zend_Tool</code> -- a feature that would be quite useful for creating
    project-specific tasks.<sup>*</sup>
</p>

<p>
    That said, <code>Zend_Tool</code> providers are an incredibly useful and
    easy way to write CLI tools based on Zend Framework. Hopefully this post
    will help demystify the component and its usage, and get you thinking about
    what tasks <em>you</em> would like to write.
</p>

<p>
    <sup>*</sup> You <em>can</em> fake it by creating an alternate configuration
    file in your project, informing the environment of it, and calling the
    <code>zf</code> commandline tool -- something that can be done in a single
    line:
</p>

<div class="example"><pre><code class="language-bash">
% ZF_CONFIG_FILE=./zf.ini; zf &lt;action&gt; &lt;provider&gt; ...
</code></pre></div>
EOT;
$entry->setExtended($extended);

return $entry;
