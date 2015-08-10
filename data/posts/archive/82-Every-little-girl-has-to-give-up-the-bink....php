<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('82-Every-little-girl-has-to-give-up-the-bink...');
$entry->setTitle('Every little girl has to give up the bink...');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1120766356);
$entry->setUpdated(1121052714);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
So, last night, Maeve fell asleep in the early evening, on the couch, clutching
her sippy cup and sucking on her bink while watching Scooby Doo. She woke up an
hour later, and after she'd been up for a while and was less groggy, she
announced to Jen and me that, "I'm not going to use my bink ANY MORE. Every
little girl has to give up the bink some time, when they're four, so I'm not
going to use the bink EVER AGAIN." (Imagine dramatic pauses between the all-caps
words there...)
</p>

<p>
This coming from the girl who has a fit every time her bink isn't within
eyesight and reach. Needless to say, we didn't quite believe her, but we were
willing to support her. We told her that if she wants to stop using the bink,
that's okay; it's also okay if she decides to use it again. (Fully expecting
she'd want it within minutes of going to bed.)
</p>

<p>
Well... Maeve slept all night without it, and didn't want it this morning, nor
in the car. She's adamant, our little warrior queen! (Which is what the Gaelic
Maeve translates to in English, in case you were wondering.)
</p>

<p>
I might be jumping the gun here, but I get the feeling our little girl has taken
another step in growing up... and I'm bewildered and a little sad. Much as I've
hated the bink the past year, I also associate it with my little girl... and
she's getting so she's not so little any more!
</p>
<p>
<b>UPDATE:</b> I jumped the gun. She <em>did</em> go a full 24 hours, but the
following night decided she wanted the bink again. But there <em>is</em> hope
for a bink-less future... 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;