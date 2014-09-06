<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('203-2008-The-year-in-review');
$entry->setTitle('2008: The year in review');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1230751023);
$entry->setUpdated(1231138849);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'dojo',
  2 => 'programming',
));

$body =<<<'EOT'
<p>
    That time of year again -- wrap-up time. Each year, it seems like it's the
    busiest ever, and I often wonder if it will ever slow down. As usual, I'm
    restricting myself to primarily professional activities out of respect for
    the privacy of my family.
</p>

<p>
    The short, executive summary:
</p>

<ul>
    <li>One trip to Israel</li>
    <li>One trip to The Netherlands</li>
    <li>One trip to California's Bay Area</li>
    <li>One trip to Atlanta, GA</li>
    <li>Three minor releases of Zend Framework</li>
    <li>Seven webinars, six for zend.com and one for Adobe</li>
    <li>Three conferences attended as a speaker, including:<ul>
         <li>One six-hour workshop</li>
         <li>One three-hour tutorial (as a co-presenter)</li>
         <li>Four regular sessions</li>
         <li>Two panel sessions (one scheduled, one for an uncon)</li>
         <li>Two uncon sessions (one as a co-presenter)</li>
         <li>One foul-mouthed Pecha Kucha talk</li>
    </ul></li>
    <li>Ten Burlington, VT PHP User's Group meetings attended; I spoke at
        many</li>
    <li>One Bug Hunt week organized</li>
    <li>Two books reviewed as a technical editor</li>
    <li>Six articles for <a href="http://devzone.zend.com/">DevZone</a></li>
    <li>50 blog entries (including this one)</li>
</ul>

<p>
    Read on for the gruesome, month-by-month breakdown.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>January and February</h2>

<p>
    I started coding <code>Zend_Form</code> in earnest in January, and had a
    preview ready for testing mid-month. The pace continued into February, as I
    addressed user feedback and issues, and continued working feverishly towards
    what would become Zend Framework's 1.5.0 release.
</p>

<p>
    I also answered many questions for and had many discussions with 
    <a href="http://www.calevans.com/">Cal Evans</a> relating to his "Guide to
    Programming Zend Framework".
</p>

<h2>March</h2>

<p>
    I gave my first webinar of the year early in March, on the subject of
    <code>Zend_Form</code>; it was very well attended, but unfortunately there
    was an issue recording the sound, so the recording was never released. After
    two more failed attempts at re-recording, we gave up. I apologize to all who
    have wanted to view it.
</p>

<p>
    While working towards the 1.5.0 release, <a href="http://ralphschindler.com/">Ralph Schindler</a>
    and I also finished up final touches on <code>Zend_Layout</code> and the
    various "placeholder" view helper implementations.
</p>

<p>
    On 17 March 2008, we released Zend Framework 1.5, our first minor release
    following 1.0.0.
</p>

<p>
    I started blogging tips and tricks for 1.5, and also writing articles for
    <a href="http://devzone.zend.com/">DevZone</a> during March, and had
    tremendous amounts of feedback. In fact, one blog post, on "Login and
    Authentication", is still one of the most trafficked on my blog after 9
    months.
</p>

<p>
    I also began what I envisioned as a series of Vim posts, but, alas, it has
    resulted only in two.
</p>

<h2>April</h2>

<p>
    Following the 1.5 release, I did weekly posts for a month or so covering
    various features of Zend Framework, including Front Controller plugins,
    Action Controller helpers, and View helpers. Towards the end of the month,
    the entire team participated in a Q&amp;A webinar to discuss the 1.5
    release.
</p>

<h2>May</h2>

<p>
    At the beginning of the month, I released the last in my series of 1.5
    tutorials on <a href="http://devzone.zend.com/">DevZone</a>, covering Form
    decorators. This has saved me countless hours on IRC and in the mailing
    lists explaining how this aspect of forms work.
</p>

<p>
    During this month, we also finally announced that Zend Framework would be
    partnering with <a href="http://dojotoolkit.org/">Dojo</a> to provide an
    out-of-the-box RIA solution. I began work identifying the various
    integration points and creating proposals for implementation. I also did a
    Q&amp;A webinar with the Dojo team outlining the proposed integration.
</p>

<p>
    At the end of the month, the ZF team reorganized the subversion repository
    to allow for an "Extras" repository, and also to ensure that projects
    originating in the laboratory or extras and migrating to the standard
    library retain all history. Even with the big notices we put on the ZF site,
    articles on DevZone, and posts on various blogs, we still get questions on
    this seven months later. Let this be a lesson to you: plan your repositories
    for any contingency as early as you can!
</p>

<h2>June</h2>

<p>
    I started June with a trip to Israel, to visit the Zend offices. I met up
    with <a href="http://andigutmans.blogspot.com/">Andi</a> in Newark, and we
    flew from there to Israel several rows apart in coach. One of the best meals
    I've ever had was after we landed and he invited me to his sister's place
    for dinner, and we dined on chicken marinated in home-pressed olive oil and
    fresh herbs, hummus, and pitas baked with fresh oregano. The rest of the
    week was spent in the office, in endless meetings.
