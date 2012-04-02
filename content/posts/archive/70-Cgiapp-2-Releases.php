<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('70-Cgiapp-2-Releases');
$entry->setTitle('Cgiapp - 2 Releases');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1115390316);
$entry->setUpdated(1115390560);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've made two releases of Cgiapp this week, 1.6.2 and 1.6.3. 
</p>
<p>
    1.6.2 was tested in a PHP 4.3.4 environment, and features several bugfixes
    that give PHP4 compatibility.  1.6.3 fixes a change in load_tmpl() that
    broke backwards compatibility.
</p>
<p>
    As usual, Cgiapp is available on <a
        href="http://cgiapp.sourceforge.net./">the SourceForge website</a>, as
    is a complete Changelog and documentation.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;