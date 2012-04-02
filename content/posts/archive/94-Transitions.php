<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('94-Transitions');
$entry->setTitle('Transitions');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1127278712);
$entry->setUpdated(1127329719);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'programming',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    Life is in transition for me now. Two weeks ago, we got to bring our
    handsome baby boy home, and I haven't been sleeping much since (though more
    than Jen). On top of the sleep deprivation, however, comes more exciting
    news: I've been hired as a PHP Developer by <a
        href="http://www.zend.com/">Zend Technologies</a>!
</p>
<p>
    I was approached by Daniel Kushner in late July regarding another position
    at Zend, and was flown out at the beginning of August. While I felt the
    interview went well, I harbored some doubts; work got fairly busy shortly
    thereafter, and then, of course, Liam was born, and the interview went
    completely out of my head. Until about three days after Liam's birthday,
    when Daniel contacted me again about the PHP Developer position.
</p>
<p>
    Work started yesterday, and I was flown to Zend's offices in Cupertino, CA,
    for orientation and to sit down with both Daniel and others to prepare for
    the projects on which I will be working. Thankfully, the job will not
    require that I move, and I will be working out of the 'home office' in
    Vermont when I return later this week.
</p>
<p>
    The decision to leave <a href="http://assoc.garden.org/about">NGA</a> was
    difficult, but the opportunity to work with Zend is just too good to miss. I
    am honored to be selected by them, and hope this is the beginning of many
    good things to come.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;