<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('34-POD-for-PHP');
$entry->setTitle('POD for PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1080520419);
$entry->setUpdated(1095702829);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'personal',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    I was lamenting at work the other day that now that I've discovered OO and
    templating with PHP, the only major feature missing for me is a way to
    easily document my programs. I'm a big fan of perl's POD, and use it fairly
    extensively, even for simple scripts -- it's a way to provide a quick manual
    without needing to worry too much about how to format it.
</p>
<p>
    So, it hit me on the way home Friday night: what prevents me from using POD
    in multiline comments of PHP scripts? I thought I'd give it a try when I got
    home.
</p>
<p>
    First I googled for 'POD for PHP', and found a link to perlmongers where
    somebody recounted seeing that exact thing done, and how nicely it worked.
</p>
<p>
    Then I tried it.. and it indeed worked. So, basically, I've got all the
    tools I love from perl in PHP, one of which is borrowed directly from the
    language!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;