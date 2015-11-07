<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('87-On-Safari');
$entry->setTitle('On Safari');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1122174153);
$entry->setUpdated(1122175019);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'family',
));

$body =<<<'EOT'
<p>
    This past year, Maeve had a month-long unit on Safaris and jungle animals at
    daycare. One day, she came home singing:
</p>
<blockquote><i>
        On Safari, on Safari,<br />
        In a jeep, in a jeep,<br />
        I can see a lion, I will take a picture,<br />
        Click, click, click. Click, click, click.<br />
        <br />
        On Safari, on Safari,<br />
        In a jeep, in a jeep,<br />
        I can see a tiger, I will take a picture,<br />
        Click, click, click. Click, click, click.
</i></blockquote>
<p>
    And so on.
</p>
<p>
    There's a place just over the Quebec border called <a
    href="http://www.parcsafari.com">Parc Safari</a> that we've been hearing
    about, and today we went up there on a family outing -- one of the last that
    we'll have with just the three of us (before little Liam is born).
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<blockquote><i>
        On Safari, on Safari,<br />
        In our Matrix, in our Matrix,<br />
        I can see a zebra, I will take a picture,<br />
        Click, click, click. Click, click, click.<br />
</i></blockquote>
<p>
    Needless to say, the excursion was a hit for the whole family. The park has
    a drive-through safari adventure where you drive your car through a small
    preserve, looking at animals and even getting a chance to feed some of them.
    We saw zebras, elephants, rhinos, ostriches, water buffalo, ibexes, camels,
    giraffes, emu, oryx, reindeer, caribou, elk, bison, yaks, wildebeests... and
    many more that I'm either forgetting or didn't know the names of. 
</p>
<p>
    The highlight of the safari was probably towards the end, when we each of us
    got chances to feed bison from the car -- try <em>that</em> in Yellowstone!
    The bison were surprisingly gentle, and typically tried to take the food
    nuggets from your hand with their lips, but the greedier ones used their
    tongues and tended to exude tremendous amounts of slobber in the process.
</p>
<p>
    (My personal favorite, though, was getting the chance to stroke a camel's
    whiskers. I don't know why, but it was just one of those great moments.)
</p>
<p>
    Parc Safari also has a small amusement park, with very cheap prices ($2
    Canadian per person for all day access to all rides), so Maeve got a chance
    to ride to her hearts delight. She and I went on the Ferris wheel together,
    and had an excellent time waving at Jen, who awaited us below.
</p>
<p>
    If we had any doubts about Maeve enjoying it, they were allayed when,
    shortly after crossing the US border, we looked back in time to see Maeve
    pass out from sheer exhaustion. What a sweet kid... the chocolate from the
    ice cream was still smudged on her cheek, and her hand was gripping her
    memento rock.
</p>
EOT;
$entry->setExtended($extended);

return $entry;