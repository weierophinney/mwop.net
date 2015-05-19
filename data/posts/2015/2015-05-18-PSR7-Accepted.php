<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-05-18-psr-7-accepted');
$entry->setTitle('PSR-7 Accepted!');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-05-18 22:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-05-18 22:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'http',
  'php',
  'programming',
));

$body =<<<'EOT'
<p>
  I'm pleased to announce that as of 22:00 CDT on 18 May 2015, <a
    href="http://www.php-fig.org/psr/psr-7">http://www.php-fig.org/psr/psr-7</a>
  PSR-7 (HTTP Message Interfaces) has been accepted!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>The road to PSR-7</h2>

<p>
  The road to PSR-7 was a long and winding one. It started in summer of 2012 as
  a draft proposal on HTTP clients by <a href="http://www.whitewashing.de/">Benjamin Eberlei</a>,
  during which others proposed that perhaps a smaller standard on the HTTP message
  interfaces themselves &mdash; which would also allow targeting server-side
  applications, as those rely on the messages.
</p>

<p>
  At that point, <a href="https://github.com/thewilkybarkid">Chris Wilkinson</a> picked up the
  ball and created the formal draft that became PSR-7. In it, he developed the original
  HTTP message interfaces. During his tenure as editor, he also started drafting a related
  proposal on URIs, but this was never picked up as a PSR. PSR-7 was polished significantly
  during his tenure, particularly in the aspect of keeping headers as part of the message,
  and having a <code>MessageInterface</code> describing the commonalities between the two
  message types, with differentiators being in the request and response descriptions.
</p>

<p>
  At a certain point (I'm not sure when, as I've still not found any formal announcement in the
  group archives), the baton was handed over to <a href="http://mtdowling.com/">Michael Dowling</a>.
  His big addition to the proposal was modeling the message body as a stream. This has been fairly
  controversial from its introduction, and prompted <a
  href="http://mtdowling.com/blog/2014/07/03/a-case-for-higher-level-php-streams/">a
  lengthy blogpost</a> in summer 2014 detailing the why. I feel this was a stellar decision. Every language I've
  surveyed that has first-class HTTP abstractions has modeled the message bodies as streams:
  doing so allows for async operations in many languages (though not natively in PHP for a bit
  longer, alas), but also ensures that large messages do not eat your available memory. PHP
  itself models message bodies as streams (<code>php://input</code> and the output buffer are
  examples), so modeling them this way is a natural fit.
</p>

<p>
  Shortly after that blog post was written, I was playing heavily with <a
    href="https://nodejs.org">node.js</a>, and was immediately struck by how uniform various modules
  were in terms of handling HTTP. This was in large part due to Node having a built-in, well
  designed (mostly!) HTTP abstraction. The other part was a side effect of that: middleware
  essentially arises naturally in the language due to that abstraction, meaning that middleware
  is abundant and works pretty much anywhere you're writing web-facing applications.
</p>

<p>
  I decided to port <a href="https://github.com/senchalabs/connect">Connect</a>, the middleware
  library that gave rise to <a href="http://expressjs.com/">Express</a>, the arguably dominant
  node web framework, to PHP. In doing so, I had one huge hurdle to jump
  immediately: HTTP abstraction.
</p>

<p>
  Sure, every framework has an HTTP abstraction. I even contribute to one in Zend Framework. But
  my thought process was: I want to expose as many PHP developers to these concepts as possible,
  but my choice of HTTP abstraction might end up raising tribal flags. And then I remembered
  seeing Michael's post on PSR-7 streams and thought, "maybe I should target PSR-7!"
</p>

<p>
  The problem was there was no implementation yet.
</p>

<p>
  So, I put together an implementation over a weekend, and went to the list with it. About
  two days after Michael posted saying he was going to abandon PSR-7 due to time constraints.
</p>

