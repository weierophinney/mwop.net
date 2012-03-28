<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('179-DPC08-Wrapup');
$entry->setTitle('DPC08 Wrapup');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1213961625);
$entry->setUpdated(1213977388);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'conferences',
  2 => 'dpc08',
  4 => 'zend framework',
));

$body =<<<'EOT'
<p>
    Last Friday and Saturday I spent at <a
        href="http://phpconference.nl/">the Dutch PHP Conference</a>, hosted by
    <a href="http://ibuildings.nl/">Ibuildings</a>. Unfortunately, I had very
    little time to blog while there. I'd prepared my outlines and basic slides
    before heading to the conference, but had a large number of screenshots and
    images to prepare that kept me up until the wee hours of the morning each
    day. In addition, the conference was extremely well organized -- which meant
    that any time not spent speaking was spent interacting with attendees or
    other speakers -- never a bad thing!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First off, the big impression I had when the conference finished is that
    either PHP developers in Holland are incredibly professional, or that the
    best practices advocated by various community leaders are finally taking
    hold. My last talk was one addressing development best practices, and I was
    constantly amazed at how many people were <em>already</em> using most if not
    all the best practices I touched on in my talk. This is really fantastic
    news, as far as I'm concerned; hopefully all those PHP detractors out there
    are going to start taking notice that PHP development has matured, and, in
    fact become very quality oriented. Here's that presentation:
</p>

<div style="width:425px;text-align:left" id="__ss_472388"><object style="margin:0px" width="425" height="355"><param name="movie" value="http://static.slideshare.net/swf/ssplayer2.swf?doc=20080614bestpractices-1213726335523088-9"/><param name="allowFullScreen" value="true"/><param name="allowScriptAccess" value="always"/><embed src="http://static.slideshare.net/swf/ssplayer2.swf?doc=20080614bestpractices-1213726335523088-9" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="355"></embed></object><div style="font-size:11px;font-family:tahoma,arial;height:26px;padding-top:2px;"><a href="http://www.slideshare.net/?src=embed"><img src="http://static.slideshare.net/swf/logo_embd.png" style="border:0px none;margin-bottom:-5px" alt="SlideShare"/></a> | <a href="http://www.slideshare.net/weierophinney/everyday-best-practices-of-php-development?src=embed" title="View Everyday Best Practices of PHP Development on SlideShare">View</a> | <a href="http://www.slideshare.net/upload?src=embed">Upload your own</a></div></div>

<p>
    The big event for me, of course, was the full day Zend Framework workshop.
    My two regrets: a) not having a better script for the final hour, when I
    covered a simple demo bugapp used for developing the workshop, and b)
    running opposite <a href="http://sebastian-bergmann.de">Sebastian
        Bergmann</a> -- as I wanted to see what he's been working on recently
    with <a href="http://phpunit.de/">PHPUnit</a>. That said, I feel the
    workshop went over very well; I was able to finish each section early enough
    that we had time for 5 - 15 minutes of questions over the material just
    covered, and there were excellent questions from those attending. The funny
    part was that with two of the questions, I simply fired up my browser to the
    tutorials I wrote on <a href="http://devzone.zend.com/">DevZone</a>, and
    used the examples and materials from them to answer the questions.
</p>

<div style="width:425px;text-align:left" id="__ss_472382"><object style="margin:0px" width="425" height="355"><param name="movie" value="http://static.slideshare.net/swf/ssplayer2.swf?doc=20080613zendframeworkworkshop-1213725963433006-9"/><param name="allowFullScreen" value="true"/><param name="allowScriptAccess" value="always"/><embed src="http://static.slideshare.net/swf/ssplayer2.swf?doc=20080613zendframeworkworkshop-1213725963433006-9" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="355"></embed></object><div style="font-size:11px;font-family:tahoma,arial;height:26px;padding-top:2px;"><a href="http://www.slideshare.net/?src=embed"><img src="http://static.slideshare.net/swf/logo_embd.png" style="border:0px none;margin-bottom:-5px" alt="SlideShare"/></a> | <a href="http://www.slideshare.net/weierophinney/zend-framework-workshop?src=embed" title="View Zend Framework Workshop on SlideShare">View</a> | <a href="http://www.slideshare.net/upload?src=embed">Upload your own</a></div></div>

<p>
    As promised, I've <a href="/uploads/bugapp.tar.gz">uploaded the bug application</a> I worked up to
    create the session. Be aware that it's incomplete; the main thing was to see
    how each of the various pieces could interact so I'd know what to cover and
    when in the workshop.
</p>

<p>
    Following the night of the workshops, Ibuildings had a dinner for their
    employees, and I was invited to tag along. I had some great conversations
    with <a href="http://www.lornajane.net">Lorna Jane</a> and one of her
    colleagues, Bill, discussing topics ranging from family to training, from
    travel to PHP, and more. Dinner was timed to end as the big match between
    Holland and France began, and all I have to say about that is: if you've
    never witnessed the fans of a big football match in Holland when Oranje is
    winning, you haven't lived. 'Nuff said. :-)
</p>

<p>
    I had a number of good conversations with a variety of people, really --
    <a href="http://inside.e-novative.de/">Stefan Priebsch</a>, 
    <a href="http://sebastian-bergmann.de/">Sebastian</a>, 
    <a href="http://suraski.net/blog/">Zeev</a>, 
    <a href="http://mtabini.blogspot.com">Marco Tabini</a>, 
    Lorna Jane, Stefan Koopfmanschap, 
    <a href="http://www.aide-de-camp.org/">Fabien Potencier</a>, and more. 
    (Believe it or not, you <em>can</em> get more than one framework lead in the
    same room and have things stay civil; Fabien and I swapped some information
    regarding plans for our next major releases, what issues we've seen, and
    what features we're excited about.) <a href="http://felix.phpbelgium.be/blog/">Felix De Vliegher</a> and 
    <a href="http://www.dragonbe.com/">Michelangelo Van Damme</a>
    were also there, representing PHP Belgium, and it was great to hear how
    their region's PHP community is starting to come together.
</p>

<p>
    I also got a chance to meet a few people from the Zend Framework lists and
    IRC in person: Andries Seutens, Jurrien Stutterheim, and Bart McLeod -- and
    a few others whose names I most regrettably forget. I wish I could have had
    more time to talk with each of you, and discuss your projects more.
</p>

<p>
    Now, while the conference was fantastic, probably the best part for me
    personally was the day prior. My good friend <a href="http://www.wolerized.com/">Remi</a>, with whom I've worked on
    some projects at Zend a number of times over the past year, came up from
    Gouda to hang with me for the day. We walked all over the city -- through
    the Vondelpark, up by the Rijksmuseum and Van Gogh museums, along and
    over countless canals, near (but not through) the red light district, by the
    RAI (where the conference was held) and all the way up to Grand Centraal. I
    complained about sore feet and legs all weekend, but it was the best tour I
    could have imagined of the city, and one I shan't forget any time soon.
    And the reason we went to Grand Centraal was to meet up with 
    <a href="http://www.leftontheweb.com/">Stefan Koopmanschap</a>, with whom we
    would then have dinner and drinks that night. I feel very fortunate to have
    had the chance to spend some uninterrupted time with each of these fantastic
    developers and individuals.
</p>

<p>
    So, the short summary: excellent conference, excellent friends, excellent
    city. Looking forward to DPC '09!
</p>
EOT;
$entry->setExtended($extended);

return $entry;