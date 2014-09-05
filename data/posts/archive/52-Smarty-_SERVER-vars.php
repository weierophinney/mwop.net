<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('52-Smarty-_SERVER-vars');
$entry->setTitle('Smarty $_SERVER vars');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1104552038);
$entry->setUpdated(1104552285);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
I don't know why I never bothered to look this up, but I didn't. One thing I
typically do in my parent Cgiapp classes is to pass $_SERVER['SCRIPT_NAME'] to
the template. I just found out -- through the pear-general newsgroup -- that
this is unnecessary: use $smarty.server.KEY_NAME to access any $_SERVER vars
your template might need.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;