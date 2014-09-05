<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('189-Pastebin-app-and-conference-updates');
$entry->setTitle('Pastebin app and conference updates');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1220969963);
$entry->setUpdated(1221542130);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  3 => 'phpworks08',
  4 => 'webinar',
  5 => 'zend framework',
  6 => 'zendcon08',
));

$body =<<<'EOT'
<p>
    I have a number of updates and followups, and decided to post them in a
    single entry.
</p>

<p>
    First off, you may now <a
        href="http://www.zend.com/en/webinar/Framework-Dojo/Webinar-Rec-Framework-Dev-EN-ZFDojo-20080903.flv">view
        my Dojo Webinar online</a> (requires login and registration at
    zend.com). Attendance was phenomenal, and I've had some really good
    feedback. If you want to see it live, I'm giving the talk (with revisions!)
    at the <a href="http://www.zendcon.com/">ZendCon</a> UnConference, at 
    <a href="http://dojotoolkit.org/2008/07/10/dojo-developer-day-boston">Dojo Developer Day Boston</a> 
    later this month, and at 
        <a href="http://phpworks.mtacon.com/c/schedule/talk/d1s5/1">php|works</a>
    in November. I hope to be able to show new functionality at each
    presentation.
</p>

<p>
    Second, I've completed what I'm calling version 1.0.0 of the pastebin
    application I demo'd in the webinar. The PHP code is fully unit tested
    (though I haven't yet delved into using DOH! to test the JS), and
    incorporates a number of best practices and tips that Pete Higgins from Dojo
    was kind enough to provide to me. When using a custom build (and I provide a
    profile for building one), it simply flies.
</p>

<ul>
    <li><a href="/uploads/pastebin-1.0.0.tar.gz">Download the pastebin
        application</a></li>
</ul>

<p>
    The pastebin application showcases a number of features besides Dojo:
    <code>Zend_Test_PHPUnit</code> was used to test the application,
    and <code>Zend_Wildfire</code>'s FireBug logger and DB profiler are used to
    provide profiling and debug information.
</p>

<p>
    Finally, <a href="http://www.zendcon.com/">ZendCon</a> is next week! I'll be
    around, but already have a packed schedule (1 tutorial, 2 regular sessions,
    an UnCon session, a meet-the-developers session... and that's just what I
    know about!). I look forward to meeting ZF users and developers, though, so
    feel free to grab me and introduce yourself.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;