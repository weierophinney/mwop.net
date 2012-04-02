<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('39-Learn-something-new-everyday');
$entry->setTitle('Learn something new everyday');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1076934819);
$entry->setUpdated(1095703239);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    <a href="http://www.linux.com">Linux.com</a> has had a running series on CLI
    commands for Newbies. Most of it has been very basic, but there are still a
    few gems within. For instance, today I was introduced to <kbd>apropos</kbd>
    and <kbd>whatis</kbd>. Give a search term to the former, and it will list
    all programs in which the search term is found in the manpages; give a
    program name to the latter, and it will tell you which man page addresses
    it.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;