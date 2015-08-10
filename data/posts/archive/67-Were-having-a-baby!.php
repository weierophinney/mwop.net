<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('67-Were-having-a-baby!');
$entry->setTitle('We\'re having a baby!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1114293712);
$entry->setUpdated(1114294340);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>
    I can't believe I haven't announced this to the world yet, but Jen and I are
    expecting another baby! The due date is mid-September. And.... we decided at
    the ultrasound this past week we would go ahead and find out the gender...
    and....
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p style="font-size: 1.5em; font-weight: bold;">
    It's A BOY!!!!
</p>
<p>
    I'm terribly excited, in case you can't tell. I'd had a dream shortly after
    we found out we were pregnant that we were at the doctor's for the
    ultrasound, and I'd seen quite clearly a little penis; I've been convinced
    since then that we would have a boy. It was amazing to have it confirmed --
    so many emotions ran through me -- how will I be a father to a boy, how will
    I teach him to shave, will I need to learn about sports if he takes to them,
    IT'S A BOY!!!
</p>
<p>
    We've been going through baby names since then. Before Maeve was born, we'd
    planned on calling a boy Aidan; a couple years ago, we though maybe Will
    would be a good name (Will is one of the principal characters in Philip
    Pullman's <em>His Dark Materials</em> trilogy). However, one of my
    co-workers, has two boys -- Will and Aidan -- whom Maeve plays with, so
    those names are out.
</p>
<p>
    We've narrowed the names down to four, though: Nolan, Liam, Gavin, and
    Devin. Leave a comment and let us know what you think!
</p>
<p>
    Oh, did I mention -- it's a boy!!!!
</p>
EOT;
$entry->setExtended($extended);

return $entry;