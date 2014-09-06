<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('143-NYPHP-Zend-Framework-Presentation');
$entry->setTitle('NYPHP Zend Framework Presentation');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1189780076);
$entry->setUpdated(1189793261);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    This past Wednesday, <a href="http://www.zend.com/">Zend's</a> Chief
    Marketing Officer, <a href="http://blogs.zend.com/author/mark/">Mark de
        Visser</a>, and myself joined the <a href="http://nyphp.org/">NYPHP</a>
    group for a <a
        href="http://nyphp.org/content/calendar/view_entry.php?id=112&date=20070912">special
        event meeting</a>. Mark presented information on Zend's development
    stack and toolset (which I entirely missed, as I was still in transit), and
    I came in to give an overview of <a href="http://framework.zend.com/">Zend
        Framework</a>.
</p>

<p>
    There were some great questions, and nice discussions following the event.
    If you live in New York and do PHP for a living, and haven't attended, you
    should; if you're ever visiting the area, see if you can attend a meeting!
</p>

<p>
    <a href="/uploads/FrameworkPresentation.zip"
        title="FrameworkPresentation.zip" target="_blank">Here are the
        slides.</a> They're done using <a
        href="http://meyerweb.com/eric/tools/s5/">S5</a>, a browser-based
    slideshow system. Simply unzip and double-click on index.html to start
    viewing.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;