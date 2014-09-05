<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('92-Welcome-home,-Liam!');
$entry->setTitle('Welcome home, Liam!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(false);
$entry->setCreated(1125770135);
$entry->setUpdated(1217022518);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'private',
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
    Yesterday, after 14 days in the hospital, Liam came home!
</p>
<p style="text-align: center;">
    <img src="/gallery/gal/homecoming/_thb_43630020.jpg" alt="Liam" height="94" width="125" />
</p>
<p>
    He gained a full half-pound while in the NICU, eats like a horse, and is a
    sweet little man. We're all happy to have both him and Mommy back home;
    Maeve was particularly excited, and performed a little dance at daycare when
    I told her the whole family was home waiting for her.
</p>
<p>
    Thank you, everyone, for the support over the past two weeks! And welcome
    home, Liam!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;