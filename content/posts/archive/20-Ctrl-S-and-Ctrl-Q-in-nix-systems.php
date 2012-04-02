<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('20-Ctrl-S-and-Ctrl-Q-in-nix-systems');
$entry->setTitle('Ctrl-S and Ctrl-Q in *nix systems');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1075057722);
$entry->setUpdated(1095701433);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I just ran into this not long ago, and wish I'd discovered it years ago.
    Basically, <tt>Ctrl-S</tt> <em>suspends</em> a process, while
    <tt>Ctrl-Q</tt> <em>resumes</em> it. This is useful when in <tt>g/vim</tt>
    or <tt>screen</tt> and you manage to lock up your application because you
    accidently hit <tt>Ctrl-S</tt> when reaching for another key combo.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;