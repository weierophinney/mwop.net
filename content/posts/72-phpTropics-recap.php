<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('72-phpTropics-recap');
$entry->setTitle('php|Tropics recap');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1116640941);
$entry->setUpdated(1116644057);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    <a href="http://www.phparch.com/tropics">php|Tropics</a> last week was
    excellent, and I'm not even sure where to begin for a recap. I started
    journaling while there, but found I simply did not have enough time (or
    bandwidth -- my one complaint about the conference) to keep up.
</p>
<p>
    The trip down was hellish. I'd lost my birth certificate, and was turned
    away at the ticket counter due to improper documentation. I was able to
    reschedule for several hours later, and had <em>just</em> enough time to go
    home, look futilely for my birth certificate, and then run down to the town
    clerk office in Bolton to get a certified and notarized letter proving my
    voter registration. Which got me on the plane, but I still ended up having
    to sign a notarized affidavit of citizenship before my final flight from
    Houston could take off.
</p>
<p>
    The resort where the conference was held is called Moon Palace Resort, and
    is one of a string of five or more 'Palace' resorts on the Mexican Riviera
    below Cancun. The place is huge -- there are two lobby areas, and each
    serves what looks like a couple thousand rooms. You can't help but get
    exercise while there, because there's simply so much ground to cover.
</p>
<p>
    <a href="http://blogs.phparch.com/mt/">Marco Tabini</a> gave the keynote,
    and kept it short and sweet. To my mind, he covered one primary point: the
    PHP job market sucks for pay. As he put it, why does an assembly line worker
    at Ford get paid $35/hour, while the average PHP developer is paid $15/hour?
    This is not to belittle the assembly line worker, but more to ask the
    question of whether or not a PHP developer is inherently less skilled. Marco
    feels that the way to boost PHP wages is to produce a standard educational
    corpus and licensing program -- and educate employers as to why someone who
    has taken them deserves to be paid more. (I'm still not sure how I feel
    about his conclusions, but they're certainly food for thought -- as I'm not
    being paid much more than the PHP average.)
</p>
<p>
    Rob and I skipped out on the first two sessions. To our mind, we weren't
    as interested in them as other sessions, and we weren't going to have much
    free time while there. We put on the swim trunks and took a stroll through
    the pool. Yes, a stroll. I wish I could find a map that shows just how long
    and serpentine the pool there is; you can easily get a good workout just
    going for a stroll in the pool.
</p>
<p>
    And strolling is thirsty business, so we strolled over to a swim-up bar.
    Now, one excellent thing about the location is that the resort is an
    "all-inclusive" resort -- meaning that all food <em>and</em> drink is
    included in the price. So, we ordered margaritas, since it was noon where we
    live. They were okay, a little weak... and Rob noted that the bartender
    wasn't using anything off the top shelf. "What do you think we need to do to
    get him to use the Sauza?" asked Rob, to which I replied, "I think you just
    ask for it." So, next round, "Margaritas con Sauza, por favor."
</p>
<p>
    And so it goes.
</p>
<p>
    Strolling also included the beach, a wonderful expanse of white sand
    overlooking the azure water of the Caribbean. I spent a lot of time just
    sitting under coconut trees staring out over the sea; I can see how tempting
    it would be to retire on the Caribbean.
</p>
<p>
    We started attending sessions that afternoon, and only missed one more over
    the course of the conference. I did get some excellent information from a
    number of sessions -- but one thing in particular I got out of the
    conference as a whole is how far advanced is the setup Rob and I have put
    together at NGA. Our web cluster is almost as good as it gets without
    pouring money into commercial databases and support (though <a
        href="http://ilia.ws/">Ilia Alshanetsky's</a> web acceleration session
    gave me a ton of information on tuning the relations between php, apache,
    and the OS); we've standardized on PEAR and are using it well; we're
    filtering data reasonably well (though we can always do better); etc. I
    often feel like I'm behind the curve when it comes to our technology, so the
    conference was a welcome boost to the ego.
</p>
<p>
    On day 2, I ran into <a href="http://paul-m-jones.com/blog">Paul Jones</a>,
    with whom I've emailed once or twice, and on whose blog I've commented
    several times. We immediately started hanging out, and talking shop. Which
    began the other important aspect of the conference: the social networking.
</p>
<p>
    In day-to-day practice, I really only get to talk code and program with one
    other person, Rob. This is fine, but it leads to a narrow exposure. Going to
    the conference gave me a chance to go over code and coding philosophy with a
    larger variety of people -- my peer group, if you will. I got to see that,
    if you're not working for a large corporation, you do the same shit I do
    every day -- programming, installing and tuning servers, help desk issues,
    everything; coding in PHP is only one aspect of your busy life. It was
    actually refreshing to see that I'm not alone.
</p>
<p>
    A group of six of us got together that second evening, and ate out at one of
    the 'restaurants' (there are several eateries at the resort; not really
    restaurants, 'cause you don't have to pay, and they're all buffets)
    overlooking the Caribbean. As we were talking, we commented on how the
    networking amongst each other was probably the best part of the conference
    -- and how it would be nice if the speakers would deign to join us.
</p>
<p>
    Ask and ye shall receive. Later that evening, as we stood around the 'swing
    bar' (a little tiki bar with swings instead of bar stools, out on an island
    of the large, serpentine pool), we were gradually joined by speakers,
    including Marcus Boerger, <a href="http://netevil.org/">Wez Furlong</a>, <a
        href="http://www.derickrethans.nl/">Derick Rethans</a>, and
    <a href="http://blog.backendmedia.com/">Lukas Smith</a>. We had some great
    discussions that started devolving in indirect proportion to the amount we
    drank (well, not really devolving, but certainly migrating to other
    non-coding topics...)
</p>
<p>
    Unfortunately, Rob and I had to cut out around 11:30, as we were taking the
    <a
        href="http://www.zend.com/store/education/certification/zend-php-certification.php">Zend
    Certification Exam</a> at 8 the following morning. I quit drinking between
    9:30 and 10... Rob had not, so he got to do the exam with a hangover. All in
    all, I found the exam less difficult than the study guide, but certainly
    full of tricks meant to foil you.
</p>
<p>
    The final evening had a similar conclusion to the night before, only with
    even more participants, including <a
        href="http://blog.casey-sweat.us/">Jason Sweat</a> and Ilia. This time
    there were no exams to follow, and we stayed up until 1:30 (and later for
    some people). 
</p>
<p>
    Looking back, I see I wrote very little about the actual conference -- which
    seems odd, as it was the central event. There were certainly some excellent
    presentations, and a lot of great material -- much of it I have not been
    able to find elsewhere. Hopefully I'll find some time to blog about it in
    the coming weeks.
</p>
<p>
    I didn't take many pictures, and I need to get a gallery going anyways. Rob,
    however, took a ton of pictures and put them up on his site each day. You
    can view them at <a href="http://riggen.org/gallery/">his gallery</a>; I'll
    put direct links to the individual galleries later.
</p>
<p>
    So, if you get a chance, attend the next PHP conference you can possibly
    afford, and spend as much time as possible getting to know your fellow PHP
    code monkeys; the benefits are, to use an oft-used marketing phrase,
    priceless.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;