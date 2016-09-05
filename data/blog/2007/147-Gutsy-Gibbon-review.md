---
id: 147-Gutsy-Gibbon-review
author: matthew
title: 'Gutsy Gibbon review'
draft: false
public: true
created: '2007-10-20T07:55:00-04:00'
updated: '2016-07-04T12:10:00-05:00'
tags:
    - linux
    - ubuntu
---
Early in the week, I decided to avoid the release rush and go ahead and update
my laptop to [Ubuntu's](http://www.ubuntu.com) Gutsy Gibbon release. Overall,
it's quite good, with one caveat I'll elaborate on later.

<!--- EXTENDED -->

I'd been having some issues with fonts following a session at ZendCon where I
hooked my laptop up to a widescreen display, and the updates fixed all those
issues. Most things that worked before continued to work, and often in an
improved way. The various new themes available — from GDM to GTK to window
manager themes — make for some pretty displays, and I've found a new look for
my desktop that I really like.

Among the improvements, it installed [trackerd](http://www.gnome.org/projects/tracker/),
a desktop search tool. I'd tried installing this on my own before, but ran into
a ton of dependency issues I couldn't fix. Prior to this, I'd used beagle, which
worked okay, but tended to overlook a lot of files. Trackerd, on the other hand,
indexed my entire box overnight, and stays on top of new files easily. Couple
this with the 'deskbar', and I now have the type of desktop search I've only
seen in Macs.

Last night, I stumbled upon [a forum thread](https://web.archive.org/web/20061110204904/https://help.ubuntu.com/community/CompositeManager/Xgl#head-3138701daf76c1fd11c0b68bf5745c1d1ccacca5)
detailing how to get X compositing working with ATI cards. This was something
I've been continually disappointed with; my card supposedly supports it, but
every time I've tried using it, I find it unusable — lots of wierd screen
artifacts, and a huge slowdown in responsiveness. After following the directions
in the linked article, however, I now have compositing going — window drop
shadows, translucency for inactive windows, etc. It looks really nice, and
doesn't seem to be slowing down the machine at all.

No review would be complete without a gripe though, right? And I've got a big
one. In the past, one of the strengths of ubuntu for me has been that suspend
and hibernate have just worked. With this upgrade, however, they no longer work
for me. Evidently, a new kernel option was enabled that is supposed to speed up
these operations… However, the available ATI drivers do not support this
option, which leads, in my case, to a complete inability to suspend or
hibernate, and for others, lockup on resume. Supposedly ATI will be releasing
new drivers that will fix the issue, but there's no published time frame for
when that will happen. Additionally, ubuntu made no announcements about the
issue, and provides no workarounds. To me, this is a huge BC break, and should
have been addressed prior to the release, particularly as there were many, many
complaints about it in the weeks prior to the release.

Gripes aside, I find the new functionality fantastic, and look forward to ATI's
release of new drivers for its Radeon series cards. Perhaps this release will
keep me happy enough that I won't keep lusting for a shiny new Macbook too much.

### Updates

- *2016-07-04*: Updated the link to the forum thread to point to an archived
  version on the Wayback Machine.
