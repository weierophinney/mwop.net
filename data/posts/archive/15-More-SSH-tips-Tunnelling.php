<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('15-More-SSH-tips-Tunnelling');
$entry->setTitle('More SSH tips: Tunnelling');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074896354);
$entry->setUpdated(1095700830);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    I wrote up a short tutorial today on the IT wiki about SSH tunneling. What I
    didn't know is that you can start a tunnel <em>after</em> you've already
    ssh'd to another machine. Basically, you:
</p>
<ul>
    <li>Press Enter</li>
    <li>Type ~C</li>
</ul>
<p>
    and you're at an <tt>ssh></tt> prompt. From there, you can issue the
    tunnel command of your choice: <tt>-R7111:localhost:22</tt>, for instance.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;