<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('175-Speaking-at-the-Dutch-PHP-Conference');
$entry->setTitle('Speaking at the Dutch PHP Conference');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1211137713);
$entry->setUpdated(1211278237);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'conferences',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've known for some time, but was reluctant to blog about it until the plane
    tickets were purchased and in hand: I've been invited to speak at the 
    <a href="http://phpconference.nl/">Dutch PHP Conference</a> this coming
    June:
</p>

<p style="text-align: center"><img src="http://phpconference.nl/img/dpc08/logoDPC08_small.png" alt="DPC" height="149" width="217" />
</p>

<p>
    I'll be presenting two separate sessions: 
    <a href="http://phpconference.nl/workshops/">an all day tutorial</a> 
    on 13 June 2008 covering Zend Framework, and a regular session on 14 June
    2008 covering 
    <a href="http://phpconference.nl/schedule/bestpractices">Best Practices for PHP development</a>, 
    which will focus on how to utilize Zend Framework coding standards and
    methodologies to help deliver efficient, high quality code for your
    organization.
</p>

<p>
    I'm looking forward to meeting old and new friends alike at the conference!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;