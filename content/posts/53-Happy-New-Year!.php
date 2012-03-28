<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('53-Happy-New-Year!');
$entry->setTitle('Happy New Year!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1104552468);
$entry->setUpdated(1104552473);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    It's about 50 minutes shy of 2005 here, and Maeve has finally succumbed to
    sleep, I'm almost done with my stout, and we're in West Bolton without TV
    for the second year running (yay!).
</p>
<p>
    I hope the new year brings peace and happiness to one and all! Happy coding!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;