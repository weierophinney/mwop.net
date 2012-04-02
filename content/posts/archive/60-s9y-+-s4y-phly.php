<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('60-s9y-+-s4y-phly');
$entry->setTitle('s9y + s4y = phly');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1111808560);
$entry->setUpdated(1112280030);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    So, I did a little experimentation, and I was able to seamlessly integrate
    Serendipity into my existing website via Smarty, some CSS, and changing just
    a couple of links!
</p>
<p>
    The result is the website you're currently reading!
</p>
<p>
    This means I can now offer trackbacks, comments, RSS feeds, and all the
    other stuff a modern blog should offer... and with minimum fuss and with a
    lot of standards and security. I wish everything I did was this easy.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;