<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('114-Always-check-the-version-before-release');
$entry->setTitle('Always check the version before release');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1149473855);
$entry->setUpdated(1149474204);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
Last week, I had someone bring to my attention that the <a href="http://php.net/spl">SPL's</a> Countable interface was actually first released in PHP 5.1.0... which means I needed to update the PHP dependency on <a href="/phly/index.php?package=Phly_Hash">Phly_Hash</a>. I also needed to do so on <a href="/phly/index.php?package=Phly_Config">Phly_Config</a> as it depends on Phly_Hash. I released 1.1.1 versions of each yesterday; the only change in each is the PHP version dependency.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;