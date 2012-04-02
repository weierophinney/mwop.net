<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('56-Cgiapp-1.5.3-released');
$entry->setTitle('Cgiapp 1.5.3 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1105724597);
$entry->setUpdated(1105724604);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    1.5.3 fixes an issue introduced by 1.5.2 that creates a performance hit
    whenever the run mode is being determined by function name or CGI
    parameter. More details <a href="/matthew/download?mode=view_download&id=11">on the
    Cgiapp download page</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;