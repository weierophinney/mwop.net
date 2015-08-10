<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('45-PHP_SELF-versus-SCRIPT_NAME');
$entry->setTitle('PHP_SELF versus SCRIPT_NAME');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1097631297);
$entry->setUpdated(1097631303);
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
    I've standardized my PHP programming to use the environment variable
    <b>SCRIPT_NAME</b> when I want my script to refer to itself in links and
    form actions. I've known that <b>PHP_SELF</b> has the same information, but
    I was more familiar with the name 'SCRIPT_NAME' from using it in perl, and
    liked the feel of it more as it seems to describe the resource better
    ('PHP_SELF' could stand for the path to the PHP executable if I were to go
    by the name only).
</p>
<p>
    However, I just noticed a post on the php.general newsgroup where somebody
    asked what the difference was between them. Semantically, there isn't any;
    they should contain the same information. However, historically and
    technically speaking, there is. <b>SCRIPT_NAME</b> is defined in the CGI 1.1
    specification, and is thus a standard. <em>However</em>, not all web servers
    actually implement it, and thus it isn't necessarily <em>portable</em>.
    <b>PHP_SELF</b>, on the other hand, is implemented directly by PHP, and as
    long as you're programming in PHP, will always be present.
</p>
<p>
    Guess I have some grep and sed in my future as I change a bunch of
    scripts...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;