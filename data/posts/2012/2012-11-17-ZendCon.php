<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-11-17-zendcon-beautiful-software');
$entry->setTitle('My ZendCon Beautiful Software Talk');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-11-17 07:53', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-11-17 07:53', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'oop',
));

$body =<<<'EOT'
<p>
    Once again, I spoke at <a href="http://www.zendcon.com/">ZendCon</a> 
    this year; in talking with <a href="http://twitter.com/chwenz">Christian Wenz</a>,
    we're pretty sure that the two of us and <a href="http://andigutmans.blogspot.com">Andi</a>
    are the only ones who have spoken at all eight events.
</p>

<p>
    Unusually for me, I did not speak on a Zend Framework topic, and had
    only one regular slot (I also co-presented a Design Patterns tutorial
    with my team). That slot, however, became one of my favorite talks I've
    delivered: "Designing Beautiful Software". I've given this talk a couple
    times before, but I completely rewrote it for this conference in order 
    to better convey my core message: beautiful software is maintainable
    and extensible; writing software is a craft.
</p>

<p>
    I discovered today that not only was it recorded, but it's been <a href="http://youtu.be/mQsQ6QZ4dGg">posted
    on YouTube</a>:
</p>

<iframe width="420" height="315" src="http://www.youtube.com/embed/mQsQ6QZ4dGg" frameborder="0" allowfullscreen></iframe>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I've also <a href="/slides/2012-10-25-BeautifulSoftware/BeautifulSoftware.html">posted the slides</a>.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