<p>
  After a few weeks of discussion and heavy thinking, I decided to pick it up and try to move
  it forward. <a href="https://philsturgeon.uk/">Phil Sturgeon</a> and <a
  href="https://beau.io">Beau Simensen</a> agreed to continue as sponsor and
  coordinator, respectively, and so my tenure as editor began.
</p>

<p>
  This has been an interesting journey for me. When I started, there were still debates about
  using streams; I had to quickly ramp up on the spec and Michael's arguments, and see if I 
  agreed with them (I did), and, better, if I could defend them (I could).
</p>

<p>
  I discovered there was another aspect I felt needed to be addressed though: the messages
  worked great for client-side aspects, but fell short for server-side: users were left
  to parse the URI for query string arguments, and to parse the body manually, and headers
  for cookies, and... well, this is where the "mostly" came in with Node: you end up having
  to do a lot of stuff yourself, or rely on a microframework for that stuff. I felt we could
  do better. Thus, the <code>ServerRequestInterface</code> was born, providing access to
  the data we take for granted in PHP's superglobals, but also providing some necessary
  abstraction for populating that data, as PHP sometimes does a poor job of it (e.g., you
  do not get a populated <code>$_POST</code> for non-POST requests or for POST requests
  without specific media types).
</p>

<p>
  December 2014 came and went, and Phil resigned from FIG, leaving me in need of an
  additional sponsor. <a href="http://paul-m-jones.com">Paul M Jones</a> graciously stepped up.
</p>

<p>
  The server request additions had a fair bit of back and forth, but gained consensus in the end. Except that
  a certain amount of feedback concerned the fact that these were value objects, but mutable.
  So, in January, I took a deep dive into understanding value objects and immutability, and
  applied the concepts to the specification. We ended up with something that's very robust,
  without side effects, and which eliminates whole categories of potential problems due to
  changing state. This, too, was quite controversial, until folks saw actual real-world
  examples.
</p>

<p>
  I also introduced a <code>UriInterface</code> in order to abstract the URI components.
  I and others discovered that we often needed to parse the URI to get at things like the
  scheme, path, host, etc., and that this was tedious, repetitious, and sometimes error-prone.
  Introducing a URI abstraction solves this. I tried to do so in such a way that we can be
  forward-compatible with a later, formal URI proposal, and borrowed heavily from work Chris
  Wilkinson had done earlier.
</p>

<p>
  At this point, we decided to put it up for a vote. Initial results were quite positive, and
  it looked like we had a shoo-in. Except sometime during the second week, we got a lot of
  people reviewing the proposal for the first time. There's nothing like imminent acceptance
  to raise the interest of developers, and a number of flaws were found. Key among them were
  the fact that we weren't doing a great job of detailing the relationship between the URI
  and the Host header, nor were file uploads being handled particularly well.
</p>

<p>
  So, we cancelled the vote around 24 hours before acceptance, and went back to draft stage.
  <a href="http://webmozarts.com/">Bernard Schussek</a> provided some great 
  justifications for an abstraction around file uploads which we ended up 
  incorporating, and we ironed out the URI/host relationships in a much simpler 
  fashion than we had previously.
</p>

<p>
  And that took us to a second vote, which puts us where we are today: with a new PSR now accepted!
</p>

<h2>Thanks</h2>

<p>
  Many people helped contribute to this proposal. While I may be the editor and largely the public
  face for it at this point, it was the result of years of work before I even stepped in, and many
  folks contributed after I did. In particular, I want to thank:
</p>

<ul>
  <li><a href="http://wwww.garfieldtech.com/">Larry Garfield</a>, who tested
    PSR-7 out on some sample projects, which enabled me to get concrete feedback from a usage perspective.</li>
  <li><a href="http://evertpot.com">Evert Pot</a>, who ultimately voted against the proposal, but who
    provided fantastic feedback and discussion throughout the lifetime of it. His help was invaluable.</li>
  <li>Phil, Beau, and Paul for enduring my rants, frustrations, and self-doubts the last few months!</li>
</ul>

EOT;
$entry->setExtended($extended);

return $entry;
