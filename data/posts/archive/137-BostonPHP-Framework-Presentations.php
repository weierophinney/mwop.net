<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('137-BostonPHP-Framework-Presentations');
$entry->setTitle('BostonPHP Framework Presentations');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1173156755);
$entry->setUpdated(1173238759);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    Last Wednesday evening, I had the honor of presenting Zend Framework to <a
        href="http://www.bostonphp.org/">BostonPHP</a>, as part of an ongoing
    series they're holding on PHP frameworks; Horde was also represented as part
    of the evening's fare. It was the first time I've attended a UG, so I got
    the double whammy of that and being a presenter. Oh, make it a triple whammy
    -- Boston is a 3+ hour drive from the Burlington, VT area I now call home.
</p>
<p>
    All in all, the presentations went quite well. It was particularly fun to
    see what my friend <a href="http://hagenbu.ch/">Chuck Hagenbuch</a>
    has been up to with <a href="http://horde.org/">Horde</a>; his description
    and demonstration of RDO, or "Rampage Data Objects" was very cool (I really
    like the idea of "Horde on the Rampage" -- it's a very funny image for me),
    as was his <em>working</em> demonstration of using WebDAV to pull results
    via PHP in the Mac Finder.
</p>
<p>
    A lot of people are interested in and working regularly with Zend Framework,
    at least based on the questions I was receiving. Attendees ranged from the
    "what does Zend do" category to the "We're standardizing on Zend Framework
    and use Zend products throughout our stack" category. The bulk of the
    comments I received were either of the flavor "I really like what I'm
    seeing" or wondering how mature/stable Zend_Db is. Unfortunately, at the
    time I was preparing the slides, there were many features in Zend_Db that can cause
    headaches, and I took some time to point these out; however most of these
    are soon to be history, due to the work of Bill Karwin and Simon Mundy, who
    are pushing to get a stable, usable DB abstraction layer out the door for ZF
    1.0. 
</p>
<p>
    During the joint question and answer session, I started getting some
    particularly tough, pointed questions from one member of the group. I wasn't
    getting particularly rattled, but the moderator, Seth, decided to intervene
    and introduce me to my interlocutor -- none other than Nate Abele of the
    CakePHP project. In the end, he joined Chuck and myself at the front of the
    room, and we had a nice panel discussing how the various frameworks handle
    different issues.
</p>
<p>
    If you're ever in the Boston area, check to see if BostonPHP is having a
    meeting; it's a fun group.
</p>
<p>
    <a href="/uploads/2007-02-28-FrameworkPresentation.pdf" title="2007-02-28-FrameworkPresentation.pdf" target="_blank">My slides are now available</a>;
    I've revised them slightly to fix some
    syntactical errors I noticed during the presentation, but otherwise they're
    what I presented. You may also want to check out the 
    <a href="http://www.bostonphp.org/images/mp3/bostonphp_2_28_07.mp3" target="_blank">podcast</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;