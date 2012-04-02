<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('225-Why-UnCons-are-Important');
$entry->setTitle('Why UnCons are Important');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1254920105);
$entry->setUpdated(1254927187);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'cw09',
  3 => 'zendcon',
  4 => 'zendcon09',
));

$body =<<<'EOT'
<p>
    My good friend, <a href="http://caseysoftware.com/blog/">Keith Casey</a>, is
    once again chairing <a href="http://zendcon.com/">Zendcon's</a> <a
        href="http://joind.in/event/zendcon09-uncon">UnCon</a>. For those who
    have never attended, it's basically one or more tracks running parallel to
    the main conference, but with content pitched by attendees -- sometimes
    presented by them, other times presented by others who are knowledgeable in
    the field.
</p>

<p>
    Why should you care? There are great sessions already selected for the
    conference featuring some well-known speakers from the PHP world; why would
    you want to either attend or present at the uncon?
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Continue the Discussion</h2>

<p>
    Last year, I did a tutorial session with <a
        href="http://mikenaberezny.com">Mike Naberezny</a> covering PHP
    development best practices. Following the session, several attendees
    approached Keith and said they could really use a session just on <a
        href="http://subversion.tigris.org/">Subversion</a>; the material
    covered in the tutorial, while good, did not go into enough depth for them. 
</p>

<p>
    Keith then approached me about doing an uncon session, and I in turn tagged
    <a href="http://www.lornajane.net">Lorna Jane Mitchell</a> about doing the
    session. We ended up doing it together, after sitting down for about 10
    minutes of planning. We had the flexibility to both go over what we thought
    were core basics everyone should know, as well as to answer very specific
    questions. The session was very well attended, and those people who gave us
    feedback indicated that it was exactly the amount of detail they were
    looking for.
</p>

<p>
    So, in summary, the uncon allowed attendees to get more information on a
    topic that was covered only briefly in another, regular session.
</p>

<h2>Springboard to Speaking</h2>

<p>
    Was your talk rejected for the conference? Are you having trouble getting
    accepted to any conferences at all?
</p>

<p>
    Conference organizers have a catch-22 they face every time they put together
    a schedule. On the one hand, there may be some really interesting talks
    submitted by unknown speakers; on the other, scheduling known speakers helps
    put money on the table (attendees want to hear from established experts). As
    a result, you see a lot of the same speakers at each and every conference.
</p>

<p>
    So, how do <em>you</em> break in? You speak.
</p>

<p>
    Speaking at area user groups is one way to break into the system; good
    sessions often generate buzz that extends beyond your user group. But an
    even better way is to speak at an uncon session at an established
    conference. Oftentimes you'll have conference organizers attending these, or
    friends of conference organizers, and this can have a huge impact on your
    chances at speaking. Additionally, I've seen a ton of buzz generated on
    twitter and blogs by uncon sessions -- and this buzz gets noticed.
</p>

<p>
    Don't believe me? Let's revisit that talk Lorna Jane and I gave. We pitched
    it as a tutorial session for <a href="http://tek.mtacon.com/">php|tek</a>
    this spring... and it was accepted, largely on the basis of our uncon
    session. It was the only talk I pitched for that conference that was
    accepted. (Believe it or not, I have to submit talks just like everyone
    else, and get a fair share of rejections just like everyone else.)
</p>

<p>
    At php|tek, I also pitched two uncon tracks, one on using Git with SVN, and
    another on how to write domain models for your MVC layers. This latter
    session, on models, generated a lot of buzz, and was later picked up by MTA
    for a <a href="http://codeworks.mtacon.com/">CodeWorks 2009</a> webinar,
    which was very well received. I also pitched it for ZendCon this year... and
    will be presenting it there in two weeks.
</p>

<p>
    In short, if you want to speak at conferences, start by pitching ideas to
    the uncon tracks at conferences you attend. Prepare well for it, make a
    good impression, and you may be delivering it as a regular session at
    another conference.
</p>

<h2>Explore new ideas</h2>

<p>
    Conference organizers, besides having to choose well-known speakers, often
    also need to stick to known topics. Part of the reason you see topics on the
    buzz words du jour is because people want to see sessions on them. But what
    about things like PHP-GTK? or using PHP to write CLI tools? or using PHP to
    connect to a specific web service? These may all be interesting, but may not
    attract crowds. But what if <em>you</em>, as an attendee, want to hear about
    these topics?
</p>

<p>
    One aspect of the uncon is that you can vote on topics and/or suggest topics
    you want to hear about. This gives you a chance to help shape the direction
    of the conference to cater to your own interests. It also allows you to
    explore some areas of the language you may not have known about, but, when
    you see the presentation abstract, could benefit the work you do.
</p>

<p>
    So, use the uncon to explore the language!
</p>

<h2>Vote now!</h2>

<p>
    If you're going to ZendCon, plan on speaking at or attending the uncon!
    And help shape it, by heading over to Joind.in and <a
        href="http://joind.in/event/zendcon09-uncon">voting for sessions</a>
    now! See you there!
</p>
EOT;
$entry->setExtended($extended);

return $entry;