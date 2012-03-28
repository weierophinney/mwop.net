<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('193-Pastebin-app-updates');
$entry->setTitle('Pastebin app updates');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1222539299);
$entry->setUpdated(1223026755);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've been getting a lot of interest in my 
    <a href="http://weierophinney.net/matthew/archives/189-Pastebin-app-and-conference-updates.html">Pastebin</a>
    demo app -- partly by those wanting to play with Dojo+ZF, partly by those
    just interested in the application.
</p>

<p>
    I'm constantly trying to improve the application. I've done one webinar and
    one UnCon session showcasing it, and will be presenting it at 
    <a href="http://dojotoolkit.org/2008/07/10/dojo-developer-day-boston">Dojo Develper Day in Boston</a> 
    this Monday as well as at 
    <a href="http://phpworks.mtacon.com/c/index">php|works</a> later this fall,
    and want to keep the materials up-to-date and freely available. To this end,
    I've created a <a href="http://github.com">Github</a> repository so you can
    track the latest developments, as well as pull custom tarballs:
</p>

<ul>
    <li><a href="http://github.com/weierophinney/pastebin/tree/master">Pastebin
    on Github</a></li>
</ul>

<p>
    All patches and feedback are welcome!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;