<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('224-CodeWorks-2009-Begins');
$entry->setTitle('CodeWorks 2009 Begins');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1253639327);
$entry->setUpdated(1253645647);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'cw09',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Today is the kickoff for <a href="http://codeworks.mtacon.com/">CodeWorks
        2009</a>, a remarkable PHP road show hitting seven cities in 14 days.
    While I'm not joining the tour until Atlanta, I'm proud to be joining up at
    that stop and presenting a Zend Framework tutorial during the tour.
</p>

<p style="text-align: center;"><img src="/uploads/CW09_Speaker.png"
    alt="CodeWorks'09" height="200" width="150" /></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    When Marco first announced his intentions for the tour, frankly, my first
    thought was, "He <em>has</em> finally gone off the deep end," a sentiment
    that he was all too willing to consider himself. However, the more I thought
    about it, the more intriguing the idea was: bring some great speakers to a
    bunch of cities, and keep the admission costs down so that locals can
    actually afford to go. 
</p>

<p>
    My own considerations for going were many. I was initially reluctant --
    conferences are a lot of work, and while I travel fine, it always takes a
    while for me to recover on return. And the full two weeks was completely out
    for me -- I have a family I actually want to be able to return to, after
    all, and a job I like. (Yes, speaking is part of my job, but two weeks in a
    row, non-stop, might be a bit much for even the most understanding of
    employers.) Fortunately, for US speakers, Marco had an answer to that: the
    ability to participate in either the Western or Eastern halves of the tour,
    which would make the total time away much less, though still an insanely
    daunting idea.
</p>

<p>
    One facet of the project in particular kept me thinking, however. The
    opportunity to speak with PHP developers and ZF developers across the
    Eastern Seaboard is an opportunity I won't get often. And then there's the
    ability to say, "I did it" -- I never thought that'd be compelling, but the
    sheer audacity of the event just begs for participation.
</p>

<p>
    If you're anywhere within a few hours of one of the CodeWorks stops, and
    you've never attended a conference before, you owe it to yourself to hop in
    the car and go. The chance to meet other PHP developers, get training from
    some top-notch speakers, and improve your skills more than make up for any
    of the cost -- and you may never get an opportunity like this again (though
    I sincerely hope you will!).
</p>

<p>
    I look forward to meeting and speaking with you in Atlanta, Miami, D.C., and
    NYC next week!
</p>
EOT;
$entry->setExtended($extended);

return $entry;