<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('call-a-spade-a-spade');
$entry->setTitle('Call A Spade A Spade');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1326923287);
$entry->setUpdated(1326923287);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
  1 => 'advocacy',
  2 => 'politics',
  3 => 'internet',
));

$body =<<<'EOT'
<p>
    I don't often get political on my blog, or over social media. But those of 
    you who <a href="http://twitter.com/weierophinney">follow me on twitter</a> 
    lately have been treated to a number of tweets and retweets from me about 
    some bills before the US legislative bodies called "SOPA" and "PIPA". Over
    the last couple days, I realized exactly why I disagree with them, and 
    felt 140 characters is not enough.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Copyright Isn't Distribution</h2>

<p>
    First off, I want to state that I respect the rights of copyright holders. 
    People and businesses that create unique and new things completely deserve
    the right to monetize them exclusively. That said, I also believe in 
    listening to your customers; if customers find it too difficult to obtain 
    and/or consume your products, you should figure out how to make that easier.
    That's a distribution problem, not a legal one.
</p>

<p>
    One way copyright holders try to curb unlimited copying of products and thus
    retain control of distribution is via Digital Rights Management (DRM). However,
    DRM usage is often in direct contradiction to how users consume media.
</p>

<p>
    Why? How?
</p>

<ul>
    <li>
        I cannot lend files to others. I came of age in the 80s.
        I constantly lent and borrowed books and music from my friends. If I 
        really liked something, I'd buy it. But with DRM-encumbered media, I
        can't do this. If my wife buys an ebook from [major book seller] to read
        on her [major e-reader brand], likes it, and wants me to read it, in most
        cases I have to re-purchase the book. The same applies to music. To me,
        this is a step backwards.
    </li>

    <li>
        As I said, I have a number of devices: a computer, a laptop, a tablet, 
        an ebook reader, a smartphone. What I have on me is a matter of 
        geography and convenience. I may start reading an ebook on my computer, 
        but later will want to read it on my tablet during lunch, and on my 
        ebook reader when in bed. However, most forms of DRM don't allow this 
        sort of device shifting -- especially if the devices are from different 
        manufacturers.  Those that do often have limits on how many devices may 
        be "authorized" at any given time, or how many may access the media at 
        any given time.  Most of these schemes require an internet connection 
        -- and for those occasions when internet connectivity is unavailable, 
        you're out of luck.  Whatever happend to the <a 
        href="http://en.wikipedia.org/wiki/Fair_use">doctrine of Fair Use</a>?
    </li>

    <li>
        Let's be honest, digital storage fails more often than anyone likes to
        either admit or accept. Back-ups are essential. However, depending on
        the DRM scheme, this may or may not be acceptable. What happens to the
        media I purchased 5 years ago? What if it's no longer readable -- do I
        need to re-purchase? Back in the day, I'd have made a few copies of an
        album, tape, or CD, and if the original was corrupt or destroyed, I'd 
        still be able to listen, without any additional cost. Can I say the
        same for digital media?
    </li>
</ul>

<p>
    With all these problems, why does DRM exist?
</p>

<p>
    Originally the idea was to ensure that creators and distributors are 
    getting paid for the works they produce -- which is reasonable. However, 
    with the ease of copying in the digital age, the idea is also to get paid 
    for every format in which
    the work is released -- the assumption is that if you're using the work on 
    a different device, you must be a different person. This flies in the face
    of how many consumers actually consume digital media -- as I've shown above
    it's not at all uncommon to have multiple devices from multiple vendors 
    owned by a single person. The assumption that payment is required for each 
    format is an assumption either made out of ignorance or greed. I'd argue 
    that DRM as applied to consumer media -- music, books, and video -- is used 
    primarily to circumvent the doctrine of Fair Use, plain and simple.
</p>

<h2>SOPA and PIPA are Censorship Bills</h2>

<p>
    DRM is but one "solution" the entertainment industry has come up with to 
    the "problem" of copying. I put those words in quotes as I think they're
    dubious at best -- DRM is an imperfect solution, and copying may or may
    not be a real problem; I've read numerous studies that raise doubts on 
    these points.
</p>

