<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('180-phpwomen-at-DPC08');
$entry->setTitle('phpwomen at DPC08');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1213983001);
$entry->setUpdated(1214037271);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'dpc08',
  3 => 'phpwomen',
));

$body =<<<'EOT'
<p>
    So, one thing I forgot to mention in my <a
        href="http://weierophinney.net/matthew/archives/179-DPC08-Wrapup.html">DPC08
        wrapup</a> was my involvement with the <a
        href="http://www.phpwomen.org/">phpwomen booth</a>. <a
        href="http://www.khankennels.com/blog/">Lig</a> emailed me some months
    in advance asking if I'd be an official "booth babe" while at the conference
    -- basically wearing a T-shirt to show my support of the movement, and
    answering any questions that others might have regarding it. While I haven't
    been particularly active with phpwomen, I of course agreed.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    As <a href="http://www.lornajane.net/">Lorna Jane</a> noted, there were
    <em>very</em> few women at the conference. I'd say estimating them at 2% of
    total attendees would very much be a stretch. Also very strange was how
    little interest the phpwomen booth attracted -- during those times I was at
    the booth, we'd get a few people coming forward, but not many... and very
    few males. This is very much in contrast to my observations at ZendCon last
    year, when there were almost constantly folks at the booth, and dozens of
    males were wearing the phpwomen t-shirts throughout the conference.
</p>

<p>
    That said, one very funny picture came out of my participation, snapped by
    <a href="http://www.leftontheweb.com/">Stefan</a>:
</p>

<div style="text-align: center">
<img src="http://farm4.static.flickr.com/3114/2583600706_fa10962945.jpg?v=0"
alt="Feeling lucky with the booth babe" height="500" width="375" />
</div>

<p>
    I think it speaks for itself, no? :-)
</p>
EOT;
$entry->setExtended($extended);

return $entry;