---
id: 203-2008-The-year-in-review
author: matthew
title: '2008: The year in review'
draft: false
public: true
created: '2008-12-31T14:17:03-05:00'
updated: '2009-01-05T02:00:49-05:00'
tags:
    - php
    - dojo
    - programming
---
That time of year again — wrap-up time. Each year, it seems like it's the busiest ever, and I often wonder if it will ever slow down. As usual, I'm restricting myself to primarily professional activities out of respect for the privacy of my family.

The short, executive summary:

- One trip to Israel
- One trip to The Netherlands
- One trip to California's Bay Area
- One trip to Atlanta, GA
- Three minor releases of Zend Framework
- Seven webinars, six for zend.com and one for Adobe
- Three conferences attended as a speaker, including:
  - One six-hour workshop
  - One three-hour tutorial (as a co-presenter)
  - Four regular sessions
  - Two panel sessions (one scheduled, one for an uncon)
  - Two uncon sessions (one as a co-presenter)
  - One foul-mouthed Pecha Kucha talk
- Ten Burlington, VT PHP User's Group meetings attended; I spoke at many
- One Bug Hunt week organized
- Two books reviewed as a technical editor
- Six articles for [DevZone](http://devzone.zend.com/)
- 50 blog entries (including this one)

Read on for the gruesome, month-by-month breakdown.

<!--- EXTENDED -->

January and February
--------------------

I started coding `Zend_Form` in earnest in January, and had a preview ready for testing mid-month. The pace continued into February, as I addressed user feedback and issues, and continued working feverishly towards what would become Zend Framework's 1.5.0 release.

I also answered many questions for and had many discussions with [Cal Evans](http://www.calevans.com/) relating to his "Guide to Programming Zend Framework".

March
-----

I gave my first webinar of the year early in March, on the subject of `Zend_Form`; it was very well attended, but unfortunately there was an issue recording the sound, so the recording was never released. After two more failed attempts at re-recording, we gave up. I apologize to all who have wanted to view it.

While working towards the 1.5.0 release, [Ralph Schindler](http://ralphschindler.com/) and I also finished up final touches on `Zend_Layout` and the various "placeholder" view helper implementations.

On 17 March 2008, we released Zend Framework 1.5, our first minor release following 1.0.0.

I started blogging tips and tricks for 1.5, and also writing articles for [DevZone](http://devzone.zend.com/) during March, and had tremendous amounts of feedback. In fact, one blog post, on "Login and Authentication", is still one of the most trafficked on my blog after 9 months.

I also began what I envisioned as a series of Vim posts, but, alas, it has resulted only in two.

April
-----

Following the 1.5 release, I did weekly posts for a month or so covering various features of Zend Framework, including Front Controller plugins, Action Controller helpers, and View helpers. Towards the end of the month, the entire team participated in a Q&amp;A webinar to discuss the 1.5 release.

May
---

At the beginning of the month, I released the last in my series of 1.5 tutorials on [DevZone](http://devzone.zend.com/), covering Form decorators. This has saved me countless hours on IRC and in the mailing lists explaining how this aspect of forms work.

During this month, we also finally announced that Zend Framework would be partnering with [Dojo](http://dojotoolkit.org/) to provide an out-of-the-box RIA solution. I began work identifying the various integration points and creating proposals for implementation. I also did a Q&amp;A webinar with the Dojo team outlining the proposed integration.

At the end of the month, the ZF team reorganized the subversion repository to allow for an "Extras" repository, and also to ensure that projects originating in the laboratory or extras and migrating to the standard library retain all history. Even with the big notices we put on the ZF site, articles on DevZone, and posts on various blogs, we still get questions on this seven months later. Let this be a lesson to you: plan your repositories for any contingency as early as you can!

June
----

I started June with a trip to Israel, to visit the Zend offices. I met up with [Andi](http://andigutmans.blogspot.com/) in Newark, and we flew from there to Israel several rows apart in coach. One of the best meals I've ever had was after we landed and he invited me to his sister's place for dinner, and we dined on chicken marinated in home-pressed olive oil and fresh herbs, hummus, and pitas baked with fresh oregano. The rest of the week was spent in the office, in endless meetings.

Four days after returning from Israel, I flew to Amsterdam for the [Dutch PHP Conference](http://www.phpconference.nl), to which I'd been invited to speak. My good friend [Remi](http://wolerized.com/) took the train up to Amsterdam to meet me the day I flew in, and we walked and walked and walked around the city, until dinnertime. [Stefan Koopmanschap](http://www.leftontheweb.com/) then met us for a lovely dinner, and I returned to the hotel to finish screenshots for the six-hour workshop on Zend Framework I was presenting the next day. The entire conference was wonderful, and I met many fantastic people, including [Stefan Priebsch](http://www.priebsch.de/), [Lorna Jane Mitchell](http://www.lornajane.net/), [Andries Seutens](http://andries.systray.be/), and many, many more — plus many familiar faces, such as Sebastian Bergmann, Derek Rethans, Mike Van Dam, Felix de Vliegher, and Marco Tabini.

On my blog, I started raising the question of how we will refer to Abstract classes and Interfaces in PHP 5.3, but I think my arguments went largely unheard and/or misunderstood.

The last half of the month was spent working on both Dojo integration with Zend Framework (a task that turned out fairly easy, in large part due to some wonderful guidance from [Pete Higgins](http://higginsforpresident.net/)), and preparing `Zend_Test_PHPUnit` for inclusion in Zend Framework 1.6.

July
----

I think I'll remember July as the month of the neverending release cycle.

August
------

On 8 August 2008, PHP 4 officially died. I thought about drinking a toast for about 3 seconds, forgot about it, and finished my beer.

The following Monday, we released the second release candidate of Zend Framework 1.6.0.

August, too, became part of the month of the neverending release cycle.

September
---------

Finally, on 2 September 2008, we released 1.6.0 into the wild. My contributions included, as noted earlier, Dojo integration, PHPUnit integration, and code assistance on our Captcha solution and file upload support.

The next day, I gave yet another webinar on Zend Framework and Dojo integration, but finally actually had some code samples and working demos to show off, soundly quieting the claims of vaporware. I also started learning about Dojo release builds, under the tutelage of Pete Higgins.

Mid-month saw the fourth annual Zend/PHP Conference, this time in Santa Clara. I was involved in a marathon of seven different sessions over three days. I've rarely been so exhausted, and it's a wonder I remember anything following — but I had a wonderful time with the PHP *community* following, including [Jon Whitcraft](http://www.bombdiggity.net/), [Rob Allen](http://akrabat.com/), the [ibuildings](http://ibuildings.nl/) crew, and more.

I also finally got to meet [David Sklar](http://sklar.com/), to whom I owe the fact of my first public speaking engagement at the first ZendCon.

Following ZendCon, there were announcements that two colleagues at Zend I respect highly were leaving for new opportunities: Mark de Visser left to join Sonatype as its CEO, and Cal Evans left to head ibuildings' new Center for PHP Expertise. I wish them both luck in their new endeavors.

October
-------

I helped [Wade Arnold](http://wadearnold.com/blog/) complete testing of `Zend_Amf` as we prepared for the Zend Framework 1.7.0 release, and learned a fair deal about Flex in the process.

During this time, I also completed a technical review of [Zend Framework in Action](http://zendframeworkinaction.com). Rob Allen and Nick Lo had contacted me earlier in the year, but I'd been unable to commit to it. In July, I agreed, only to get sucked into the neverending release cycle. Fortunately, in October I had time to complete the review. The book is very well written and organized, and I can't recommend it highly enough. I was able to give some constructive feedback and have some dialog with Rob that, hopefully, helped clarify a few areas of Zend Framework, and will hopefully help their readers.

For the 1.7 release of Zend Framework, I worked on performance benchmarking, profiling, improvements, and a best practices guide.

Late in the month, I delivered a webinar with Lee Brimelow for Adobe to showcase the upcoming AMF support in Zend Framework.

November
--------

The last few days of October and first week of November, I organized a bug hunt week for Zend Framework, culminating in a [Bug Hunt Day](http://bughuntday.org/) event held and organized by [PHP Belgium](http://www.phpbelgium.be/) and [phpGG](http://phpgg.nl/) (The Netherlands). We closed out close to 150 issues over the course of the week and a couple dozen during the Bug Hunt Day, and got many contributors started on the path of professional bug squashing enlightenment.

The second week of November, I flew down to Atlanta, GA, to attend [php|works](http://phpworks.mtacon.com/).. err, php|works/pyworks. First off, a huge thank you to [Elizabeth Naramore](http://naramore.net/blog/), who helped me fairly last minute to make sure I had a room to stay in. While there, I presented my Dojo and Zend Framework talk, but with some updated content. Of course, every presenter's nightmare occurred, and I had to reboot my laptop mid-stream. I surprised myself, and, I think, the attendees by actually being able to continue speaking while we waited for my machine to reboot.

I also presented a [Pecha Kucha](http://en.wikipedia.org/wiki/Pecha_Kucha) talk — I re-branded the phrase as "Pikachu" a couple weeks earlier (a reference to the iconic character in [Pokemon](http://www.pokemon.com/), a game I play with my daughter), and that phrase has, for better or for worse, stuck. My talk was on how to be banned from an open source project, and I swore entirely too much. It was a nice release, however, as I try to be politic in public usually, and sometimes just need to rant.

I got to see a ton of old and new friends alike while there — former Zenders [Mike Naberezny](http://mikenaberezny.com/) and [Paul M. Jones](http://paul-m-jones.com/), [Keith Casey](http://caseysoftware.com/blog/), [Matthew Turland](http://ishouldbecoding.com/), [Ivo](http://jansch.nl/) and a bunch of the ibuildings crew, Pollita (sorry, I have to stop linking everyone now…), Sebastian… basically, a ton of the usual suspects. I also met a lot of new people, many of them introducing themselves as ZF users; I appreciate all of you introducing yourselves, as *you* are the reason I code.

The following Monday, 17 November 2008, we released Zend Framework 1.7.0, timed to coincide with the the Adobe MAX conference, as AMF support was our major story for the release. `Zend_Amf` has generated tremendous buzz in both the PHP and Flash/Flex communities, due to the simplicity and robustness of its design. This release also marked the first release to include the extras repository — which now ships with community-contributed [JQuery](http://jquery.com/) support.

December
--------

[Chris Shiflett](http://shiflett.org/) and [Sean Coates](http://seancoates.com/) organized this year's [PHP Advent Calendar](http://phpadvent.org/2008/), and solicited entries from a select group of PHP community members a week in advance. I didn't volunteer to contribute for the first week, but managed to get mine in on the first day… only to see it appear the very next. Hopefuly, my guide to responsible contributions will help those wondering how to report and/or fix bugs in open source projects.

I started blogging more, in part due to more free time in the evenings (it's nice when the kids go to bed at a reasonable hour!), and in part due to finally putting a number of ideas into a blogging "backlog" so that I could pick up and post when I had time. From this, I added an entry on mumbles/irssi integration, autocompletion with ZF and Dojo, created a simple pubsub implementation for PHP, and started a series of posts on how to architect models (and some concrete tips for doing so). I have more posts in December than I have in several other months combined.

Reflection
----------

This past year, I became much more involved with both the Zend Framework and greater PHP communities, and feel I have enriched my life with many wonderful new friends — some local, some global. I feel truly fortunate to be working in a job I love, contributing to a project that helps others do the jobs they love, and part of such an accepting and vibrant group of people.

Looking back, I travelled less, though because most of it was in a five month period, it felt like more. On that note, I vow never to do back to back trips across the big pond, as it was incredibly exhausting.

Looking ahead to 2009
---------------------

I have several things to look forward to already in 2009. I'll be continuing my series of posts on models. In February, I will have an article published in a print magazine for the first tiem. I'll be speaking at PHP Quebec in March, presenting two sessions and sitting in on a panel. I hope to speak at several other conferences, and potentially write more articles and tutorials. Overall, I want to contribute more to the ecosystem of best practices in PHP, particularly in the areas of testing and deployment strategies.

I hope this post finds *you* in good health and spirits, and that you have a fantastic start to the new year!
