<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('84-File_Fortune-accepted-to-PEAR');
$entry->setTitle('File_Fortune accepted to PEAR');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1121313665);
$entry->setUpdated(1121313926);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    What a way to start the day -- I had an email from PEPR letting me know that
    my <a
        href="http://pear.php.net/pepr/pepr-proposal-show.php?id=263">File_Fortune
    proposal</a> had been accepted!
</p>
<p>
    File_Fortune is a PHP OOP interface to reading and writing fortune files.
    It is loosely based on the perl module Fortune.pm, but modified
    significantly to better work with PHP file access methods, as well as to add
    writing capability.
</p>
<p>
    I will be uploading my initial release shortly, probably as a beta or RC. 
</p>
<p>
    So, go fetch those Simpsons, Hitchhiker's Guide, and Star Wars fortune
    cookie databases and prepare to add random quotes to your site!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;