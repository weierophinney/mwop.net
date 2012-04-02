<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('96-Zend-Conference');
$entry->setTitle('Zend Conference');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1129230540);
$entry->setUpdated(1129232493);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Around the time I was hired by Zend, I was asked, along with 
    <a href="http://www.6502.org/users/mike/blog/">Mike Naberezny</a>, to fill
    in for a tutorial session entitled 'Setting up PHP' for the 
    <a href="http://zend.kbconferences.com/">upcoming Zend/PHP Conference and Expo</a>. 
    The basic premise of the session is to give a step-by-step tutorial on how
    to setup and configure PHP for various scenarios, such as development,
    testing, and production.
</p>
<p>
    Mike and I have been working in parallel developing ideas and outlines for
    the session, and I'm fairly excited to have the opportunity. However, if
    you're attending the conference and, in particular, this session, I'd love
    to hear any input you might have -- any tricks you'd love to learn,
    configuration settings you don't understand, use cases you might need. Leave
    a comment!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;