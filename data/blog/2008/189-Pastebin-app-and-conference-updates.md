---
id: 189-Pastebin-app-and-conference-updates
author: matthew
title: 'Pastebin app and conference updates'
draft: false
public: true
created: '2008-09-09T10:19:23-04:00'
updated: '2008-09-16T01:15:30-04:00'
tags:
    0: dojo
    1: php
    3: phpworks08
    4: webinar
    5: 'zend framework'
    6: zendcon08
---
I have a number of updates and followups, and decided to post them in a single
entry.

First off, you may now [view my Dojo Webinar online](http://www.zend.com/en/webinar/Framework-Dojo/Webinar-Rec-Framework-Dev-EN-ZFDojo-20080903.flv)
(requires login and registration at zend.com). Attendance was phenomenal, and
I've had some really good feedback. If you want to see it live, I'm giving the
talk (with revisions!) at the [ZendCon](http://www.zendcon.com/) UnConference,
at [Dojo Developer Day Boston](http://dojotoolkit.org/2008/07/10/dojo-developer-day-boston)
later this month, and at [php|works](http://phpworks.mtacon.com/c/schedule/talk/d1s5/1) in
November. I hope to be able to show new functionality at each presentation.

Second, I've completed what I'm calling version 1.0.0 of the pastebin
application I demo'd in the webinar. The PHP code is fully unit tested (though
I haven't yet delved into using DOH! to test the JS), and incorporates a number
of best practices and tips that Pete Higgins from Dojo was kind enough to
provide to me. When using a custom build (and I provide a profile for building
one), it simply flies.

- [Download the pastebin application](/uploads/pastebin-1.0.0.tar.gz)

The pastebin application showcases a number of features besides Dojo:
`Zend_Test_PHPUnit` was used to test the application, and `Zend_Wildfire`'s
FireBug logger and DB profiler are used to provide profiling and debug
information.

Finally, [ZendCon](http://www.zendcon.com/) is next week! I'll be around, but
already have a packed schedule (1 tutorial, 2 regular sessions, an UnCon
session, a meet-the-developers session… and that's just what I know about!).
I look forward to meeting ZF users and developers, though, so feel free to grab
me and introduce yourself.
