<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('150-burlington-php-users-group-december');
$entry->setTitle('Burlington PHP Users Group, December');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1196610748);
$entry->setUpdated(1196684904);
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
    I've been considering for a good six months trying to organize a PHP Users
    Group for the Burlington, VT, area. When we first moved to Vermont, I was
    surprised (and excited) by the number of PHP shops (which at the time I saw
    as job potential, as I was looking for work), and actually walked my resume
    around to a half-dozen or so. The area has a ton of PHP developers, and it
    only makes sense to have a UG where we can exchange tips and tricks of the
    trade.
</p>

<p>
    Then, about six weeks ago, I mentioned this to my friend Rob. He did what I
    should have done all along, and googled for an existing group -- and found
    one!
</p>

<p>
    The <a href="http://groups.google.com/group/Burlington-VT-PHP">Burlington, VT PHP Users Group</a>
    has been around since November of 2005 on Google Groups, but we're having
    our inaugural meeting this coming Wednesday, 5 December 2007. I'll be
    speaking at this first meeting on Zend Framework's MVC components
</p>

<p>
    If you're in the Burlington area this Wednesday, you should stop by. For
    more details, 
    <a href="http://groups.google.com/group/Burlington-VT-PHP/web/meeting-2007-12-05">visit the event page</a>, 
    and don't forget to RSVP.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;
