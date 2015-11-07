<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-11-01-openshift-zf2-composer');
$entry->setTitle('OpenShift, ZF2, and Composer');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-11-01 15:25', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-11-01 15:25', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'zf2',
  'cloud',
  'composer',
));

$body =<<<'EOT'
<p>
I was recently shopping around for inexpensive cloud hosting; I want to try out
a couple of ideas that may or may not have much traffic, but which aren't suited
for my VPS setup (the excellent <a href="http://servergrove.com/">ServerGrove</a>); additionally, I'm unsure how long
I will maintain these projects. My budget for this is quite small as a result;
I'm already paying for hosting, and am quite happy with it, so this is really
for experimental stuff.
</p>

<p>
I considered Amazon, Orchestra.io, and a few others, but was concerned about the
idea of a ~$50/month cost for something I'm uncertain about. 
</p>

<p>
When I asked in <a href="irc://irc.freenode.net/zftalk.dev">#zftalk.dev</a>, someone
suggested <a href="http://openshift.redhat.com/">OpenShift</a> as an idea, and
coincidentally, the very next day
<a href="http://www.zend.com/en/company/news/press/379_red-hat-expands-openshift-ecosystem-with-zend-partnership-to-offer-professional-grade-environment-for-php-developers">Zend announced a partnership with RedHat surrounding OpenShift</a>.  The stars were in alignment.
</p>

<p>
In the past month, in the few spare moments I've had (which included an
excellent OpenShift hackathon at ZendCon), I've created a quick application that
I've deployed and tested in OpenShift. These are my findings.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>ZF2</h2>

<p>
    I didn't really have to do anything different to have <a 
    href="http://framework.zend.com/">zf2</a> work; the standard 
    <code>.htaccess</code> provided in the skeleton application worked 
    flawlessly the first time (I've worked with some cloud environments where 
    this is not the case).
</p>

<p>
The only frustration I had was the default directory structure OpenShift foists
upon us: 
</p>

<div class="example"><pre>
data/
libs/
misc/
php/
</pre></div>

<p>
This is not terrible, by any stretch. However, it's attempting to dictate the
application structure, which I'm not terribly happy with -- particularly as my
structure may vary based on the framework I'm using (or not!), and because I may
already have a project written that I simply want to deploy.
</p>

<p>
In particular, the <code>php</code> directory is galling -- it's simply the document root.
Most frameworks I've used or seen call the equivalent directory <code>public</code>, or
<code>web</code>, or <code>html</code> -- but never <code>php</code> (in large part because the only PHP file
under the document root in most frameworks is the <code>index.php</code> that acts as the
front controller). It would be nice if this were configurable.
</p>

<p>
This conflicts a bit with how a ZF2 app is structured. I ended up doing the
following:
</p>

<ul>
<li>
Removed <code>php</code> and symlinked my <code>public</code> directory to it.
</li>
<li>
Removed <code>libs</code> and symlinked my <code>vendor</code> directory to it.
</li>
<li>
Removed <code>misc</code> as I had no need to it.
</li>
</ul>

<p>
Nothing too big, thankfully -- but problematic from the perspective of, "I've
already developed this app, but now I have to make changes for it to work on a
specific cloud vendor."
</p>

<h2>Composer</h2>

<p>
    My next question was how to use <a 
    href="http://getcomposer.org/">Composer</a> during my deployment process, 
    and some some googling <a href="https://openshift.redhat.com/community/content/support-for-git-clone-on-the-server-aka-support-php-composerphar">found 
    some answers for me</a>.
</p>

<p>
Basically, I needed to create a <code>deploy</code> task that does two things:
</p>

<ul>
<li>
    Unset the <code>GIT_DIR</code> environment variable. Evidently, the build 
    process operates as part of a git hook, and since Composer often uses git 
    repositories, this can lead to problems.
</li>
<li>
    Change directory to <code>OPENSHIFT_REPO_DIR</code>, which is where the 
    application root (not document root!) lives.
