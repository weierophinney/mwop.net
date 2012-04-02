<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('185-Speaking-at-phpworks');
$entry->setTitle('Speaking at php|works');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1220533200);
$entry->setUpdated(1220548220);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'phpworks08',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm pleased to announce I've been selected to speak at 
    <a href="http://phpworks.mtacon.com/c/index">php|works</a> in Atlanta this
    November.
</p>

<p style="text-align: center"><a href="http://phpworks.mtacon.com/"><img src="http://www.jansch.nl/wp-images/works_badge.jpg"></a></p>

<p>
    I'll be presenting my <a
        href="http://phpworks.mtacon.com/c/schedule/talk/d1s5/1">talk on Dojo
        and Zend Framework</a>, demonstrating how to quickly and easily create
    rich and dynamic UIs using the various integration points with Dojo functionality provided by Zend Framework.
</p>

<p>
    Looking forward to seeing you in Atlanta in November!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;