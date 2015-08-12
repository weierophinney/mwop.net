<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('247-ZendCon-2010!');
$entry->setTitle('ZendCon 2010!');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1288197345);
$entry->setUpdated(1288471608);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
  3 => 'zendcon10',
));

$body =<<<'EOT'
<p>
    As I write this, <a href="http://www.zendcon.com/">ZendCon</a> begins in less than a week. I have the honor and pleasure to be speaking there again, for the sixth year running.
</p>

<p style="text-align:center;"><a href="http://www.zendcon.com/"><img alt="ZendCon 2010" src="http://weierophinney.net/uploads/zendcon_2010_speaker.jpg" /></a></p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
I'll be speaking twice:
</p>

<ul>
    <li><em>Tuesday, 2 November 2010, 10am:</em> <strong>"Documents, Documents,
    Documents"</strong><br />
        Relational databases and web development go hand-in-hand -- yet many web
        apps are decidely unsuited to relational storage. A new crop of
        databases has risen in recent years to solve these problems: document
        databases. Learn what types of problems document databases solve, learn
        what options exist for you, and discover some approaches to common web
        problems using these new technologies.
    </li>
    <li><em>Wednesday, 3 November 2010, 5:15pm:</em> <strong>"Introducing Zend Framework 2.0"</strong><br />
        No, I won't be announcing a general access release of ZF2! Let's stop
        that rumor right off. Instead, I'll be discussing some history of Zend
        Framework, what lessons we've learned, and the general thrust of ZF2
        development. Additionally, I'll cover the current state of development,
        and how <em>you</em> can get involved.
    </li>
</ul>

<p>
    I'm also on the closing panel:
</p>

<ul>
    <li><em>Thursday, 4 November 2010, 11:45am:</em> <strong>"The ROI of Community Involvement"</strong><br />
        Join 5 high-profile members of the PHP community who represent various interests from projects to large enterprises as we discuss the problems and paybacks of getting involved in the community. Many large companies have roadblocks that keep them from participating in the PHP community.  Many small business don't feel they have the time or people to get involved. In both cases, not seeing the potential ROI of getting developers involved in the community is holding companies back. 
    </li>
</ul>

<p>
    Finally, I'm organizing a <strong>Zend Framework roundtable</strong> to
    occur as an <a href="http://zendcon.com/uncon/">UnCon</a> session, and
    tentatively scheduling this for <em>Wednesday morning, at 11am</em>. 
</p>
    
<p>
    <em><strong>If you are a ZF contributor, and in the Bay Area that day, but
            not attending ZendCon, please contact me if you want to attend the
            roundtable and/or ZF2 talk; I can make that happen.</strong></em>
</p>

<p>
    Will you be at <a href="http://www.zendcon.com/">ZendCon</a> next week? If
    so, look me up -- I'd love to meet you!
</p>
EOT;
$entry->setExtended($extended);

return $entry;