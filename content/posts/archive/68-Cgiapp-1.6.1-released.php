<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('68-Cgiapp-1.6.1-released');
$entry->setTitle('Cgiapp 1.6.1 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1114388616);
$entry->setUpdated(1114388806);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
A user noted in a comment to my blog on the 1.6.0 release that I'd included a 'public' keyword in the s_param() method declaration... which caused compilation to fail in PHP4. So, quick on the heels of that release, I've released 1.6.1 to correct this issue. Downloads are available at the <a href="http://cgiapp.sourceforge.net/index.php/view/Downloads">Cgiapp website</a>.
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;