<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('146-ZendCon-is-over-at-last');
$entry->setTitle('ZendCon is over at last');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1192150247);
$entry->setUpdated(1192691981);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'conferences',
  3 => 'zend framework',
  4 => 'zendcon',
));

$body =<<<'EOT'
<p>
    <a href="http://www.zendcon.com/">ZendCon '07</a> is finally over, the dust
    has settled, and I finally find myself with some time alone... practically
    the first I've had since Sunday. The week was fantastic, and I had many good
    conversations and brainstorming sessions. Oh, and I ended up giving three
    different sessions, so it's time for links to slides and materials:
</p>
<ul>
    <li><a href="/uploads/2007-ZendCon-BestPractices.odp">Best Practices of PHP Development</a>. 
        <a href="http://sebastian-bergmann.de/">Sebastian</a>, 
        <a href="http://mikenaberezny.com/">Mike</a>, and I presented a full-day
        tutorial on PHP development best practices, focussing primarily on
        testing and testing strategies, but also covering coding standards,
        usage of SCM tools, and deployment. There were a ton of questions from
        the attendees, and Sebastian even whipped out some extra slides at the
        end showing new and little-known features of PHPUnit. Basically, reading
        the slides won't really indicate what we covered, but is more of a
        general outline. It was an honor and pleasure to work with Sebastian and
        Mike on this, and I hope we can do it again in the future some time.
    </li>

    <li><a href="/uploads/2007-ZendCon-MVC.ppt">Zend Framework MVC Quick Start</a>.
        This was basically the same session I did in my <a href="http://www.zend.com/webinar">webinar</a>
        a couple weeks ago, with a few corrections and a small demonstration.
        Cal put me on directly following <a href="http://terrychay.com/blog">Terry Chay</a>,
        in the largest of the four session rooms -- the one where all the
        keynotes occurred -- talk about intimidating! Amazingly, the session was
        really well attended -- others I talked to estimate between 100 and 150
        people showed up. The most amazing part, though, was that when I asked
        how many people knew what 'MVC' was, I don't think there was a single
        person who didn't raise their hand -- definitely a sign of how well
        accepted the pattern now is in PHP.
    </li>

    <li><a href="/uploads/2007-ZendCon-AjaxPresentation.odp">AJAX-Enabling Your Zend Framework Controllers</a>.
        I did this talk for the <a href="http://www.zendcon.com/wiki/index.php?title=Uncon">Unconference</a>,
        mainly because its a topic I've been interested in and wanted to
        present. In it, I detailed how to ajax-enable an application through
        some easy tricks with Action and View Helpers and using JS to decorate
        your existing application. The reference app I used was a pastebin, and
        I've got code for both <a href="http://dojotoolkit.org">Dojo</a> and 
        <a href="http://prototypejs.org">Prototype</a> flavors available:
        <ul>
            <li><a href="/uploads/PastebinDojo.tar.gz">Dojo pastebin</a></li>
            <li><a href="/uploads/PastebinPrototype.tar.gz">Prototype pastebin</a></li>
        </ul>
    </li>
</ul>

<p>
    The two highlight keynote speakers, for me, were definitely 
    <a href="http://www.joelonsoftware.com/">Joel Spolsky</a> and
    <a href="http://craphound.com/">Cory Doctorow</a>. Neither spoke about PHP,
    but both spoke about topics that PHP developers should take to heart.
    Perhaps I'll elaborate on those in another post.
</p>

<p>
    Another bonus for me was the number of old and new friends alike I got to
    see -- I had many good conversations with Paul M. Jones, Nate Abele, Ivo
    Jansch, and Ralph Schindler, and opportunities to finally meet fellow
    co-author Lig Turmelle, Ben Ramsey, Chris Shifflet (dude, we've been to four
    conferences together, and never yet met!), and many, many others. I was also
    overwhelmed by the number of Zend Framework users who sought me out either
    to ask me questions or simply thank me and the others on the team for the
    project; I'm deeply honored that I can work on a project that affects so
    many developers.
</p>

<p>
    And now for some down time to recuperate...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;