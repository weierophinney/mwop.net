<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('33-B.-Gates-Open-Source-Programmer');
$entry->setTitle('B. Gates: Open Source Programmer?');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1080583336);
$entry->setUpdated(1095702768);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'linux',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    I just read <a href="http://www.salon.com/tech/col/rose/2004/03/19/programmers_at_work/">coverage 
        of a panel of programming luminaries on Salon;</a> the topic of
    discussion was about the state of programming. In the course of the
    discussion, the subject of Open Source came up. Several of the luminaries --
    which included architects of the Mac OS and Windows, as well as others --
    derided the community for basically reinventing the wheel, and wheels that
    need to be re-thought entirely anyways. One questioned, "Why is hte idealism
    just about how the code is shared -- what about idealism about the code
    itself?"
</p>
<p>
    Andy Hertzfeld (who helped develop the original Mac OS)  was sitting on the
    panel, and jumped in. He has been working with Eazel and Chandler in recent
    years, and thus has an inside view of open source. His initial comment:
    "It's because they want people to use the stuff!" Basically, they program
    Windows- or Mac-like interfaces because then people will be willing to try
    it out. They program office suites because people "need" an office suite to
    be productive. Such offerings hook them into the OSS movement.
</p>
<p>
    Another participant, Dan Bricklin (of VisiCalc, a pioneering spreadsheet
    program) shared an anecdote from Bill Gates. Evidently, Gates gave an
    interview (with Lammers -- look up this person) in which he explained that
    his work on MS's BASIC compiler was done by looking at how other programmers
    had accomplished the task. In his own words, "The best way to prepare is to
    write programs, and to study great programs that other people have written.
    In my case, I went to the garbage cans at the Computer Science Center and I
    fished out listings of their operating systems." 
</p>
<p>
    So basically, Gates was an early adopter of OSS methodologies... Interesting
    to see that today he's so protective of MS code. Guess money might do that
    to you.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;