---
id: 154-2007-Retrospective
author: matthew
title: '2007 Retrospective'
draft: false
public: true
created: '2008-01-01T16:49:26-05:00'
updated: '2008-01-03T06:22:35-05:00'
tags:
    0: personal
    1: programming
    2: php
    3: file_fortune
    4: mvc
    5: pear
    9: 'zend framework'
    10: zendcon
---
2007 was a busy year, both personally and professionally. I won't go into the
personal too much, because, well, it's personal, and some of the details
are simply inappropriate for blogging material.

Here's the short version:

- One trip to Belgium and The Netherlands.
- Two trips to Israel.
- Two trips to Atlanta, GA (not counting the return trip from Europe, when I was stranded for a day due to storms in the Northeast).
- Three different user groups attended, with three presentations.
- One major Zend Framework release
- One PEAR release.
- One podcast.
- One webinar.
- One book published.
- One conference attended.

What follows is my month-by-month breakdown:

<!--- EXTENDED -->

### January

I finished up the last of my three chapters for
[The PHP Anthology, 2nd Edition](http://sitepoint.com/books/phpant2/),
and promptly used my advance to buy the family a Wii.

I was also introduced to [Ivo Jansch](http://jansch.nl/) through work, and had
him wow me with one of the [ibuildings](http://ibuildings.nl) products.

### February

Mid-month, my boss at [Zend](http://www.zend.com/), Boaz, got the go-ahead to
use the ibuildings WDE platform to build our new website CMS; part of the plan
would include training at the ibuildings home office in Vlissingen, NL… which
meant I had to get my passport pronto.

Towards the end of the month, I was invited to [BostonPHP](http://bostonphp.org)
to present building a simple [Zend Framework](http://framework.zend.com/) CRUD
application, along with [Chuck Hagenbuch](http://hagunbu.ch/) of the
[HORDE project](http://horde.org/). While there,
[Nate Abele](http://cake.insertdesignhere.com/) heckled me, and then joined
Chuck and myself for an impromptu framework panel; a good time was had by all.

### March

I flew to Brussels, Belgium, where I met my supervisor, Boaz, so we could go to
Vlissingen. We spent the day in Brussels, walking around and visiting such sites
as the Cathedral of St. Michael, La Grand Place, and the Mannekin Pis.

Our visit to ibuildings was very productive, and I was very impressed by the
team there; everybody was very knowledgeable and skilled. I presented a Zend
Framework overview, as well as an abbreviated version of the Best Practices talk
I'd given with Mike Naberezny at the 2006 ZendCon; the latter ignited a ton of
questions and enthusiasm.

On returning home, I had a ton of work to do on the zend.com CMS, and this
continued in spurts through November. The job was made much easier by the
ibuildings WDE product.

I closed out a ton of MVC issues in the Zend Framework, and we released the
first beta version late in the month.

### April

At the beginning of the month, our landlord threw us for a loop and announced he
was going to sell our apartment… meaning that we either had to step up our
plans to purchase a home, or start looking for a new rental. Ultimately, we
ended up looking for a rental, due to time constraints. The next two months
would be highlighted with the look for a new place as well as countless showings
of our apartment to potential buyers.

Mid-month, we packed up the family and flew down to Atlanta, GA, to visit my
wife's family. While there, we were able to go to the Atlanta Zoo and see Mei
Lan, their baby panda — way cute!

### May

Mid-month, we found a new place in Richmond, VT — a small village about 10
minutes from Burlington, near where we originally lived when we first moved to
Vermont.

During the first RC for Zend Framework, released at the end of the month, I
introduced the ViewRenderer, a feature for auto-rendering views based on the
current controller and action name — a feature common to many frameworks.
However, it ostensibly broke a ton of existing applications by being enabled by
default — not one of my more popular decisions. Since the 1.0.0 release, I've
heard little grumbling about it, and it's now often cited as an ease-of-use
feature — go figure.

### June

The first week of June, I flew to Tel Aviv, Israel, to start training people on
the new CMS, as well as to work with our entire ebiz team to finalize the work
plan for completing the CMS. It was, needless to say, my first time to Israel or
the Middle East, and I was constantly confronted with culture shock. Europe was
an easy transition to make, but Israel was completely foreign to me —
everything from the way people drove, to the architecture, to the food was
different. Unfortunately, I arrived a day late due to a flight cancellation, and
missed the tour of Jerusalem my supervisor had planned for all of us. However,
he took me to the city of Jaffa, an Arabic city where the Israeli's originally
tried to settle before building Tel Aviv to the north. The architecture was
amazing, as were the winding, narrow streets of the old city.

I was also told during this trip that Andi had requested transferring me
full-time to the Zend Framework team. I would spend the next week or two
weighing my options, and ultimately decided to do so.

A week after I returned, we moved into our new rental in Richmond. The kids love
the new place, which has a bedroom for each of them, a yard, and porches on each
entrance.

Somehow, I also found time to record my first (and so far only)
[PHP Abstract podcast](http://devzone.zend.com/article/2140-PHP-Abstract-Podcast-Episode-2---Backup-or-Die).

### July

We released [Zend Framework](http://framework.zend.com/) 1.0.0 at the beginning
of the month, marking our first stable release. While many still view it as
incomplete, the overwhelming feedback has been positive, and we've had over 2
million downloads to date.

I accepted the transfer to the Zend Framework team, but the condition was made
that I would stay part-time on the ebiz team until the new site was launched.
This meant that the next 5 months were spent splitting my time between the two
projects, often working late and on weekends to get work done.

Towards the end of the month, we took a long weekend camping in Vermont's
Northeast Kingdom. The weather was unseasonably wet, but we persevered and had a
great time. 5 days of offline time was definitely needed!

I also finally released the first stable version of
[File_Fortune](http://pear.php.net/packages/File_Fortune) on
[PEAR](http://pear.php.net/), over a year since I'd first proposed it. The
package interfaces with `mod_fortune` files, allowing both the ability to read
and write such files, with full binary compatability.

### August

Not much to report in August, except work, work, and more work.

### September

My ebiz supervisor, Boaz, flew me to Tel Aviv for a second time, this time to
perform a "brain dump" for the rest of the team before I transitioned fully out
of the team, and also to help setup our new data center and release procedures.
This time, Boaz took me to Jerusalem himself during my last full day in the
country. If you've never been to the city, you should definitely put it on your
list of things to do before you die. With my degree in religion, the place was
full of meaning for me, but it would be putting it lightly to say that religion
is palpable in the air there. We visited the Wailing Wall, the Via Dolorosa, the
Church of the Holy Sepulchre, and listened to the muezzins sing the call to
prayer for the muslims. The tour was simply amazing.

A few days after I returned, I flew down to New York City for a special meeting
of [NYPHP](http://nyphp.org/), where [Mark de Visser](http://blogs.zend.com/author/mark/)
presented on various Zend products and initiatives, and I gave a Zend Framework overview.

A week after the NYPHP presentation, I did a [zend.com webinar](http://www.zend.com/webinars)
on the Zend Framework MVC layer.

### October

October was the month of [ZendCon](http://www.zendcon.con/). I presented a
full-day tutorial on best practices and unit testing with
[Sebastian Bergmann](http://sebastian-bergmann.de/) and
[Mike Naberezny](http://naberezny.com/); despite the length and subject matter,
we were SRO for most of the day.

I also did a main-stage presentation on Zend Framework's MVC components,
directly following [Terry Chay](http://terrychay.com/blog/) — an intimidating
situation at best. From the feedback I've seen, the presentation was
well-received, and I had somewhere between 120 and 150 attendees — phenomenal!
(Even more amazing was how many people were familiar with MVC in general!)

One great thing about the conference was the fact that I got to network with a
number of framework developers, both Zend Framework and otherwise, including
Nate Abele of CakePHP as well as [Paul M. Jones](http://paul-m-jones.com) of
[the Solar framework](http://solarphp.com). Many good conversations were had.

Late in the month, [The PHP Anthology, 2nd Edition](http://sitepoint.com/books/phpant2/),
my first published book as an author, was finally released!

### November

I spent much of the month working on
[Zend_Layout](http://framework.zend.com/wiki/display/ZFPROP/Zend_Layout), a
much requested component that simplifies and automates Two Step Views in Zend
Framework. I also started work implementing [Zend_View Enhanced](http://framework.zend.com/wiki/pages/viewpage.action?pageId=33071),
a set of view helpers for making complex views with `Zend_View` possible.

I also started playing with [Twitter](http://twitter.com/) a bit, and came up
with a [Zend_Service_Twitter](http://framework.zend.com/wiki/display/ZFPROP/Zend_Service_Twitter)
proposal for interacting with the Twitter API via PHP.

And finally, the Sunday before Thanksgiving, we finally launched the new
[Zend.com](http://www.zend.com/) site, which was well-received in the
blogosphere.

### December

A goal I've had for some time has been to form a PHP user group in the
Burlington area. A friend of mine pointed out to me sometime this fall that
there's actually already [a Google Group](http://groups.google.com/group/Burlington-VT-PHP)
formed; he and the original founder started planning a meeting for early
December. I spoke at this inaugural meeting, presenting Zend Framework's MVC
layer yet again; a good time was had by all, and a lot of enthusiasm for future
meetings was generated.

I finished up `Zend_Layout` and `Zend_View` Enhanced with the help of Ralph
Schindler, and got a new proposal up for
[Zend_Form](http://framework.zend.com/wiki/display/ZFPROP/Zend_Form), just in
time for my holidays to begin — 11 days with family and with little to no
internet connectivity during a trip to Atlanta, GA for one of only a handful of
Christmases I've spent without snow.

Summary
-------

This year was *incredibly* busy — three cross-seas trips, one cross-continent
trip, a move, and several trips along the Eastern Seaboard; three user group
presentations, and eight presentations over the course of the year; one
conference; one move; one PEAR release; one podcast; one webinar; one book; and
countless hours of programming.

My goals for the coming year? I'm too tired to even think about it ;-).
