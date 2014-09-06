<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('170-ZF-Plugins-Tutorial-on-DevZone');
$entry->setTitle('ZF Plugins Tutorial on DevZone');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1208179332);
$entry->setUpdated(1208635432);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm doing a series of articles on various 
    <a href="http://framework.zend.com/">Zend Framework</a> MVC topics for the
    <a href="http://devzone.zend.com/">Zend Developer Zone</a>. Last week, I
    covered Action Helpers. This week, I cover 
    <a href="http://devzone.zend.com/article/3372-Front-Controller-Plugins-in-Zend-Framework">Front Controller Plugins</a>.
    If you've ever been mystified by or curious about this subject, head on over
    and give it a read!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;