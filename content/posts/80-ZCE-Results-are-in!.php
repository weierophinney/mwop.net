<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('80-ZCE-Results-are-in!');
$entry->setTitle('ZCE - Results are in!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1119972780);
$entry->setUpdated(1120038486);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    Got the official notification: I passed the <a
        href="http://www.zend.com/store/education/certification/zend-php-certification.php">Zend
        PHP Certification Exam</a>, and can now report I'm a Zend Certified
    Engineer (ZCE)!
</p>
<p style="text-align: center;">
    <a href="http://zend.com/zce.php?c=ZEND901102&r=0502029"><img
        src="/matthew/img/zce_logo.gif" alt="Zend Certified Engineer" height="47"
        width="73" /></a>
</p>
<p>
    Thanks go to my bosses at <a href="http://assoc.garden.org/">NGA</a> for
    giving me the opportunity to attend <a
        href="http://www.phparch.com/tropics">php|Tropics</a>, to <a
        href="http://www.phparch.com/">Marco Tabini</a> for offering the ZCE
    exam as part of the php|Tropics conference fee, and to my wife, Jen, and
    daughter, Maeve, for putting up with me while I studied... and being good
    sports about having to stay home while I went to Cancun. Hopefully next time I can take you along!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;