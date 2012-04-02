<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('216-Speaking-at-DPC-again!');
$entry->setTitle('Speaking at DPC (again!)');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1239337519);
$entry->setUpdated(1239362237);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'dpc09',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm thrilled to once again be speaking at the 
    <a href="http://phpconference.nl">Dutch PHP Conference</a>.
</p>

<p style="text-align: center">
    <a href="http://phpconference.nl"><img src="http://dpc.09.s3.amazonaws.com/dpc09_speaker.jpg" /></a>
</p>

<p>
    Like last year, I'm giving two sessions; unlike last year, these are going
    to be more advanced. I noticed last year both in terms of audience
    participation as well as in speaking with attendees that I'd be able to step
    it up a notch were I to return.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    My Zend Framework Workshop will therefor focus on some more advanced
    material -- and in particular the various plugin systems we offer and how
    to utilize them to make your life easier. Additionally, I'll likely
    incorporate much of my the material from my various Zend_Form posts to give
    some more advanced modelling and rendering material than I have in the past.
</p>

<p>
    I'm also giving a "regular" session, entitled simply, "Contribute!" In this
    session, I'll show how <em>you</em> can be a good open source netizen, and
    provide some tools, tips, and tricks for contributing to your favorite open
    source projects.
</p>

<p>
    So, plan on attending -- if not for me, for one of the other fantastic
    speakers who will be at the event (Andrei Zmievski, Sebastian Bergmann,
    Derick Rethans, Rob Allen, Paul Reinheimer, and many, many more). Convince
    your boss to book your tickets before 30 April 2009 to save her some money
    (100 on Full Ticket and 55 on the Tutorial only ticket). Just get there!
</p>
EOT;
$entry->setExtended($extended);

return $entry;