<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('221-Blog-Backlog');
$entry->setTitle('Blog Backlog');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1250797624);
$entry->setUpdated(1251234746);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Several people have pointed out to me recently 
    that I haven't blogged since early May, prior to attending
    <a href="http://tek.mtacon.com/">php|tek</a>. Since then, I've built up a
    huge backlog of blog entries, but had zero time to write any of them.
</p>

<p>
    The backlog and lack of time has an easy explanation: my change of roles
    from Architect to Project Lead on the <a
        href="http://framework.zend.com/">Zend Framework</a> team. While the
    change is a welcome one, it's also been much more demanding on my time than
    I could have possibly envisioned. Out of the gate, I had to finish up the
    1.8 release, and move immediately into planning and execution of the
    1.9 release -- while learning the ropes of my new position, and
    continuing some of my previous development duties. Add a couple of
    conferences (php|tek and <a href="http://phpconference.nl/">DPC</a>) into
    the mix, and you can begin to see the issues.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    At the time I write this, ZF currently stands at version 1.9.1, with 1.9.2
    just around the corner. A few unsung bits about the 1.9 series:
</p>

<ul>
    <li>I updated the coding standards slightly to include naming conventions
    for abstract classes and interfaces</li>
    <li>I finally added in documentation standards (at the prompting of our two
    most active documentation translators).</li>
    <li>The test suite no longer uses output buffering, which means you can see
    test status in realtime, and it no longer segfaults after using all
    available RAM.</li>
</ul>

<p>
    I'm currently in planning mode, and hope to start spinning out some articles
    and tutorials in the coming weeks (I posted one today), as well as finally
    posting a roadmap for ZF 2.0 (hint: there will be at least a 1.10 first).
    I've been playing a bit with document-based databases such as CouchDB, as
    well as with Dependency Injection, Doctrine, and pub-sub architectures. I
    hope to blog about some of my experiments in the coming weeks.
</p>

<p>
    This autumn, I'll be speaking at two separate conferences. I'll be joining
    php|a's <a href="http://codeworks.mtacon.com/">CodeWorks</a> for the East
    Coast tour, starting in Atlanta, and moving on through Miami, Washington,
    D.C., and New York City. A few weeks later, I'll be at <a
        href="http://zendcon.com/">ZendCon</a>, giving back-to-back tutorials on
    Zend Framework, and a regular session on domain models in MVC frameworks.
</p>

<p>
    If you don't hear from me, and need to contact me, you can find me on
    twitter, freenode under my registered nick (if you don't know it, you
    shouldn't be contacting me), or the various framework mailing lists. If I'll
    be in your area during the autumn conference season, please look me up!
</p>
EOT;
$entry->setExtended($extended);

return $entry;