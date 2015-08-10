<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('219-Speaking-at-phptek');
$entry->setTitle('Speaking at php|tek');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1242228825);
$entry->setUpdated(1242228825);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'tek09',
));

$body =<<<'EOT'
<p>
    I announced this earlier in the year, but for those that missed it, I'm
    speaking at <a href="http://tek.mtacon.com/">php|tek</a> next week.
</p>

<p style="text-align: center">
    <img src="/uploads/tek_09_badge_speaker.gif" 
        alt="Speaking at php|tek 2009" />
</p>

<p>
    I'll be co-presenting a workshop entitled <a
        href="http://tek.mtacon.com/c/schedule/talk/ts2/0">Practical SVN for PHP
        Developers</a> along with the lovely and talented 
    <a href="http://www.lornajane.net/">Lorna Jane Mitchell</a>. In a way, it's
    a continuation of the unconference session we did together at ZendCon08, and
    will provide much more in-depth information on the subject -- including how
    to create and organize your repositories, branching and tagging strategies,
    how and when to commit, as well as more basic usage of subversion for
    day-to-day use.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I've also proposed two unconference sessions:
</p>

<ul>
    <li><a href="http://joind.in/talk/view/437">Using Git to take SVN
        offline</a> continues the version control theme. In it, I plan to
    demonstrate and discuss <kbd>git+svn</kbd>, a set of tools I use daily to
    allow me the benefits of a distributed versioning system backed by a
    non-distributed, canonical system.</li>

    <li><a href="http://joind.in/talk/view/436">Play-dough: How to Model Your
        PHP Objects</a> is a continuation of a series of blog posts I did
    earlier this year on creating models for your MVC systems. I plan to look at
    some common design patterns, including Service Layers, Domain Models, Data
    Mappers, Transaction Scripts, ActiveRecord, and Table and Row Data Gateways
    -- and where and when each may make sense in your application.</li>
</ul>

<p>
    If you're interested in either of the above two topics, follow the links and
    vote. Or, if you just want to see me run around like a chicken with its head
    cut off for two days as I scramble to put materials together, that works as
    well.
</p>

<p>
    If you're going to php|tek next week, please look me up. And if you're not,
    it's not too late <a href="http://tek.mtacon.com/c/signup">to register</a>!
</p>
EOT;
$entry->setExtended($extended);

return $entry;