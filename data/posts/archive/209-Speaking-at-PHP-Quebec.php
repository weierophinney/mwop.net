<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('209-Speaking-at-PHP-Quebec');
$entry->setTitle('Speaking at PHP Quebec');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1236081582);
$entry->setUpdated(1236089399);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I'm speaking at <a href="http://conf.phpquebec.org/">PHP Quebec</a> this
    week. While I live a scant 1.5 hours away from the venue, this is the first
    I've been to the conference. The snow gods have declared their wrath
    already, but I plan to thwart them and drive up to Montreal this evening
    regardless.
</p>

<p>
    I'm giving two talks and participating in a panel discussion this week. The
    first talk is entitled "Practical Zend Framework Jutsu with Dojo," and,
    while it may look like a familiar topic of mine by this point, I've spent
    the last several days reworking the talk entirely, and am very much looking
    forward to presenting it tomorrow. (My copy of "Presentation Zen" could not
    have come soon enough! and that's all I'll say about that.)
</p>

<p>
    On Friday, PHP Quebec has introduced a "Framework Bootcamp" track, including
    sessions by myself, 
    <a href="http://fabien.potencier.org/">Fabien Potencier</a> of 
    <a href="http://symfony-project.org/">symfony</a>, and 
    <a href="http://derickrethans.nl/">Derick Rethans</a>
    representing <a href="http://ezcomponents.org/">eZ Components</a>. My talk
    that day is entitled "Zend Framework Little Known Gems." While hardly a
    completely original talk (Aaron Wormus did a "Hidden Gems" series of posts
    for <a href="http://devzone.zend.com/">DevZone</a> a couple years back, and
    Zend's own <a href="http://prematureoptimization.org/blog/">Shahar Evron</a>
    did a similar talk at last fall's IPC), this will be my first time doing a
    Zend Framework talk on something other than the MVC stack (or how to use
    components with the MVC stack). Leaving my comfort zone, so to speak.
</p>

<p>
    Towards the end of the day, Fabien, Derick, and myself will be corralled
    onstage together for a "Framework Comparison".
</p>

<p>
    If you're headed up to PHP Quebec this week, I look forward to meeting you
    or seeing you again!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;