<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('154-2007-Retrospective');
$entry->setTitle('2007 Retrospective');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1199224166);
$entry->setUpdated(1199359355);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'personal',
  1 => 'programming',
  2 => 'php',
  3 => 'file_fortune',
  4 => 'mvc',
  5 => 'pear',
  9 => 'zend framework',
  10 => 'zendcon',
));

$body =<<<'EOT'
<p>
    2007 was a busy year, both personally and professionally. I won't go into
    the personal too much, because, well, it's personal, and some of the details
    are simply inappropriate for blogging material.
</p>

<p>
    Here's the short version:
</p>

<ul>
    <li>One trip to Belgium and The Netherlands.</li>
    <li>Two trips to Israel.</li>
    <li>Two trips to Atlanta, GA (not counting the return trip from Europe, when
    I was stranded for a day due to storms in the Northeast).</li>
    <li>Three different user groups attended, with three presentations.</li>
    <li>One major Zend Framework release</li>
    <li>One PEAR release.</li>
    <li>One podcast.</li>
    <li>One webinar.</li>
    <li>One book published.</li>
    <li>One conference attended.</li>
</ul>

<p>
    What follows is my month-by-month breakdown:
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>January</h3>
<p>
    I finished up the last of my three chapters for 
    <a href="http://sitepoint.com/books/phpant2/">The PHP Anthology, 2nd Edition</a>, 
    and promptly used my advance to buy the family a Wii. 
</p>

<p>
    I was also introduced to <a href="http://jansch.nl/">Ivo Jansch</a> through
    work, and had him wow me with one of the 
    <a href="http://ibuildings.nl">ibuildings</a> products.
</p>

<h3>February</h3>
<p>
    Mid-month, my boss at <a href="http://www.zend.com/">Zend</a>, Boaz, got the
    go-ahead to use the ibuildings WDE platform to build our new website CMS;
    part of the plan would include training at the ibuildings home office in
    Vlissingen, NL... which meant I had to get my passport pronto.
</p>

<p>
    Towards the end of the month, I was invited to 
    <a href="http://bostonphp.org">BostonPHP</a> to present 
    building a simple <a href="http://framework.zend.com/">Zend Framework</a>
    CRUD application, along with <a href="http://hagunbu.ch/">Chuck Hagenbuch</a> 
    of the <a href="http://horde.org/">HORDE project</a>. While there, 
    <a href="http://cake.insertdesignhere.com/">Nate Abele</a> heckled me, and
    then joined Chuck and myself for an impromptu framework panel; a good time
    was had by all.
</p>

<h3>March</h3>
<p>
    I flew to Brussels, Belgium, where I met my supervisor, Boaz, so we could go
    to Vlissingen. We spent the day in Brussels, walking around and visiting
    such sites as the Cathedral of St. Michael, La Grand Place, and the Mannekin
    Pis. 
</p>

<p>
    Our visit to ibuildings was very productive, and I was very impressed by the
    team there; everybody was very knowledgeable and skilled. I presented a Zend
    Framework overview, as well as an abbreviated version of the Best Practices
    talk I'd given with Mike Naberezny at the 2006 ZendCon; the latter ignited a
    ton of questions and enthusiasm.
</p>

<p>
    On returning home, I had a ton of work to do on the zend.com CMS, and this
    continued in spurts through November. The job was made much easier by the
    ibuildings WDE product.
</p>

<p>
    I closed out a ton of MVC issues in the Zend Framework, and we released the
    first beta version late in the month.
</p>

<h3>April</h3>
<p>
    At the beginning of the month, our landlord threw us for a loop and
    announced he was going to sell our apartment... meaning that we either had
    to step up our plans to purchase a home, or start looking for a new rental.
    Ultimately, we ended up looking for a rental, due to time constraints. The
    next two months would be highlighted with the look for a new place as well
    as countless showings of our apartment to potential buyers.
</p>

<p>
    Mid-month, we packed up the family and flew down to Atlanta, GA, to visit my
    wife's family. While there, we were able to go to the Atlanta Zoo and see
    Mei Lan, their baby panda -- way cute!
