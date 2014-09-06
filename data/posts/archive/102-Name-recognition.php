<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('102-Name-recognition');
$entry->setTitle('Name recognition');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1134251818);
$entry->setUpdated(1134252015);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
    Don't know if this is actually possible, but it appears Liam is already
    starting to associate names with people!
</p>
<p>
    Just now, Jen was holding Liam, and turned him to face Maeve and I, who were
    sitting on the couch. He wasn't looking anywhere in particular. Then, Jen
    said, "Hi, Papa!" and his eyes moved to focus on me. A moment later, she
    said, "Hi, big sister!" and he moved is eyes to focus on Maeve.
</p>
<p>
    As I type this, Jen just tried the experiment again with Maeve, and again he
    moved his eyes and head to look at her!
</p>
<p>
    How cool and amazing it is to witness child development!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;