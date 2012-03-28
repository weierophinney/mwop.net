<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('204-Seven-Things-Tagged-by-Keith-Casey');
$entry->setTitle('Seven Things - Tagged by Keith Casey');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1230911094);
$entry->setUpdated(1231104712);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'perl',
  2 => 'personal',
));

$body =<<<'EOT'
<p>
    I'm really not sure I understand these "seven things" or "tagged" memes, but
    I'm going to give it a shot, after 
    <a href="http://caseysoftware.com/blog/seven-things-tagged-by-tony-bibbs">Keith Casey</a> did a drive-by
    tagging of me on New Year's Eve.
</p>

<p>
    So, without further ado, seven things you may not know about me...
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<ul>
    <li>
        <em>My actual college degree is in comparative religion.</em> I ended up
        in the Religion department at the 
        <a href="http://www.ups.edu/">University of Puget Sound</a> (yes, the
        initials are UPS, which can easily cause confusion with brown,
        parcel-bearing trucks), due to a line of questioning that occurred
        during an Artificial Intelligence course I was taking. The instructor
        was asking if there would be any ethical barrier to unplugging an AI --
        i.e., since it would be capable of thought, would this be equivalent to
        "killing" it? My initial response was, "No," because humans consist of
        more than thought... and then I started wondering a bit about that. My
        emphasis in religion was in Eastern religions. I have a minor in
        Mathematics (CS at UPS was actually CS/Mathematics).
    </li>

    <li>
        <em>I have an FCC Commercial Radio Operator's License.</em> My parents
        were volunteer DJs at <a href="http://www.kglt.net/">KGLT</a> while I
        was growing up, and I did my first radio announcing at... get this...
        the ripe age of 11. I finally got my license before starting college so
        that I could be a DJ at the university station... and ended up as the
        General Manager of <a href="http://en.wikipedia.org/wiki/KUPS">KUPS</a>
        my last two years.
    </li>

    <li>
        <em>I had long hair -- down to my butt at times -- for around ten
            years.</em> Which likely comes as a huge shock to those of you who
        have met me at conferences.  Ironically, I cut it off just prior to
        moving to Vermont as part of an effort to increase the success of my job
        hunt.
    </li>

    <li>
        <em>Before I started my programming career, I was a graphics
        technician.</em> The job immediately prior to my first programming
        position was with a small book publisher that specialized in bird
        hunting and flyfishing guidebooks, for which I created maps, scanned and
        processed images for books, and did book and catalog layout.
    </li>

    <li>
        <em>My first Object Oriented Programming was in Perl.</em> If you've
        ever done OOP in Perl, you'll likely agree with the following statement:
        OOP in any other language is easy by comparison. I mean, come on, a
        syntax where the very definition of an object requires that you "bless"
        a "thingy"? Truly; this is from the "bless" documentation:

        <blockquote>
            bless REF: This function tells the thingy referenced by REF that it
            is now an object in the CLASSNAME package. If CLASSNAME is omitted,
            the current package is used. Because a bless is often the last thing
            in a constructor, it returns the reference for convenience. Always
            use the two-argument version if a derived class might inherit the
            function doing the blessing. See perltoot and perlobj for more about
            the blessing (and blessings) of objects.
        </blockquote>

        This made OOP in PHP look easy.
    </li>

    <li>
        <em>I hold the degree of shodan in Aikido,</em> though I haven't trained
        in several years, due to time and travel constraints. I love the
        movement and flow of Aikido, and always found it very meditative. I also
        liked working with weapons, especially the bokken (wooden sword). This
        is why when I say, "don't make me get my clue bat out," you should take
        heed; I know from experience that white oak leaves a mark.
    </li>

    <li>
        <em>I could have been <a href="http://calevans.com/">Cal</a>.</em> When
        <a href="http://www.zend.com/">Zend</a> first interviewed me, it was for
        the position of Editor-in-Chief of 
        <a href="http://devzone.zend.com/">DevZone</a>. After my in-house
        interview, I had reservations -- I didn't feel experienced or connected
        enough, and was worried I'd botch it. Fortunately for me, and probably
        the PHP community in general, they decided to hire me as a PHP developer
        instead.
    </li>
</ul>

<p>
    So, that's seven things (and quite a bit more, really) about me. And now
    it's time to tag some others:
</p>

<ul>
    <li><a href="http://calevans.com/">Cal Evans</a> is an obvious choice for
    me. Besides having worked together for some years, he's a great friend.</li>

    <li><a href="http://www.leftontheweb.com/">Stefan Koopmanschap</a>, who
    took a train to Amsterdam just to have dinner and a beer with me.</li>
    <li><a href="http://seancoates.com/">Sean Coates</a>, whom I met in an
    airport on the way back from ZendCon two years ago, who lives less than two
    hours away, and whom I haven't seen since that ZendCon.</li>
    <li><a href="http://www.lornajane.net/">Lorna Jane Mitchell</a>, with whom
    I'll be doing a tutorial session on Subversion at php|tek, and who will be
    clearly flustered by being tagged.</li>
    <li><a href="http://jansch.nl/">Ivo Jansch</a>, whom I met almost two years
    ago, and somebody I admire and respect greatly.</li>
    <li><a href="http://www.khankennnels.com/blog/">Ligaya Turmelle</a>, one of
    my co-authors for "The PHP Anthology," the woman who got me to volunteer as
    a phpwomen Booth Babe, and now MySQL guru.</li>
    <li><a href="http://akrabat.com/">Rob Allen</a>, who has made my job easier
    by publishing tutorials and now a book on Zend Framework, and who in
    real-life is a mild-mannered Clark Kent I'd gladly raise a pint with any
    day.</li>
</ul>

<p>
    And here are the rules I'm supposed to pass on to the above bloggers:
</p>

<ul>
    <li>Link your original tagger(s), and list these rules on your blog.</li>
    <li>Share seven facts about yourself in the post - some random, some wierd.</li>
    <li>Tag seven people at the end of your post by leaving their names and the
    links to their blogs.</li>
    <li>Let them know they've been tagged by leaving a comment on their blogs
    and/or Twitter.</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;