<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('this-is-the-stub-used-in-the-uri-and-should-be-unique');
$entry->setTitle('New site!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1300744335);
$entry->setUpdated(1301034313);
$entry->setTimezone('America/New_York');
$entry->setTags(array('php', 'personal'));

$body =<<<'EOT'
<p>
    This is the principal body of the post, and will be shown everywhere.
</p>
EOT;
$entry->setBody($body);

$extended =<<<'EOT'
This is the extended portion of the entry, and is only shown in the main entry 
views.
EOT;
$entry->setExtended($extended);

return $entry;
