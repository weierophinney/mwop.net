<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('172-View-Helpers-Tutorial-on-DevZone');
$entry->setTitle('View Helpers Tutorial on DevZone');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1209390660);
$entry->setUpdated(1209390660);
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
    I have another tutorial in my <a href="http://framework.zend.com/">Zend Framework</a> MVC series up on <a href="http://devzone.zend.com/">DevZone</a> 
    today, this time on <a href="http://devzone.zend.com/article/3412-View-Helpers-in-Zend-Framework">View
    Helpers</a>. If you're curious on how to create view helpers, override the
    standard view helpers, or how some of the standard view helpers such as
    partials and placeholders work, give it a read!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;