</p>

<p>
    Four days after returning from Israel, I flew to Amsterdam for the 
    <a href="http://www.phpconference.nl">Dutch PHP Conference</a>, to which I'd
    been invited to speak. My good friend <a href="http://wolerized.com/">Remi</a> 
    took the
    train up to Amsterdam to meet me the day I flew in, and we walked and walked
    and walked around the city, until dinnertime. 
    <a href="http://www.leftontheweb.com/">Stefan Koopmanschap</a> then met us
    for a lovely dinner, and I returned to the hotel to finish screenshots for
    the six-hour workshop on Zend Framework I was presenting the next day. The
    entire conference was wonderful, and I met many fantastic people, including
    <a href="http://www.priebsch.de/">Stefan Priebsch</a>, 
    <a href="http://www.lornajane.net/">Lorna Jane Mitchell</a>, 
    <a href="http://andries.systray.be/">Andries Seutens</a>, and many, many
    more -- plus many familiar faces, such as Sebastian Bergmann, Derek Rethans,
    Mike Van Dam, Felix de Vliegher, and Marco Tabini.
</p>

<p>
    On my blog, I started raising the question of how we will refer to Abstract
    classes and Interfaces in PHP 5.3, but I think my arguments went largely
    unheard and/or misunderstood.
</p>

<p>
    The last half of the month was spent working on both Dojo integration with
    Zend Framework (a task that turned out fairly easy, in large part due to
    some wonderful guidance from <a href="http://higginsforpresident.net/">Pete Higgins</a>), 
    and preparing <code>Zend_Test_PHPUnit</code> for inclusion in Zend Framework
    1.6.
</p>

<h2>July</h2>

<p>
    I think I'll remember July as the month of the neverending release cycle.
</p>

<h2>August</h2>

<p>
    On 8 August 2008, PHP 4 officially died. I thought about drinking a toast
    for about 3 seconds, forgot about it, and finished my beer.
</p>

<p>
    The following Monday, we released the second release candidate of Zend
    Framework 1.6.0. 
</p>

<p>
    August, too, became part of the month of the neverending release cycle.
</p>
    
<h2>September</h2>

<p>
    Finally, on 2 September 2008, we released 1.6.0 into the wild.  My
    contributions included, as noted earlier, Dojo integration, PHPUnit
    integration, and code assistance on our Captcha solution and file upload
    support.
</p>

<p>
    The next day, I gave yet another webinar on Zend Framework and Dojo
    integration, but finally actually had some code samples and working demos to
    show off, soundly quieting the claims of vaporware. I also started
    learning about Dojo release builds, under the tutelage of Pete Higgins.
</p>

<p>
    Mid-month saw the fourth annual Zend/PHP Conference, this time in Santa
    Clara. I was involved in a marathon of seven different sessions over three
    days. I've rarely been so exhausted, and it's a wonder I remember anything
    following -- but I had a wonderful time with the PHP <em>community</em>
    following, including <a href="http://www.bombdiggity.net/">Jon
        Whitcraft</a>, <a href="http://akrabat.com/">Rob Allen</a>, the <a
        href="http://ibuildings.nl/">ibuildings</a> crew, and
    more. 
</p>
    
<p>
    I also finally got to meet <a href="http://sklar.com/">David Sklar</a>, to
    whom I owe the fact of my first public speaking engagement at the first
    ZendCon.
</p>

<p>
    Following ZendCon, there were announcements that two colleagues at Zend I
    respect highly were leaving for new opportunities: Mark de Visser left to
    join Sonatype as its CEO, and Cal Evans left to head ibuildings' new Center
    for PHP Expertise. I wish them both luck in their new endeavors.
</p>

<h2>October</h2>

<p>
    I helped <a href="http://wadearnold.com/blog/">Wade Arnold</a> complete
    testing of <code>Zend_Amf</code> as we prepared for the Zend Framework 1.7.0
    release, and learned a fair deal about Flex in the process.
</p>

<p>
    During this time, I also completed a technical review of 
    <a href="http://zendframeworkinaction.com">Zend Framework in Action</a>. 
    Rob Allen and Nick Lo had contacted me earlier in the year, but I'd
    been unable to commit to it. In July, I agreed, only to get sucked into the
    neverending release cycle. Fortunately, in October I had time to complete
    the review. The book is very well written and organized, and I can't
    recommend it highly enough. I was able to give some constructive feedback
    and have some dialog with Rob that, hopefully, helped clarify a few areas of
    Zend Framework, and will hopefully help their readers.
</p>

<p>
    For the 1.7 release of Zend Framework, I worked on performance benchmarking,
    profiling, improvements, and a best practices guide.
</p>

<p>
    Late in the month, I delivered a webinar with Lee Brimelow for Adobe to
    showcase the upcoming AMF support in Zend Framework.
</p>

<h2>November</h2>

