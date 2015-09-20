---
id: 221-Blog-Backlog
author: matthew
title: 'Blog Backlog'
draft: false
public: true
created: '2009-08-20T15:47:04-04:00'
updated: '2009-08-25T17:12:26-04:00'
tags:
    - php
    - 'zend framework'
---
Several people have pointed out to me recently that I haven't blogged since
early May, prior to attending [php|tek](http://tek.mtacon.com/). Since then,
I've built up a huge backlog of blog entries, but had zero time to write any of
them.

The backlog and lack of time has an easy explanation: my change of roles from
Architect to Project Lead on the [Zend Framework](http://framework.zend.com/)
team. While the change is a welcome one, it's also been much more demanding on
my time than I could have possibly envisioned. Out of the gate, I had to finish
up the 1.8 release, and move immediately into planning and execution of the 1.9
release — while learning the ropes of my new position, and continuing some of
my previous development duties. Add a couple of conferences (php|tek and
[DPC](http://phpconference.nl/)) into the mix, and you can begin to see the
issues.

<!--- EXTENDED -->

At the time I write this, ZF currently stands at version 1.9.1, with 1.9.2 just
around the corner. A few unsung bits about the 1.9 series:

- I updated the coding standards slightly to include naming conventions for
  abstract classes and interfaces
- I finally added in documentation standards (at the prompting of our two most
  active documentation translators).
- The test suite no longer uses output buffering, which means you can see test
  status in realtime, and it no longer segfaults after using all available RAM.

I'm currently in planning mode, and hope to start spinning out some articles and
tutorials in the coming weeks (I posted one today), as well as finally posting a
roadmap for ZF 2.0 (hint: there will be at least a 1.10 first). I've been
playing a bit with document-based databases such as CouchDB, as well as with
Dependency Injection, Doctrine, and pub-sub architectures. I hope to blog about
some of my experiments in the coming weeks.

This autumn, I'll be speaking at two separate conferences. I'll be joining
php|a's [CodeWorks](http://codeworks.mtacon.com/) for the East Coast tour,
starting in Atlanta, and moving on through Miami, Washington, D.C., and New York
City. A few weeks later, I'll be at [ZendCon](http://zendcon.com/), giving
back-to-back tutorials on Zend Framework, and a regular session on domain models
in MVC frameworks.

If you don't hear from me, and need to contact me, you can find me on twitter,
freenode under my registered nick (if you don't know it, you shouldn't be
contacting me), or the various framework mailing lists. If I'll be in your area
during the autumn conference season, please look me up!
