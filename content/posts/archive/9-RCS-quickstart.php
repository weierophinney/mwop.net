<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('9-RCS-quickstart');
$entry->setTitle('RCS quickstart');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074721522);
$entry->setUpdated(1094870275);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
));

$body =<<<'EOT'
<p>Gleaned from <em>Linux Server Hacks</em></p>
<ul>
    <li>Create an RCS directory</li>
    <li>Execute a 'ci -i filename'</li>
    <li>Execute a 'co -l filename' and edit as you wish.</li>
    <li>Execute a 'ci -u filename' to check in changes.</li>
</em></em></ul>
<p>The initial time you checkout the copy, it will be locked, and this can cause
problems if someone else wishes to edit it; you should probably edit it once and
put in the version placeholder in comments somewhere at the top or bottom:</p>
<pre>$VERSION$</pre>
<p>and then check it back in with the -u flag to unlock it.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;