<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('157-Burlington-PHP-Tonight');
$entry->setTitle('Burlington PHP Tonight');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1201698584);
$entry->setUpdated(1201723981);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    The <a href="http://groups.google.com/group/Burlington-VT-PHP">Burlington
        PHP User Group</a> is having another meeting tonight at 5:30pm at Brown
    &amp; Jenkins Coffee Roasters. From the announcement:
</p>

<blockquote>
    Bradley Holt will be giving a presentation on developing a web application
    using Zend Framework. Bradley Holt is founder and web developer for 
    <a href="http://www.foundline.com/">Found Line</a>, a local design and
    development studio which has used Zend Framework in several recent projects.
    He also works as a software developer for a local non-profit. Before
    starting Found Line he worked as computer trainer teaching a variety of
    subjects including Java/JSP, ASP.NET, and PHP
</blockquote>

<p>
    Visit <a href="http://groups.google.com/group/Burlington-VT-PHP/web/meeting-2008-01-30">the meeting page</a> 
    for details on location and RSVPs. If you're in the Burlington, VT, area,
    we'd love to see you there!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;