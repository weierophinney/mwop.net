<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('55-Cgiapp-1.5.2-released');
$entry->setTitle('Cgiapp 1.5.2 released');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1105679523);
$entry->setUpdated(1105679542);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    At work, we've been developing a new platform for our website, based
    entirely on Cgiapp. This week we released the first stage of it: <a href="http://www.garden.org/home">garden.org</a> and <a href="http://assoc.garden.org">assoc.garden.org</a>. These should stand
    as good testament to Cgiapp's robustness!
</p>
<p>
    With all that development, and also with some communication from other
    Cgiapp users, I've made some changes to Cgiapp, and release version 1.5.2
    this evening.
</p>
<p>
    1.5.2 is mainly security and bugfixes. Error handling was somewhat broken in
    1.5.1 -- it wouldn't restore the original error handler gracefully. This is
    now corrected. Additionally, I've made run() use the array returned by
    query() -- consisting of the $_GET and $_POST arrays -- in determining the
    run mode. Finally, I've modified the behaviour of how run() determines the
    current run mode: if the mode parameter is a method or function name, it
    cannot be a Cgiapp method or a PHP internal function. This allows more
    flexibility on the part of the programmer in determining the mode param --
    words like 'run' and 'do' can now be used without causing massive problems
    (using 'run' would cause a race condition in the past).
</p>
<p>
    As usual, Cgiapp is avaiable <a href="download?mode=view_download&id=11">in
        the downloads area</a>. Grab your tarball today!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;