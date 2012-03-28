<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('85-File_Fortune-0.9.0-Released');
$entry->setTitle('File_Fortune 0.9.0 Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1121366292);
$entry->setUpdated(1121366673);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <a href="http://pear.php.net/package/File_Fortune">File_Fortune</a> has been
    released via <a href="http://pear.php.net/">PEAR</a>.
</p>
<p>
    Changes since the proposal include the addition of a static method for
    retrieving a random fortune from a list of files, the addition of a DocBook
    tutorial listing several usage examples, renaming the exception classes to
    conform to PEAR CS, some minor bugfixes, and some streamlining of the
    package definition.
</p>
<p>
    Included in the release is an example script, phpFortune, that can be used
    on the command line to get random fortunes from one or more fortune files.
    Usage is:
</p>
<pre>
    $> phpFortune fortunes
    $> phpFortune fortunes simpsons hitchhiker
    $> phpFortune
</pre>
<p>
    Enjoy!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;