</li>
</ul>

<p>
    Once I did those, I could run my normal composer installation. The 
    <code>deploy</code> task looks like this:
</p>

<div class="example"><pre><code class="language-bash">
#!/bin/bash
# .openshift/action_hooks/deploy
( unset GIT_DIR ; cd $OPENSHIFT_REPO_DIR ; /usr/local/zend/bin/php composer.phar install )
</code></pre></div>

<p>
This leads into my next topic.
</p>

<h2>Deployment</h2>

<p>
    First off, as you probably guessed from that last secton, there 
    <strong>are</strong> hooks for
    deployment -- it doesn't have to be simply git. I like this, as I may have
    additional things I want to do during deployment, such as retrieving and
    installing site-specific configuration files, installing Composer-defined
    dependencies (as already noted), etc.
</p>

<p>
    Over all, this is pretty seamless, but it's not without issues. I've been 
    told that some of my issues are being worked on, so those I won't bring up 
    here. The ones that were a bit strange, and which caught me by surprise, 
    though, were:
</p>

<ul>
<li>
    Though the build process creates the site build from git, your 
    <strong>submodules are not updated recursively</strong>. This tripped me 
    up, as I was using <a href="https://github.com/EvanDotPro/EdpMarkdown">EdpMarkdown</a>,
    and had installed it as a submodule. I ended up having to import it, and its
    own submodule, directly into my project so that it would work.
</li>
<li>
    I installed the <a href="http://www.mongodb.org/">MongoDB</a> cartridge. Ironically, it was not then enabled in
    Zend Server, and I had to go do this. This should be turnkey.
</li>
<li>
    <code>/usr/bin/php</code> is not the same as <code>/usr/local/zend/bin/php</code>. This makes no
    sense to me if I've installed Zend Server as my base gear. Considering
    they're different versions, this can be hugely misleading and lead to errors.
    I understand there are reasons to have both -- so simply be aware that if you
    use the Zend Server gear, your tasks likely should use
    <code>/usr/local/zend/bin/php</code>.
</li>
</ul>

<h2>The good parts?</h2>

<ul>
<li>
    <a href="https://openshift.redhat.com/community/faq/i-have-deployed-my-app-but-i-don%E2%80%99t-like-telling-people-to-visit-myapp-myusernamerhcloudcom-how-c">You 
    can alias an application to a DNS CNAME</a> -- meaning you can point your
    domain name to your OpenShift applications. Awesome!
</li>
<li>
    Simplicity of adding capabilities, such as Mongo, MySQL, Cron, and others. 
    In most cases, this is simply a "click on the button" and it's installed 
    and available.
</li>
<li>
    <a href="http://www.zend.com/en/products/server">Zend Server</a>. For 
    most PHP extensions, I can turn them on or off with a few
    mouse clicks. If I want page-level caching, I don't have to do anything to my
    application; I can simply setup some rules in the Zend Server interface and
    get on with it, and enjoy tremendous boosts to performance. I used to enjoy
    taming and tuning servers; most days anymore, I just want them to work.
</li>
<li>
    <a href="https://openshift.redhat.com/community/developers/remote-access">SSH</a> 
    access to the server, with a number of commands to which I've been given
    <code>sudoer</code> access. If you're going to sandbox somebody,
    this is a fantastic way to do it. Oh, also: SSH tunnels to services like Mongo
    and MySQL just work (via the <code>rhc-port-forward</code> command).
</li>
</ul>

<h2>Summary</h2>

<p>
Over all, I'm quite pleased. While it took me a bit to find the various
incantations I needed, the service is quite flexible. For my needs, considering
I'm doing experimental stuff, the price can't be beat (the current developer
preview is free). Considering most stuff I do will fall into this or the basic
tier, and that most cartridges do not end up counting against your alotment of
gears, the pricing ($0.05/hour) is extremely competitive. 
</p>

EOT;
$entry->setExtended($extended);

return $entry;
