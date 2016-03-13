---
id: 120-Serendipity-upgrade
author: matthew
title: 'Serendipity upgrade'
draft: false
public: true
created: '2006-06-17T23:25:52-04:00'
updated: '2006-06-17T23:25:52-04:00'
tags:
    - programming
    - php
---
I upgraded [Serendipity](http://www.s9y.org/) today, due to the recent announcement of the 1.0 release, as well as to combat some rampant issues with trackback spam.

I've been very happy with Serendipity so far; it just runs, and the default install gives just what you need to get a blog up and running, and nothing more; any extra functionality comes via plugins which you, the blogger, get to decide upon.

Additionally, it's incredibly easy to upgrade. Unpack the tarball, rsync it over your existing install (I rsync it, because I don't use 'serendipity' as my directory name), visit the admin, boom, you're done. I've upgraded several times, and never lost data, nor configuration settings.

My primary reason for the upgrade was, as noted earlier, to combat trackback spam. As of this morning, I had 15,000 pending trackbacks, none of which appeared to be valid (if any of them were, and you're not seeing yours, I'm very sorry; I deleted them *en masse*). These had accumulated in *less than a month* — that's an average of about one every 3 minutes.

Since upgrading, and using the [Akismet](http://akismet.com/) service, I've received not a single spam trackback. Needless to say, I'm happy I performed the upgrade!

If you're a Serendipity user, and haven't upgraded to 1.0.0 yet (or one of it's reportedly very stable release candidates), do it today — you have nothing to lose, and a lot of lost time to gain!
