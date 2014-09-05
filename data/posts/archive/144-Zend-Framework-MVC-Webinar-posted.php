<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('144-Zend-Framework-MVC-Webinar-posted');
$entry->setTitle('Zend Framework MVC Webinar posted');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1190813109);
$entry->setUpdated(1191393594);
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
    Last Wednesday, I presented a webinar for Zend entitled "MVC applications
    with Zend Framework". We had more than 50 attendees, most of whom stayed on
    the whole time. For those of you who attended, thanks for the great
    questions and comments. 
</p>
<p>
    If you would like to view the webinar, download the slides, or download the
    example code (a hello world app), visit the <a
        href="http://www.zend.com/webinar">Zend.com Webinar page</a> and look
    for the presentation; as of today, it's the first one on the list, but that
    will change as more webinars are presented.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;