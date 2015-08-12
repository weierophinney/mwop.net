<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('158-Backwards-Compatibility');
$entry->setTitle('Backwards Compatibility');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1202396171);
$entry->setUpdated(1202396171);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    <a href="http://jansch.nl/">Ivo</a> already pointed this out, but I want to
    point it out again: Boy Baukema writes 
    <a href="http://www.ibuildings.nl/blog/archives/541-Backward-compatibility,-bane-of-the-developer.html">a very nice entry regarding backwards compatibility</a>
    on the ibuildings.nl corporate blog.
</p>

<p>
    Backwards compatibility (BC) is a tricky thing to support, even when you
    strive hard to, as Boy puts it, "think hard about your API" prior to
    release. Somebody will always come along and point out ways it could have
    been done better or ways it could be improved. I've had to wrestle with
    these issues a ton since joining the Zend Framework team, and while it often
    feels like the wrong thing to do to tell somebody, "too little, too late"
    when they have genuinely good feedback for you, its often in the best
    interest of the many users already using a component.
</p>

<p>
    I had the pleasure of meeting Boy last year when visiting the ibuildings.nl
    offices, and he's got a good head on his shoulders. He does a nice job
    outlining the issues and a number of approaches to BC; if you develop a
    project for public consumption, you should definitely head over and read
    what he has to say.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;