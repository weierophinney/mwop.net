<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('177-ZF-Subversion-Reorganization');
$entry->setTitle('ZF Subversion Reorganization');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1211489525);
$entry->setUpdated(1211489525);
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
    If you've found that your SVN checkouts or svn:externals of 
    <a href="http://framework.zend.com/">Zend Framework</a> are not working
    currently, then you missed the announcements on fw-general and #zftalk; I've
    just completed a 
    <a href="http://framework.zend.com/wiki/display/ZFUSER/Subversion+Migration+Information">Subversion Reorganization</a>
    that is part of our new proposal process and 'Extras' offering. Please
    follow the link for details on how to update your installs.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;