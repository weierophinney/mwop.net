<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('141-Transitions');
$entry->setTitle('Transitions');
$entry->setAuthor('matthew');
$entry->setDraft(true);
$entry->setPublic(true);
$entry->setCreated(1182977251);
$entry->setUpdated(1182977251);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Almost two years ago, I was contacted by <a href="http://www.zend.com/">Zend</a> 
    about a job opportunity, and mid-September of that year started working for
    Zend full-time in its new eBiz group, responsible for maintenance and
    development of its websites and web services.
</p>
<p>
    A little over a year ago, <a href="http://andigutmans.blogspot.com/">Andi</a> 
    <a href="http://weierophinney.net/matthew/archives/129-MVC-changes-in-Zend-Framework.html">asked me to assume the role of lead developer on the Zend Framework MVC components</a>,
    which kicked off my first major involvement in Zend Framework. Since then, I
    have been maintaining the MVC components and a handful of others (such as
    the server components, mail, and JSON) in addition to my day-to-day duties
    on the eBiz team.
</p>
<p>
    A few weeks ago, my supervisor, Boaz, approached me to tell me that Andi had
    requested for me to transfer full-time to the Zend Framework team. After
    some thought, I agreed to the move. I will stay on the ebiz team for the
    next couple of months to help finish out some projects, but will then
    transition to full-time involvement with the Zend Framework team. This will
    afford me more time to work on tutorials and articles covering Zend
    Framework, as well as more dedicated time to work on the components I own.
</p>
<p>
    Many thanks to all who have supported and encouraged me the past couple
    years, and particularly to those I've worked with on a day-to-day basis --
    you know who you are. Here's looking to a rosy future with Zend Framework!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;