<p>
    The last few days of October and first week of November, I organized a bug
    hunt week for Zend Framework, culminating in a 
    <a href="http://bughuntday.org/">Bug Hunt Day</a> event held and organized
    by <a href="http://www.phpbelgium.be/">PHP Belgium</a> and 
    <a href="http://phpgg.nl/">phpGG</a> (The Netherlands). We closed out close
    to 150 issues over the course of the week and a couple dozen during the Bug
    Hunt Day, and got many contributors started on the path of professional bug
    squashing enlightenment.
</p>

<p>
    The second week of November, I flew down to Atlanta, GA, to attend
    <a href="http://phpworks.mtacon.com/">php|works</a>.. err,
    php|works/pyworks.  First off, a huge thank you to 
    <a href="http://naramore.net/blog/">Elizabeth Naramore</a>, who
    helped me fairly last minute to make sure I had a room to stay in. While
    there, I presented my Dojo and Zend Framework talk, but with some updated
    content. Of course, every presenter's nightmare occurred, and I had to
    reboot my laptop mid-stream. I surprised myself, and, I think, the attendees
    by actually being able to continue speaking while we waited for my machine
    to reboot.
</p>

<p>
    I also presented a <a href="http://en.wikipedia.org/wiki/Pecha_Kucha">Pecha Kucha</a> 
    talk -- I re-branded the phrase as "Pikachu" a couple weeks earlier (a
    reference to the iconic character in <a href="http://www.pokemon.com/">Pokemon</a>, 
    a game I play with my daughter), and that phrase has, for better or for
    worse, stuck. My talk was on how to be banned from an open source project,
    and I swore entirely too much. It was a nice release, however, as I try to
    be politic in public usually, and sometimes just need to rant.
</p>

<p>
    I got to see a ton of old and new friends alike while there -- former
    Zenders <a href="http://mikenaberezny.com/">Mike Naberezny</a> and 
    <a href="http://paul-m-jones.com/">Paul M. Jones</a>, 
    <a href="http://caseysoftware.com/blog/">Keith Casey</a>, 
    <a href="http://ishouldbecoding.com/">Matthew Turland</a>, 
    <a href="http://jansch.nl/">Ivo</a>
    and a bunch of the ibuildings crew, Pollita (sorry, I have to stop linking
    everyone now...), Sebastian... basically, a ton of the usual suspects. I
    also met a lot of new people, many of them introducing themselves as ZF
    users; I appreciate all of you introducing yourselves, as <em>you</em> are
    the reason I code.
</p>

<p>
    The following Monday, 17 November 2008, we released Zend Framework 1.7.0,
    timed to coincide with the the Adobe MAX conference, as AMF support was our
    major story for the release. <code>Zend_Amf</code> has generated tremendous
    buzz in both the PHP and Flash/Flex communities, due to the simplicity and
    robustness of its design. This release also marked the first release to
    include the extras repository -- which now ships with community-contributed
    <a href="http://jquery.com/">JQuery</a> support.
</p>

<h2>December</h2>

<p>
    <a href="http://shiflett.org/">Chris Shiflett</a> and 
    <a href="http://seancoates.com/">Sean Coates</a> organized this year's 
    <a href="http://phpadvent.org/2008/">PHP Advent Calendar</a>,
    and solicited entries from a select group of PHP community members a week in
    advance. I didn't volunteer to contribute for the first week, but managed to
    get mine in on the first day... only to see it appear the very next.
    Hopefuly, my guide to responsible contributions will help those wondering
    how to report and/or fix bugs in open source projects.
</p>

<p>
    I started blogging more, in part due to more free time in the evenings (it's
    nice when the kids go to bed at a reasonable hour!), and in part due to
    finally putting a number of ideas into a blogging "backlog" so that I could
    pick up and post when I had time. From this, I added an entry on
    mumbles/irssi integration, autocompletion with ZF and Dojo, created a simple
    pubsub implementation for PHP, and started a series of posts on how to
    architect models (and some concrete tips for doing so). I have more posts in
    December than I have in several other months combined.
</p>

<h2>Reflection</h2>

<p>
    This past year, I became much more involved with both the Zend Framework and
    greater PHP communities, and feel I have enriched my life with many
    wonderful new friends -- some local, some global. I feel truly fortunate to
    be working in a job I love, contributing to a project that helps others do
    the jobs they love, and part of such an accepting and vibrant group of
    people.
</p>

<p>
    Looking back, I travelled less, though because most of it was in a five
    month period, it felt like more. On that note, I vow never to do back to
    back trips across the big pond, as it was incredibly exhausting.
</p>

<h2>Looking ahead to 2009</h2>

<p>
    I have several things to look forward to already in 2009.  I'll be
    continuing my series of posts on models. In February, I will have an article
    published in a print magazine for the first tiem.  I'll be speaking at PHP
    Quebec in March, presenting two sessions and sitting in on a panel. I hope
    to speak at several other conferences, and potentially write more articles
    and tutorials. Overall, I want to contribute more to the ecosystem of best
    practices in PHP, particularly in the areas of testing and deployment
    strategies.
</p>

<p>
    I hope this post finds <em>you</em> in good health and spirits, and that you
    have a fantastic start to the new year!
</p>
EOT;
$entry->setExtended($extended);

return $entry;