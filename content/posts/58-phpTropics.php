<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('58-phpTropics');
$entry->setTitle('php|Tropics');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1111378426);
$entry->setUpdated(1111378439);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    Well, it's official: My IT Manager convinced those in the upper echelons
    (well, considering it's a non-profit with only around 20 employees, that
    meant the president and the CFO) that (1) he and I need to attend a PHP
    conference, (2) due to the amount of work we've been putting in to bring
    money into the organization, cost shouldn't be <em>too</em> much of a
    deciding factor, and (3) <a href="http://www.phparch.com/tropics"
        target="_blank">php|Tropics</a> isn't <em>too</em> expensive, especially
    considering the sessions involved cover some of the very issues we've been
    struggling with the past few months (PHP/MySQL/Apache and clusters, PHP5
    OOP, PHP Security, test-driven development, Smarty, and more). 
</p>
<p>
    So, we're going to Cancun in May!
</p>
<p>
    This is incredibly exciting! I've never been to Mexico, nor even a resort,
    so I'll finally get to find out what my wife and friends have been talking
    about all these years. Plus, the conference is top-notch -- many of the
    presenters are well-known in the PHP community, and have blogs I've been
    following for the past year. (I only wish that Chris Shiflett's PHP Security
    series wasn't running head-to-head with the PHP5 OOP Extensions and PHP 5
    Patterns sessions; I suspect Rob and I will have to do a divide-and-conquer
    that day.)
</p>
<p>
    Drop me a line if you'll be attending -- I'm looking forward to meeting
    other PHP junkies!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;