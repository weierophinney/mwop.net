<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('109-Cgiapp-1.9.0-released');
$entry->setTitle('Cgiapp 1.9.0 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1146610860);
$entry->setUpdated(1146578668);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I released Cgiapp 1.9.0 into the wild last night. The main difference
    between 1.8.0 and 1.9.0 is that I completely removed the plugin system. I
    hadn't had any users reporting that they were using it, and, in point of
    fact, the overloading mechanism I was using was causing some obscure issues,
    particularly in the behaviour of cgiapp_postrun().
</p>
<p>
    As usual, you can find more information and links to downloads
    <a href="http://cgiapp.sourceforge.net/">at the Cgiapp site.</a>
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;