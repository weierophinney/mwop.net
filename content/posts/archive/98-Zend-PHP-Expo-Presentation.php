<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('98-Zend-PHP-Expo-Presentation');
$entry->setTitle('Zend PHP Expo Presentation');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1129661890);
$entry->setUpdated(1129673741);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Mike and I have just finished our talk on "Setting Up PHP". The number of
    attendees was N + 1, where N is the number of speakers... which was to be
    expected, as we were presenting opposite a session on web services,
    Shiflett's PHP Security talk, and a crash course on the ZCE. However, it's
    undoubtedly the best presentation missed by attendees. :-)
</p>
<p>
    <a href="/files/Presentation.ppt">You can grab our presentation online.</a>
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;