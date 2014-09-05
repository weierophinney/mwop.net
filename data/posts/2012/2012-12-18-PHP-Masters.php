<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-12-18-php-master-series');
$entry->setTitle('PHP Master Series on Day Camp For Developers');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-12-18 14:24', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-12-18 14:24', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'oop',
));

$body =<<<'EOT'
<p>
    <a href="http://blog.calevans.com">Cal Evans</a> has organized another 
    DayCamp4Developers event, this time entitled "<a 
    href="http://blog.calevans.com/2012/11/19/php-master-series-vol-1">PHP 
    Master Series, Volume 1</a>". I'm honored to be an invited speaker for this 
    first edition, where I'll be presenting my talk, "Designing Beautiful Software".
</p>

<p>
    Why would you want to participate? Well, for one, because you can interact directly
    with the various speakers during the presentations. Sure, you can likely find the slide
    decks elsewhere, or possibly even recordings. But if we all do our jobs right, we'll
    likely raise more questions than answers; if you attend, you'll get a chance to ask
    some of your questions immediately, <em>and we may even answer them!</em>
</p>

<p>
    On top of that, this is a fantastic lineup of speakers, and, frankly, not a lineup 
    I've ever participated in. In a typical conference, you'd likely see one or two of
    us, and be lucky if we weren't scheduled against each other; if you attend 
    this week, you'll get to see us all, back-to-back. 
</p>

<p>
    What else will you be doing this Friday, anyways, while <a 
    href="http://en.wikipedia.org/wiki/2012_phenomenon">you wait for the end of the 
    world?</a>
</p>

<p>
    So, do yourself a favor, and <a 
    href="http://phpmasterseriesv1.eventbrite.com/">register today</a>!
</p>

EOT;
$entry->setBody($body);

return $entry;

