<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('44-PHP-Continue-processing-after-script-aborts');
$entry->setTitle('PHP: Continue processing after script aborts');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1097239823);
$entry->setUpdated(1097239828);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    Occasionally, I've needed to process a lot of information from a script, but
    I don't want to worry about PHP timing out or the user aborting the script
    (by clicking on another link or closing the window). Initially, I
    investigated <a href="http://php.net/register_shutdown_function">register_shutdown_function()</a>
    for this; it will fire off a process once the page finishes loading.
    Unfortunately, the process is still a part of the current connection, so it
    can be aborted in the same way as any other script (i.e., by hitting stop,
    closing the browser, going to a new link, etc.).
</p>
<p>
    However, there's another setting initialized via a function that can
    override this behaviour -- i.e., let the script continue running after the
    abort. This is <a href="http://php.net/ignore_user_abort">ignore_user_abort()</a>. By
    setting this to true, your script will continue running after the fact.
</p>
<p>
    This sort of thing would be especially good for bulk uploads where the
    upload needs to be processed -- say, for instance, a group of images or
    email addresses.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;