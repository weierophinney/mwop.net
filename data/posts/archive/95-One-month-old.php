<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('95-One-month-old');
$entry->setTitle('One month old');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(false);
$entry->setCreated(1127279553);
$entry->setUpdated(1217022532);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'private',
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
    Liam turned one month old today, and I was not there to celebrate. I miss
    the little guy.
</p>
<p>
    Jen tells me that his one month checkup went well. The little man is not so
    little anymore: he now weighs in at 6 pounds 1 oz (a pound in a week! and
    2.5 pounds since birth!), and measures at 18.75 inches (two inches!). Jen
    tells me he has developed a third chin since I left two days ago.
</p>
<p>
    More importantly, the doctors say he's doing so well that he no longer needs
    us to wake him to feed! He's completely recovered from his low blood sugar,
    and, indeed, is thriving.
</p>
<p>
    Not being home, I have no pictures to post. But you can <a
        href="/gallery/album/Main/Liam">view the archives</a> to get your Liam
    fix, just as I'm doing now.
</p>
<p>
    Happy one-month birthday, little guy!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;