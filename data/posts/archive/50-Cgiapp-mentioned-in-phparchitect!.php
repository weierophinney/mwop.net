<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('50-Cgiapp-mentioned-in-phparchitect!');
$entry->setTitle('Cgiapp mentioned in php|architect!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1099592057);
$entry->setUpdated(1099592063);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    A new <a href="download?mode=view&id=11">Cgiapp</a> user reported they had
    stumbled across the project in <a href="http://www.phparch.com">php|architect</a>! It's in the current,
    October 2004 issue, in the News section, prominently displayed in the upper
    right corner of the page. The announcement blurb is straight
    from my <a href="http://freshmeat.net/projects/cgiapp/">freshmeat project
        page</a> for version 1.4. Cgiapp is carving a name for itself!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;