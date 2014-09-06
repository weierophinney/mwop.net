<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('38-Whats-keeping-that-device-in-use');
$entry->setTitle('What\'s keeping that device in use?');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1094952431);
$entry->setUpdated(1095703160);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    Ever wonder what's keeping that device in use so you can't unmount it? It's
    happened to me a few times. The tool to discover this information?
    <kbd>lsof</kbd>.
</p>
<p>
    Basically, you type something like: <kbd>lsof /mnt/cdrom</kbd> and it gives
    you a <kbd>ps</kbd>-style output detailing the PID and process of the
    processes that are using the cdrom. You can then go and manually stop those
    programs, or kill them yourself.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;