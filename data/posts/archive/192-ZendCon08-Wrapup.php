<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('192-ZendCon08-Wrapup');
$entry->setTitle('ZendCon08 Wrapup');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1222398690);
$entry->setUpdated(1222531571);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'dojo',
  1 => 'php',
  4 => 'zencon08',
  5 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm a bit late on my <a href="http://www.zendcon.com/">ZendCon'08</a>
    wrapup; the conference left me both exhausted and with a backlog of email
    and work that has consumed me since it ended. However, this, too, is good,
    as it has given me time to reflect... and to finally get my slides up on
    SlideShare.
</p>

<p>
    ZendCon was alternately exhausting, rewarding, educational, fruitful,
    infurating, and ultimately wonderful. I've been to every single ZendCon so
    far -- I started at Zend a scant month before the inaugural event -- and
    have spoken at each. My first time speaking was a fluke; 
    <a href="http://www.sklar.com/">David Sklar</a> had just started at 
    <a href="http://www.ning.com/">Ning</a> and had to back out of his
    "Configuring PHP" tutorial session. 
    <a href="http://mikenaberezny.com/">Mike Naberezny</a> and I were drafted to
    take it over, and we had N+1 attendees, where N was the number of speakers.
    Since that inauspicious beginning, I've gradually taken on more sessions and
    stuck around to participate in the conference more. I can honestly say that
    this was the biggest, busiest, and most community focussed ZendCon I can
    remember.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    This year, I was involved in a marathon seven -- yes, that's right, seven --
    sessions over three days, and only finally got the last day off.  
    <a href="http://mikenaberezny.com/">Mike</a> and I did our 
    <a href="http://www.slideshare.net/weierophinney/best-practices-of-php-development-presentation/">Best Practices tutorial session</a> 
    on Monday, which was very well attended. Based on the numbers of people
    raising their hands as we asked if they were implementing or familiar with
    the various practices we enumerated, we may be able to begin doing more
    advanced, in-depth sessions in the future.
</p>

<p>
    On Tuesday, <a href="http://inside.e-novative.de/">Stefan Priebsch</a>
    <strike>roped me into</strike> convinced me to help with a "round table"
    UnConference session on the role of ORMs in PHP. The consensus we had was
    that ORM tools are quite good for prototyping and rapid development, but
    that any good ORM solution should de-couple the data access layer to allow
    stubbing with well-tuned SQL when performance becomes a consideration.
</p>

<p>
    Later that morning, I did a presentation on 
    <a href="http://www.slideshare.net/weierophinney/getting-started-with-zend-framework-presentation/">Getting Started with Zend Framework</a>. 
    I developed this presentation to follow our newly re-launched 
    <a href="http://framework.zend.com/docs/quickstart">Quick Start</a>. The
    talk was very well attended, and I received some excellent questions from
    attendees following the talk. I hope to streamline it in the future so it
    can be presented as a screencast or webinar. If you are interested in the
    material, I suggest heading to the link above and downloading the quick
    start materials; they are well-documented and will cover at least as much as
    I covered in the talk.
</p>

<p>
    In the afternoon, I did another unconference session, this time performing
    my <a href="http://www.slideshare.net/weierophinney/rich-uis-and-easy-remoting-with-dojo-and-zend-framework-presentation/">Rich UIs and Easy XHR with Dojo and Zend Framework</a> talk. 
    (Did I mention that conference attendees could not seem to get enough Zend
    Framework material?) I really enjoyed doing this talk <em>live</em> this
    time (I've done it previously for a webinar) -- I received some wonderful
    questions, but even better, I was able to gauge the audience reactions to
    what I was presenting. I was pleased to see people getting as excited about
    Dojo as I've become, and I hope to see that enthusiasm grow. Dojo is truly a
    fantastic choice when it comes to javascript toolkits, and I think I
    suitably demonstrated how easy it is to use Dojo now from Zend Framework.
</p>

<p>
    Tuesday afternoon, I presented my session on 
    <a href="http://www.slideshare.net/weierophinney/zendform-presentation/">Zend_Form</a>. 
    We'd originally planned to do a talk on Zend_Layout and Zend_Form, but
    there's at least two hours of material there that simply does not condense
    to 1 hour. Instead, we had Ralph present a session on Zend_Layout during an
    UnConference session, while I focussed on Zend_Form. Again, it was fun to do
    this in front of a live audience -- albeit one I could barely see from my
    perch on the mainstage. I saw some places to trim for next time -- which
    will allow me to show off Zend_Dojo integration with Zend_Form in the
    future.
</p>

<p>
    Immediately following that, I headed off to do yet another UnConference
    session with <a href="http://lornajane.net/">Lorna Jane</a>. 
    <a href="http://caseysoftware.com/blog">Keith Casey</a> had approached me on
    Monday following the tutorial Mike and I presented, indicating that there
    were requests for an "svn tips and tricks" presentation for the UnCon. I
    told him to ask Lorna Jane if she'd be interested, as I'd seen her do an
    excellent presentation on the subject at the Dutch PHP Conference in June.
    After some back and forth, we decided to do it together, and sketched out a
    rough outline early Wednesday morning. The talk was very well attended, and
    again had great audience participation. Doing the presentation has inspired
    us to consider submitting a joint proposal for a conference in the future.
</p>

<p>
    I quickly ran downstairs, only to find I was immediately wanted for a "Meet
    the Team" session. This has become a staple at ZendCon, and has had led to
    some... interesting... interchanges in the past. This year, the session was
    packed, and we had some very good discussions touching on every Zend product
    -- from Zend Framework to Zend Platform. There were certainly some hecklers,
    but all of it was in good fun, and we had a brilliant time. (Man, I think
    hanging with the UK folk has worn off on my vocabulary.)
</p>

<p>
    I actually attended fewer sessions than I was involved in, which was unusual
    and strange. Every one I was able to attend was quite good. Standouts for me
    include Jay Pipes' tutorial on Join Foo, which raised many questions for me
    and sparked a number of discussion points all week. Additionally, I was
    delighted to be able to attend Alex Russell's Dojo talk; I've exchanged
    dozens of emails with him over the past months while doing the ZF/Dojo
    integration, and it was fascinating to hear his summary of the state of HTML
    and browser support, as well as how he feels Dojo fits in the ecosystem. I
    was fortunate enough to be able to grab him afterwards so we could have
    lunch and talk shop -- and got an even larger surprise to discover he was
    not only familiar with all aspects of the Dojo support I'd done, but had
    used much of it!
</p>

<p>
    Being as busy as I was, I didn't have much chance to stop and enjoy the
    community until Wednesday evening. And the community is quite vibrant! I
    have often been behind in my slide preparations or tied up in meetings and
    unable to "join the fun" that often marks good PHP events. This time, I felt
    quite tapped into the community, as well as welcomed by all. I had
    innumerable conversations, both with people wholly unfamiliar to me, people
    I've seen yearly at ZendCon, and people I've been "seeing" virtually on IRC
    and the mailing lists. The strength of any open source project is only as
    good as the community it attracts, and on this basis alone, PHP is thriving.
</p>

<p>
    So, goodbye, ZendCon08 and all my new and old friends -- let's hope we can
    meet again next year!
</p>
EOT;
$entry->setExtended($extended);

return $entry;