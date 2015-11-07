<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('151-PHP-Advent-Calendar');
$entry->setTitle('PHP Advent Calendar');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1197154223);
$entry->setUpdated(1197154223);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
));

$body =<<<'EOT'
<p>When <a href="http://shiflett.org/">Chris Shifflet</a> contacted me about his idea for the <a href="http://shiflett.org/blog/2007/dec">PHP Advent Calendar</a>, I was intrigued; I've occasionally seen entries for the <a href="http://www.perladvent.org/">Perl Advent Calendar</a>, and found them uniformly interesting. So far, I've very much enjoyed the entries this year, and have been especially thrilled to see some well-known bloggers blogging on topics we don't normally see them discuss.

</p>

<p>

Hopefully <a href="http://shiflett.org/blog/2007/dec/php-advent-calendar-day-8">my entry</a> strikes a chord with someone; best of the holiday seasons to all of you!

</p>



  <!-- technorati tags begin --><p style="font-size:10px;text-align:right;">Tags: <a href="http://technorati.com/tag/phpcommunity" rel="tag">phpcommunity</a></p><!-- technorati tags end -->
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;