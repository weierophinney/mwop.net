<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('48-Cgiapp-1.5-released');
$entry->setTitle('Cgiapp 1.5 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1098988061);
$entry->setUpdated(1104552196);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    Cgiapp 1.5 has been released; you may now <a href="download?mode=view_download&id=11">download it</a>.
</p>
<p>
    This release fixes a subtle bug I hadn't encountered before; namely, when a
    method name or function name is passed as an argument to mode_param(), run()
    was receiving the requested run mode... and then attempting to process that
    as the mode param. The behaviour is now fixed, and is actually simpler than
    the previous (non-working) behaviour.
</p>
<p>
    Also, on reading <a href="http://shiflett.org">Chris Shiflet's</a> paper on
    PHP security, I decided to reinstate the query() method. I had been using
    $_REQUEST to check for a run mode parameter; because this combines the GET,
    POST, <b>and</b> COOKIE arrays, it's considered a bit of a security risk.
    query() now creates a combined array of GET and POST variable (POST taking
    precedence over GET) and stores them in the property $_CGIAPP_REQUEST; it
    returns a reference to that property. run() uses that property to determine
    the run mode now.
</p>
<p>
    Enjoy!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;