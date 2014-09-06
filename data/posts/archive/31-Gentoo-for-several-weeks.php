<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('31-Gentoo-for-several-weeks');
$entry->setTitle('Gentoo for several weeks');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1082686210);
$entry->setUpdated(1095702608);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I've had a bunch of problems with my new computer -- it uses ACPI, but if I
    load the ACPI modules, it won't boot; if I don't load them, I have to go
    through contortions to get the ethernet working, and it won't power down;
    and a bunch of other little stuff.
</p>
<p>
    So, a few weeks ago, I thought, what the heck? Why not try <a href="http://www.gentoo.org">Gentoo</a>? I've been reading about it
    since it first came out, and I remember talking with Duane about it once,
    and it has a reputation for both being cutting edge and stable. Heck, even
    Wil Wheaton's endorsing it... it can't be <b>all</b> bad, right?
</p>
<p>
    I had a few misstarts -- bad CDs, not quite getting how the chroot thing
    worked, problems with DNS (which I <em>still</em> don't understand; and Rob
    has them as well, so it's not just me). But once I got it installed... well,
    I'm impressed.
</p>
<p>
    The thing about Gentoo is, it <em>compiles</em> everything from source. It's
    like <a href="http://www.debian.org">Debian</a>, in that it fetches all
    dependencies and installs those... but it's all source. So it's not exactly
    fast. But because everything is compiled, and because you setup C flags
    specific to your machine, what you get is incredibly optimized for your own
    machine. This 1.6GHz machine simply flies. And the memory usage <em>just
        stays low</em>.
</p>
<p>
    I'd like to use it for my server... but I can't really take the server down
    at this point when it's making both my mom and myself money. But what a
    great system... I only wish I'd used it for the mail server at work.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;