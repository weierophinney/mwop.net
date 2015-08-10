<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('252-Making-Zend-Server-Available-Via-SSL-Only');
$entry->setTitle('Making Zend Server Available Via SSL Only');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1294347118);
$entry->setUpdated(1294437223);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
In light of the 
<a href="http://bugs.php.net/bug.php?id=53632">recent remote PHP exploit</a>, I decided to
update a couple servers I manage to ensure they weren't vulnerable. In each
case, I had been using hand-compiled PHP builds, but decided that I'm simply too
busy lately to be trying to maintain updates -- so I decided to install
<a href="http://www.zend.com/en/products/server/">Zend Server</a>. I've been using Zend
Server CE on my laptop since before even any initial private betas, and have
been pretty happy with it -- I only compile now when I need to test specific PHP
versions.
</p>

<p>
One thing I've never been happy about, however, is that by default Zend Server
exposes its administration GUI via both HTTP and HTTPS. Considering that the
password gives you access to a lot of sensitive configuration, I want it to be
encrypted.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
The Zend Server GUI runs on a <a href="http://www.lighttpd.net/">lighttpd</a> instance,
which means you can configure access to the GUI via lighttpd; in fact, the
<a href="http://files.zend.com/help/Zend-Server-Community-Edition/securing_the_administration_interface.htm">
documentation even details some approaches to securing it</a>. The recommendations,
however, are to restrict by IP address -- which is great if you have a fixed IP,
are the only one accessing the admin, or never access from, say, your phone, but
not terribly useful if any of those are not true.
</p>

<p>
With a little help from <a href="http://prematureoptimization.org/">Shahar</a>, I figured
out what to do, however. I added this clause to my
<code>lighttpd.conf</code><a href="#f1"><sup>[1]</sup></a> file:
</p>

<div class="example"><pre><code class="language-perl">
# Disable access via http (i.e., make admin https-only)
$SERVER[\&quot;socket\&quot;] == \&quot;:10081\&quot; {
  $HTTP[\&quot;remoteip\&quot;] !~ \&quot;127.0.0.1\&quot; {
      $HTTP[\&quot;url\&quot;] =~ \&quot;^/ZendServer/\&quot; {
          url.access-deny = ( \&quot;\&quot; )
      }
  }
}
</code></pre></div>

<p>
The above basically reads as follows:
</p>

<ul>
<li>
If the request comes in on port 10081 (the default HTTP port for the Zend
   Server admin):
</li>
<ul>
<li>
and the remote address is not localhost (IP <code>127.0.0.1</code>):
</li>
<ul>
<li>
Deny access to any URL starting with "/ZendServer/"
</li>
</ul>
</ul>
</ul>

<p>
Once you add the stanza, restart lighttpd<a href="#f2"><sup>[2]</sup></a> for the changes to take effect.
When accessing the site via <code>http://servername:10081/ZendServer</code>, you should now
receive a "403 - Forbidden" page, while access via
<code>https://servername:10082/ZendServer</code> remains open.
</p>

<ul>
    <li><a name="f1"><sup>[1]</sup></a>In linux versions of Zend Server,
        <code>/usr/local/zend/gui/lighttpd/etc/lighttpd.conf</code>
    </li>
    <li><a name="f2"><sup>[2]</sup></a> In linux versions of Zend Server,
        <code>/usr/local/zend/bin/zendctl.sh restart-lighttpd</code>
    </li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
