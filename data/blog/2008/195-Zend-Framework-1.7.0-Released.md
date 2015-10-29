---
id: 195-Zend-Framework-1.7.0-Released
author: matthew
title: 'Zend Framework 1.7.0 Released'
draft: false
public: true
created: '2008-11-17T14:29:14-05:00'
updated: '2008-11-19T10:07:11-05:00'
tags:
    0: php
    2: 'zend framework'
---
Today, we released [Zend Framework 1.7.0](http://framework.zend.com/download/latest).
This release features [AMF support](http://framework.zend.com/manual/en/zend.amf.html),
[JQuery support](http://framework.zend.com/manual/en/zendx.jquery.html), and
[Twitter support](http://framework.zend.com/manual/en/zend.service.twitter.html),
among numerous other offerings.

For this particular release, we tried very hard to leverage the community. The
majority of new features present in 1.7.0 are from community proposals, or were
primarily driven by community contributors. For me, this represents a
milestone: ZF is now at a stage where fewer and fewer core components are
necessary, and the community is able to build off it and add extra value to the
project.

<!--- EXTENDED -->

On that note, this release also marks the first release containing the Extras
library — a repository of community driven components that will not be
officially supported by Zend, but which must also pass ZF's strict guidelines
for submission (> 80% test coverage, fully documented, and reviewed by the
internal team). We hope that this repository continues to expand and show off
the diverse interests of our contributors.

In particular, I'm quite proud of the jQuery support. From the moment we first
announced our partnership with [Dojo](http://dojotoolkit.org), we messaged that
we while we would officially support Dojo in the framework, we would also allow
community contributions for integration with other frameworks.
[Benjamin Eberlei](http://www.whitewashing.de/blog/) drove this component from
proposal to implementation, and communicated often with me to ensure that it
would provide a story consistent with our Dojo integration. I think jQuery
users will be pleased with the results.

Besides providing a wealth of new components for the release, the community
also stepped up to help resolve bugs in the framework. We held a general bug
hunt week the week of 3 November 2008, in which we resolved approximately 100
issues. Additionally, [phpGG](http://www.phpgg.nl) and
[PHPBelgium](http://www.phpbelgium.be) banded together to start the
[Bug Hunt Day](http://bughuntday.org/) initiative, and held their first event
on 8 November 2008 — dedicated to fixing Zend Framework bugs. While we had but
a dozen issues closed during that event, I anticipate that such initiatives in
the future will bring more people to the project, and help increase the overall
quality of all projects they target. My hearty thanks to all participants
involved!

My primary involvement in this release was coordinating the bug hunts, as well
as working on performance benchmarking and profiling. I'll blog on this topic
more in the future, but I found some areas where ZF can be tuned very
efficiently and concisely to bring some significant performance gains to your
applications. I have begun writing a [Performance Guide](http://framework.zend.com/manual/en/performance.html)
appendix to the manual, and you can look for updates to that in upcoming
releases.

So, [grab 1.7.0 today](http://framework.zend.com/download/latest), and start
enjoying the new features!
