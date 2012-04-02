<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('120-Serendipity-upgrade');
$entry->setTitle('Serendipity upgrade');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1150601152);
$entry->setUpdated(1150601152);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
  'ep_no_nl2br' => 'true',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    I upgraded <a href="http://www.s9y.org/">Serendipity</a> today, due to the
    recent announcement of the 1.0 release, as well as to combat some rampant
    issues with trackback spam.
</p>
<p>
    I've been very happy with Serendipity so far; it just runs, and the default
    install gives just what you need to get a blog up and running, and nothing
    more; any extra functionality comes via plugins which you, the blogger, get
    to decide upon.
</p>
<p>
    Additionally, it's incredibly easy to upgrade. Unpack the tarball, rsync
    it over your existing install (I rsync it, because I don't use 'serendipity'
    as my directory name), visit the admin, boom, you're done. I've upgraded
    several times, and never lost data, nor configuration settings. 
</p>
<p>
    My primary reason for the upgrade was, as noted earlier, to combat trackback
    spam. As of this morning, I had 15,000 pending trackbacks, none of which
    appeared to be valid (if any of them were, and you're not seeing yours, I'm
    very sorry; I deleted them <em>en masse</em>). These had accumulated in <em>less than
    a month</em> -- that's an average of about one every 3 minutes.
</p>
<p>
    Since upgrading, and using the <a href="http://akismet.com/">Akismet</a>
    service, I've received not a single spam trackback. Needless to say, I'm
    happy I performed the upgrade!
</p>
<p>
    If you're a Serendipity user, and haven't upgraded to 1.0.0 yet (or one of
    it's reportedly very stable release candidates), do it today -- you have
    nothing to lose, and a lot of lost time to gain!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;