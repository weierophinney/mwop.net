<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('196-Tidings-of-the-Season');
$entry->setTitle('Tidings of the Season');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1228273633);
$entry->setUpdated(1228607296);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  4 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Just about every day, I have an idea for a blog post, and most days, by the
    end of the day, I just don't have the time or energy to actually write
    anything up. The inner writer in me screams, "no excuses!" while the aging
    adult in me whispers, "time for bed, dear."
</p>

<p>
    So, to keep my hand in the game, here are a few things running through my
    head, or that I'm working on, or that I'll be doing soon.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Speaking at PHP Quebec</h3>
<p style="text-align: center;">
    <img src="http://conf.phpquebec.org/img/icone/2009/2009_150x100_speakat_blue_en.gif" alt="PHP Quebec 2009" height="100" width="150" />
</p>

<p>
    I'm speaking at PHP Quebec this coming spring, and presenting "Practical
    Zend Framework Jutsu with Dojo". I will be reworking the talk I did at
    php|works to present new features and new techniques I've been working on
    for effectively using Dojo and Zend Framework together. Hopefully my laptop
    and the projector will play nice together this time!
</p>

<h3>PHP Advent Calendar</h3>
<p>
    <a href="http://shiflett.org/">Chris Shiflett</a> and 
    <a href="http://seancoates.com">Sean Coates</a> have generously donated some
    time and a domain to this year's 
    <a href="http://phpadvent.org/2008">PHP Advent Calendar</a>. I was invited
    to submit an entry, and wrote up a piece I titled 
    "<a href="http://phpadvent.org/2008/use-responsibly-by-matthew-weier-ophinney">Use
    Responsibly</a>," where I discuss good development habits when consuming
    open source projects.
</p>

<h3>Burlington, VT PHP User's Group</h3>
<p>
    This week marks the one year anniversary of regular meetings of the <a href="http://groups.google.com/group/Burlington-VT-PHP">Burlington, VT PHP User's Group</a>. 
    We <a href="http://groups.google.com/group/Burlington-VT-PHP/web/meeting-2008-12-04">meet this week</a> for a special presentation from 
    <a href="http://asynchronous.org/">Josh Sled</a> of Sun Microsystems, on
    database indexing, joins and subqueries, database optimization, and more. If
    you're in the area Thursday evening, come join us!
</p>

<h3>Zend Framework 1.7.1</h3>
<p>
Yesterday, we released <a href="http://framework.zend.com/download/latest">Zend Framework 1.7.1</a>, 
    the first bugfix release in the 1.7 series. Not much more to say about it,
    other than start downloading!
</p>

<h3>Pastebin updates</h3>
<p>
    I've been continuing development on the <a href="http://github.com/weierophinney/pastebin">pastebin application</a> 
    I developed for demonstrating Zend Framework and Dojo integration. In the
    past couple weeks, I've reworked it substantially, adding support for
    <code>dojo.back</code> so as to stay in the same page while utilizing the
    application; the results are quite good. One side effect of this is that
    I've reworked and simplified the view scripts, and added REST, JSON-RPC, and
    XML-RPC endpoints to simplify the XHR infrastructure.  I'm getting pretty
    happy with the results...
</p>

<p>
    ...which has led me to jump to the next milestone, which is to integrate the
    <a href="http://github.com/weierophinney/bugapp">bug application</a> I
    worked up for the Dutch PHP Conference last summer and start creating a
    suite of collaborative developer tools. These are intended to do three
    things: (1) demonstrate best practices and good architecture when using Zend
    Framework, (2) demonstrate appropriate techniques when using Dojo with Zend
    Framework, and (3) to scratch an itch (I'd like to use these tools for my
    personal projects). I've code named the project "Spindle", a name I like for
    its rich connotations. If you're interested in contributing, drop me a line,
    and I'll set you up with commit access. Or fork it, and send me patches.
    Whatever.
</p>

<h3>Oh, and one more thing...</h3>
<p>
    Ha! fooled you!
</p>

<p>
    Seriously, though, there are, to quote something I saw on <a
        href="http://twitter.com">twitter</a> today, a "metric shit-ton" of
    holidays and observances of just about every faith and geographic origin in
    the coming month. Enjoy, and best tidings of the season to you!
</p>
EOT;
$entry->setExtended($extended);

return $entry;