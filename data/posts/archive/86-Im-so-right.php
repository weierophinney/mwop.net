<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('86-Im-so-right');
$entry->setTitle('&quot;I\'m so right&quot;');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1121372372);
$entry->setUpdated(1121372636);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
    Not long ago, as Maeve and I were en route to Jen's work one evening, Maeve
    was being very insistent that certain things were a certain way, and was
    very adamant even as I used a placating tone with her. I asked her,
    jokingly, "Why are you so contrary today?" Her reply?
</p>
<p>
    "Because I'm so smart, and because I'm so right."
</p>
<p>
    I almost wrecked the car as I guffawed. Kids. They're so dang cute. And so
    right!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;