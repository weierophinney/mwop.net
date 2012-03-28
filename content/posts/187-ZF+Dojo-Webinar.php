<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('187-ZF+Dojo-Webinar');
$entry->setTitle('ZF+Dojo Webinar');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1220040786);
$entry->setUpdated(1220533464);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'webinar',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm giving a <a href="http://www.zend.com/en/company/news/event/webinar-zend-framework-and-dojo-integration">webinar on Zend Framework and Dojo Integration</a> 
    this coming Wednesday, 3 Sept 2008. 
</p>

<p>
    I'm particularly excited about this webinar, as I've been developing a
    sample pastebin application to show off a number of features; the webinar
    will feature some screencasts showing the new code in action, and promises
    to be much more dynamic than my typical "bullet point and code"
    presentations.
</p>

<p>
    I'm also going to show some techniques to use when developing with ZF+Dojo,
    including how to create custom builds once you're ready to deploy your
    application (and why you want to do so).
</p>

<p>
    <a href="https://zend.webex.com/zend/onstage/g.php?t=a&d=572843054">Register
    today</a>!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;