</p>

<h3>May</h3>
<p>
    Mid-month, we found a new place in Richmond, VT -- a small village about 10
    minutes from Burlington, near where we originally lived when we first moved
    to Vermont. 
</p>

<p>
    During the first RC for Zend Framework, released at the end of the month, I
    introduced the ViewRenderer, a feature for auto-rendering views based on the
    current controller and action name -- a feature common to many frameworks.
    However, it ostensibly broke a ton of existing applications by being enabled
    by default -- not one of my more popular decisions. Since the 1.0.0 release,
    I've heard little grumbling about it, and it's now often cited as an
    ease-of-use feature -- go figure.
</p>

<h3>June</h3>
<p>
    The first week of June, I flew to Tel Aviv, Israel, to start training people
    on the new CMS, as well as to work with our entire ebiz team to finalize the
    work plan for completing the CMS. It was, needless to say, my first time to
    Israel or the Middle East, and I was constantly confronted with culture
    shock. Europe was an easy transition to make, but Israel was completely
    foreign to me -- everything from the way people drove, to the architecture,
    to the food was different. Unfortunately, I arrived a day late due to a
    flight cancellation, and missed the tour of Jerusalem my supervisor had
    planned for all of us. However, he took me to the city of Yafa, an Arabic
    city where the Israeli's originally tried to settle before building Tel Aviv
    to the north. The architecture was amazing, as were the winding, narrow
    streets of the old city.
</p>

<p>
    I was also told during this trip that Andi had requested transferring me
    full-time to the Zend Framework team. I would spend the next week or two
    weighing my options, and ultimately decided to do so.
</p>

<p>
    A week after I returned, we moved into our new rental in Richmond. The kids
    love the new place, which has a bedroom for each of them, a yard, and
    porches on each entrance.
</p>

<p>
    Somehow, I also found time to record my first (and so far only) 
    <a href="http://devzone.zend.com/article/2140-PHP-Abstract-Podcast-Episode-2---Backup-or-Die">PHP Abstract podcast</a>.
</p>

<h3>July</h3>
<p>
    We released <a href="http://framework.zend.com/">Zend Framework</a> 1.0.0 at
    the beginning of the month, marking our first stable release. While many
    still view it as incomplete, the overwhelming feedback has been positive,
    and we've had over 2 million downloads to date.
</p>

<p>
    I accepted the transfer to the Zend Framework team, but the condition was
    made that I would stay part-time on the ebiz team until the new site was
    launched.  This meant that the next 5 months were spent splitting my time
    between the two projects, often working late and on weekends to get work
    done.
</p>

<p>
    Towards the end of the month, we took a long weekend camping in Vermont's
    Northeast Kingdom. The weather was unseasonably wet, but we persevered and
    had a great time. 5 days of offline time was definitely needed!
</p>

<p>
    I also finally released the first stable version of 
    <a href="http://pear.php.net/packages/File_Fortune">File_Fortune</a> on 
    <a href="http://pear.php.net/">PEAR</a>, over a year since I'd first
    proposed it. The package interfaces with mod_fortune files, allowing both
    the ability to read and write such files, with full binary compatability.
</p>

<h3>August</h3>
<p>
    Not much to report in August, except work, work, and more work.
</p>

<h3>September</h3>
<p>
    My ebiz supervisor, Boaz, flew me to Tel Aviv for a second time, this time
    to perform a "brain dump" for the rest of the team before I transitioned
    fully out of the team, and also to help setup our new data center and
    release procedures. This time, Boaz took me to Jerusalem himself during my
    last full day in the country. If you've never been to the city, you should
    definitely put it on your list of things to do before you die. With my
    degree in religion, the place was full of meaning for me, but it would be
    putting it lightly to say that religion is palpable in the air there. We
    visited the Wailing Wall, the Via Dolorosa, the Church of the Holy
    Sepulchre, and listened to the muezzins sing the call to prayer for the
    muslims. The tour was simply amazing.
</p>

<p>
    A few days after I returned, I flew down to New York City for a special
    meeting of <a href="http://nyphp.org/">NYPHP</a>, where 
    <a href="http://blogs.zend.com/author/mark/">Mark de Visser</a> presented on
    various Zend products and initiatives, and I gave a Zend Framework overview.
