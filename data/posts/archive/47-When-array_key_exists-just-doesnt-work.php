<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('47-When-array_key_exists-just-doesnt-work');
$entry->setTitle('When array_key_exists just doesn\'t work');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1098500565);
$entry->setUpdated(1098500575);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    I've been playing with parameter testing in my various Cgiapp classes, and
    one test that seemed pretty slick was the following:
</p>
<pre>
    if (!array_key_exists('some_string', $_REQUEST)) {
        // some error
    }
</pre>
<p>
    Seems pretty straight-forward: $_REQUEST is an associative array, and I want
    to test for the existence of a key in it. Sure, I could use isset(), but it
    seemed...  ugly, and verbose, and a waste of keystrokes, particularly when
    I'm using the param() method:
</p>
<pre>
    if (!isset($_REQUEST[$this->param('some_param')])) {
        // some error
    }
</pre>
<p>
    However, I ran into a pitfall: when it comes to array_key_exists(),
    $_REQUEST isn't exactly an array. I think what's going on is that $_REQUEST
    is actually a superset of several other arrays -- $_POST, $_GET, and
    $_COOKIE -- and isset() has some logic to descend amongst the various keys,
    while array_key_exists() can only work on a single level.
</p>
<p>
    Whatever the explanation, I ended up reverting a bunch of code. :-(
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;