<p>
    Another "solution" provided was the Digital Millenium Copyright Act (DMCA) of 1998.
    This bill basically gave copyright holders the ability to submit takedown 
    notices to websites or Internet Service Providers (ISP) hosting alleged 
    copyrighted materials, giving them a set time period to remove said 
    materials or appeal the notice.  The bill has both supporters and 
    opponents, with the opponents arguing that it infringes on the doctrine of 
    fair use, as well as circumvents due process (among other things). One 
    thing it <em>does</em> get right is that it in the case of websites that 
    allow user-submitted content, it does not hold the site responsible for 
    policing submissions. Additionally, if you dispute the takedown notice, there
    are no provisions to block continued distribution of the allegedly infringing
    media.
</p>
    
<p>
    However, the entertainment industry feels the DMCA doesn't go quite far enough, 
    and has convinced legislators to propose a solution more akin to using a 
    sledgehammer to pound a thumbtack.
</p>

<p>
    The bills are called SOPA and PIPA; SOPA is the Stop Online Piracy Act put
    forward in the US House of Representatives, and PIPA is the "Protect IP 
    Act" put forward in the US Senate. The basic tenets are familiar: protect
    the rights of US copyright and intellectual property holders.
</p>

<p>
    What's different is the <em>how</em>. 
</p>

<ul>
    <li>
        Unlike the DMCA, a site holding user-submitted content <em>can</em> be 
        held liable for distribution of copyrighted material.
    </li>

    <li>
        Instead of takedown notices, court orders would require ISPs, ad networks
        and payment processors to suspend business with the allegedly infringing
        site. Additionally, search engines could be barred from displaying
        links to the site. In fact, any site linking to the site, or even 
        providing instruction or tools on how to access the site, could be 
        potentially considered infringing. 
    </li>

    <li>
        The kicker is that none of the above requires any notification to the 
        alleged copyright infringer, the bills provide a very, very small window 
        for the alleged copyright infringer to dispute the allegation, and even
        in the case of a successful dispute, no means to re-coup any damages due
        to loss of revenue while the injunction was in place. Which could be 
        many days, weeks, or even months. And during that time, you cannot
        circumvent any measures put in place to bar access to your site or to
        payment processors and ad agencies.
    </li>
</ul>

<p>
    To my thinking, these bills completely disregard <em>due process</em>, 
    rights provided in the 5th and 14th amendments of the US constitution. On 
    top of that, they disregard the 4th amendment, which guards against 
    unreasonable searches and seizures. Finally due to the fact that the bills 
    would allow takedown of allegedly infringing sites, it opens the door for 
    alleged copyright holders to <b>censor</b> sites with which they do not 
    agree -- violating our 1st amendment right to Free Speech. By the time a
    dispute is resolved, even if overturned, the victim of a takedown may no
    longer be able to stay in business, or may have lost its audience.
</p>

<p>
    "But wait!" some supporters will say, "These bills apply only to 
    <em>foreign</em> websites!" Sure -- but it means (a) that we do not apply
    our own laws to foreigners, and (b) that we're essentially setting up
    censorship of foreign websites -- in effect, censoring the web for US 
    citizens. Both of these are appalling, and alone are reason enough not to 
    support the bills. On top of this, however, is the fact that the way sites 
    are hosted, particularly in this era of cloud hosting, the line between 
    "foreign" and "domestic" is easily blurred, and the net result is the 
    wholesale circumvention of constitutional rights.
</p>

<h2>This is not about the tech industry</h2>

<p>
    I heard about these bills because I am an internet and technology 
    professional, and these bills would affect my livlihood. But the bills 
    would not only affect me, but everybody in the United States who uses the
    internet, and those outside the US who access US sites or have users in
    the US. 
</p>
    
<p>
    I was very surprised to discover that my misgivings are not about the 
    technical underpinnings of the bills, but the human problems they present.  
    While I'm sympathetic to copyright holders, I also think that existing 
    solutions have a long ways to go towards satisfying Fair Use, and new 
    solutions must be created that do not incur loss of constitutionally 
    granted freedoms.  I do not want to live in a world without a free and 
    open internet, and the information and ideas it helps enable.
</p>

<p>
    Call out SOPA and PIPA for what they really are: censorship. Contact
    your representatives today, and ask them if they plan to support corporate
    interests by enabling censorship, or if they plan to honor their vows to 
    uphold the constitution.
</p>
EOT;
$entry->setExtended($extended);

return $entry;