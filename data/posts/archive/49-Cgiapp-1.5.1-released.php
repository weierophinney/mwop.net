<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('49-Cgiapp-1.5.1-released');
$entry->setTitle('Cgiapp 1.5.1 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1099591799);
$entry->setUpdated(1099591822);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    At work this week, I discovered a bug with how I was calling
    set_error_handler() in Cgiapp's run() method. Evidently passing a reference
    in a PHP callback causes issues! So, I corrected that.
</p>
<p>
    I also made a minor, one-character change to query() to make it explicitly
    return a reference to the $_CGIAPP_REQUEST property array.
</p>
<p>
    You can see full details at the <a href="download?mode=view&id=11">Cgiapp
        download page</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;