---
id: 158-Backwards-Compatibility
author: matthew
title: 'Backwards Compatibility'
draft: false
public: true
created: '2008-02-07T09:56:11-05:00'
updated: '2008-02-07T09:56:11-05:00'
tags:
    - programming
    - php
    - 'zend framework'
---
[Ivo](http://jansch.nl/) already pointed this out, but I want to point it out
again: Boy Baukema writes [a very nice entry regarding backwards compatibility](http://www.ibuildings.nl/blog/archives/541-Backward-compatibility,-bane-of-the-developer.html)
on the ibuildings.nl corporate blog.

Backwards compatibility (BC) is a tricky thing to support, even when you strive
hard to, as Boy puts it, "think hard about your API" prior to release. Somebody
will always come along and point out ways it could have been done better or ways
it could be improved. I've had to wrestle with these issues a ton since joining
the Zend Framework team, and while it often feels like the wrong thing to do to
tell somebody, "too little, too late" when they have genuinely good feedback for
you, its often in the best interest of the many users already using a component.

I had the pleasure of meeting Boy last year when visiting the ibuildings.nl
offices, and he's got a good head on his shoulders. He does a nice job outlining
the issues and a number of approaches to BC; if you develop a project for public
consumption, you should definitely head over and read what he has to say.
