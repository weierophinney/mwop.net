<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('169-Action-Helpers-in-ZF');
$entry->setTitle('Action Helpers in ZF');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1207577465);
$entry->setUpdated(1207577465);
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
    I've posted <a href="http://devzone.zend.com/article/3350-Action-Helpers-in-Zend-Framework">a new article on Action Helpers</a> in Zend Framework's MVC
    on the <a href="http://devzone.zend.com/">Zend Developer Zone</a>. If you've
    ever wanted more information on these, follow the link.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;