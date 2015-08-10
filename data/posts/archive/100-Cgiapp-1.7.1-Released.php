<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('100-Cgiapp-1.7.1-Released');
$entry->setTitle('Cgiapp 1.7.1 Released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1133407920);
$entry->setUpdated(1133408019);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I was able to roll a long-needed (and by some, long awaited) bugfix release
    of Cgiapp this morning. Cgiapp 1.7.1 corrects the following issues:
</p>
<ul>
    <li>Cgiapp5::run() was corrected to call query() instead of
    cgiapp_get_query() (which caused a fatal error)</li>
    <li>Cgiapp::__call() and Cgiapp5::__call() now report the name of the method
    called in errors when unable to find matching actions for that method.</li>
</ul>
<p>
    As usual, downloads are available <a href="/matthew/download">on my site</a> as well as <a href="http://prdownloads.sourceforge.net/cgiapp/Cgiapp-1.7.1.tgz?download">via SourceForge</a>.
</p>
<p>
    <b>Update:</b> The link on my site for downloading Cgiapp has been broken;
    I've now fixed it.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;