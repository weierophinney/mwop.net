<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('75-Planet-PHP');
$entry->setTitle('Planet PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1117126091);
$entry->setUpdated(1117567079);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I recently had an email exchange with <a href="http://blog.bitflux.ch">chregu</a> of <a href="http://www.planet-php.net">Planet PHP</a> regarding Planet PHP's blog selection. I've been subscribing to its
    RSS feed for over a year now, but was uncertain how blogs were selected --
    was it based on popularity of the developer, projects the developer works on
    (for instance, PEAR), etc. I felt that it was time for this information to
    be made public; it helps inform the readership why they're reading what
    they're reading.
</p>
<p>
    And my exchange with him resulted in his <a href="http://blog.bitflux.ch/archive/2005/05/26/planet-php-faq.html">Planet PHP FAQ</a> blog entry, which hopefully someday will become a static page
    on the site. Additionally, I found out how new blogs get added, and am proud
    to announce I'm now in the lineup!
</p>
<p>
    (Of course, the timing could not really be worse, come to think of it. I'm
    moving my family in to town this weekend (from our house in the Green
    Mountains of Vermont), and I need to make arrangements for hosting
    weierophinney.net in the interim while we wait for phone and DSL service
    -- which won't be up for at least another week :-( I'm sure I'll figure out
    something... stay tuned!)
</p>
<p>
    <b>Update:</b> corrected link to Planet PHP FAQ.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;