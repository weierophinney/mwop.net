<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('127-ZendCon-Best-Practices-Session');
$entry->setTitle('ZendCon: Best Practices Session');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1160970027);
$entry->setUpdated(1160970027);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    As <a href="http://mikenaberezny.com/archives/53">Mike already noted,</a>
    he and I are presenting a session on "Best Practices of PHP Development" at
    <a href="http://www.zendcon.com/speakers_list.php#day1">this year's Zend Conference and Expo</a>.
    It has been my fortunate experience to work with Mike in the past, and, as
    he noted, we had so much fun presenting during last year's conference, we
    thought we'd do it again.
</p>
<p>
    The session is a pre-conference tutorial session, running for 3 hours on
    Monday morning, 30 October 2006.  Currently, we're shaping up the session
    into the following subject areas:
</p>
<ul>
    <li><b>Programming Practices</b>
        <ul>
            <li>Coding Standards</li>
            <li>Test Driven Development and Unit Testing</li>
            <li>Project Documentation</li>
        </ul>
    </li>
    <li><b>Tools and Processes</b>
        <ul>
            <li>Software Configuration Management (SCM)</li>
            <li>Collaboration tips and tools</li>
            <li>Deployment</li>
        </ul>
    </li>
</ul>
<p>
    Emphasis is going to be on working in teams, particularly those operating in
    geographically diverse areas.  With roughly 30 minute blocks per topic,
    we've certainly got plenty to cover!
</p>
<p>
    If you're coming to the conference, we look forward to seeing you in our
    session! 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;