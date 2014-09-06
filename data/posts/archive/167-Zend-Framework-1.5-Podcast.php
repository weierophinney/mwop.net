<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('167-Zend-Framework-1.5-Podcast');
$entry->setTitle('Zend Framework 1.5 Podcast');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1207229458);
$entry->setUpdated(1207229458);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Cal has released a new <a
        href="http://devzone.zend.com/tag/PHP_Abstract">PHP Abstract</a> podcast
    today on the <a href="http://devzone.zend.com/">Zend Developer Zone</a>, an
    interview with Wil Sinclair, the project manager for <a
        href="http://framework.zend.com/">Zend Framework</a>, and Brad Cottel,
    Zend's product Evangelist. In it, they talk quite a bit about the work I've
    done on Zend Form, and also a lot about the proposal process. 
</p>

<p>
    If you're interested in the new 1.5 features, or how the proposal process
    works and who contributes to the community, 
    <a href="http://devzone.zend.com/article/3348-PHP-Abstract-Podcast-Episode-37-Zend-Framework-1.5">give it a listen!</a>
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;