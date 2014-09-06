<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('238-Please-Join-Me-At-TEK-X');
$entry->setTitle('Please Join Me At TEK-X');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1270648135);
$entry->setUpdated(1271019569);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'tekx',
));

$body =<<<'EOT'
<p>
    I'll be speaking this year at <a href="http://tek.phparch.com/">TEK-X</a>,
    this year's incarnation of the php|tek conference, in Chicago in May.
</p>

<p style="text-align:center;"><a href="http://tek.phparch.com/"
    target="_blank"><img src="/uploads/TEKX_SpeakerBadge_135x135.png"
    width="135" height="135" title="TEK-X PHP Conference, Chicago, IL, May 18-21"/></a></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I have the distinct privilege of doing another tutorial this year with <a
        href="http://lornajane.net/">Lorna Jane Mitchell</a>. Unlike last year,
    we're not sticking to just Subversion, but instead <a
        href="http://tek.phparch.com/talks/#TEKXT02">covering a whole spectrum
        of PHP development practices</a>, including coding standards and quality
    assurance. We're both very excited about the talk; please join us on
    tutorial day!
</p>

<p>
    I'm also doing an "upgrade" or "reboot" of my Domain Models talk, which I
    debuted last year during the UnCon. This time around, I'm going to focus on
    <a href="http://tek.phparch.com/talks/#TEKXS15">how usage of NoSQL for the
        datastore</a> affects and informs your domain models -- and how it might
    very well radically change how you develop your applications.
</p>

<p>
    If you'll be attending TEK-X this year, look me up while there; I'd love to
    meet you!
</p>
EOT;
$entry->setExtended($extended);

return $entry;