</p>

<p>
    A week after the NYPHP presentation, I did a
    <a href="http://www.zend.com/webinars">zend.com webinar</a>
    on the Zend Framework MVC layer.
</p>

<h3>October</h3>
<p>
    October was the month of <a href="http://www.zendcon.con/">ZendCon</a>. I
    presented a full-day tutorial on best practices and unit testing with 
    <a href="http://sebastian-bergmann.de/">Sebastian Bergmann</a> and 
    <a href="http://naberezny.com/">Mike Naberezny</a>; despite the length and
    subject matter, we were SRO for most of the day. 
</p>
<p>
    I also did a main-stage presentation on Zend Framework's MVC components,
    directly following <a href="http://terrychay.com/blog/">Terry Chay</a> -- an
    intimidating situation at best. From the feedback I've seen, the
    presentation was well-received, and I had somewhere between 120 and 150
    attendees -- phenomenal! (Even more amazing was how many people were
    familiar with MVC in general!)
</p>

<p>
    One great thing about the conference was the fact that I got to network with
    a number of framework developers, both Zend Framework and otherwise,
    including Nate Abele of CakePHP as well as 
    <a href="http://paul-m-jones.com">Paul M. Jones</a> of 
    <a href="http://solarphp.com">the Solar framework</a>. Many good
    conversations were had.
</p>

<p>
    Late in the month, 
    <a href="http://sitepoint.com/books/phpant2/">The PHP Anthology, 2nd Edition</a>, 
    my first published book as an author, was finally released!
</p>

<h3>November</h3>
<p>
    I spent much of the month working on 
    <a href="http://framework.zend.com/wiki/display/ZFPROP/Zend_Layout">Zend_Layout</a>,
    a much requested component that simplifies and automates Two Step Views in
    Zend Framework. I also started work implementing 
    <a href="http://framework.zend.com/wiki/pages/viewpage.action?pageId=33071">Zend_View Enhanced</a>,
    a set of view helpers for making complex views with Zend_View possible.
</p>

<p>
    I also started playing with <a href="http://twitter.com/">Twitter</a> a bit,
    and came up with a 
    <a href="http://framework.zend.com/wiki/display/ZFPROP/Zend_Service_Twitter">Zend_Service_Twitter</a>
    proposal for interacting with the Twitter API via PHP.
</p>

<p>
    And finally, the Sunday before Thanksgiving, we finally launched the new 
    <a href="http://www.zend.com/">Zend.com</a> site, which was well-received in
    the blogosphere.
</p>

<h3>December</h3>
<p>
    A goal I've had for some time has been to form a PHP user group in the
    Burlington area. A friend of mine pointed out to me sometime this fall that
    there's actually already
    <a href="http://groups.google.com/group/Burlington-VT-PHP">a Google Group</a> 
    formed; he and the original founder started planning a meeting for early
    December. I spoke at this inaugural meeting, presenting Zend Framework's MVC
    layer yet again; a good time was had by all, and a lot of enthusiasm for
    future meetings was generated.
</p>

<p>
    I finished up Zend_Layout and Zend_View Enhanced with the help of Ralph
    Schindler, and got a new proposal up for 
    <a href="http://framework.zend.com/wiki/display/ZFPROP/Zend_Form">Zend_Form</a>,
    just in time for my holidays to begin -- 11 days with family and with little
    to no internet connectivity during a trip to Atlanta, GA for one of only a
    handful of Christmases I've spent without snow.
</p>

<h2>Summary</h2>
<p>
    This year was <em>incredibly</em> busy -- three cross-seas trips, one
    cross-continent trip, a move, and several trips along the Eastern Seaboard;
    three user group presentations, and eight presentations over the course of
    the year; one conference; one move; one PEAR release; one podcast; one
    webinar; one book; and countless hours of programming.
</p>

<p>
    My goals for the coming year? I'm too tired to even think about it ;-).
</p>
EOT;
$entry->setExtended($extended);

return $entry;