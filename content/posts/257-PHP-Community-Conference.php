<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('257-PHP-Community-Conference');
$entry->setTitle('PHP Community Conference');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1299690369);
$entry->setUpdated(1299707982);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'phpconcom',
));

$body =<<<'EOT'
<p>
Last year, a new conference launched, <a href="http://brooklynbeta.org/">Brooklyn Beta</a>.
The buzz I heard about it from attendees was amazing; words like "inspiring,"
"intimate," and "energizing" were all used to describe the experience. I found
myself wishing I'd made time in my schedule to attend.
</p>

<p>
Fast forward a few months, and a new conference has been announced,
<a href="http://phpcon.org/">the PHP Community Conference</a>. It has similar goals to
Brooklyn Beta: create a conference where we can talk about the language we love
so passionately.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
<a href="http://phpcon.org/speakers">The lineup of speakers</a> is simply amazing. These are
people doing amazing things with PHP, and passionate about PHP and what they're
building with it. Some have been working on projects forever (Brian Moon's been
working on Phorum almost as long as PHP has been around), others are building
the next generation of tools and sites we'll be using (Paul Reinheimer's XHProf
and WonderProxy, FictiveKin's Gimme Bar). 
</p>

<p>
I was fortunate enough to be invited to speak as well, to speak about Zend
Framework 2, and provide some training in some of the exciting new tools and
components you'll be seeing from the project this year.
</p>

<p>
I'm particularly excited to finally meet <a href="http://toys.lerdorf.com/">Rasmus</a> --
we've not yet managed to cross paths in the 5 years and greater than a dozen
conferences I've attended or spoken at. But most exciting is knowing that, due
to the small size and venue of the conference, attendees will be peers with all
of us speaking, allowing us to have good dialog about what it is we <em>do</em> and
want to <em>create</em>.
</p>

<p>
If you haven't <a href="http://phpcon.eventbrite.com/">bought your ticket yet</a> -- and
hey, they're only $300! a steal at twice the price! -- what are you waiting for?
</p>
EOT;
$entry->setExtended($extended);

return $entry;