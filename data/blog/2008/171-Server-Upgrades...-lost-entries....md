---
id: 171-Server-Upgrades...-lost-entries...
author: matthew
title: 'Server Upgrades... lost entries...'
draft: false
public: true
created: '2008-05-16T09:05:13-04:00'
updated: '2008-05-21T14:35:27-04:00'
tags:
    0: linux
    1: programming
    2: php
    5: ubuntu
---
My good friend, Rob, hosts my site for me, in return for helping with server
maintenance. After being on Gentoo for the past three years, though, we decided
it was time to switch to something a little easier to maintain, so last night
we wiped the system partitions and installed Ubuntu server.

I'll say this: the setup is much faster! However, we had a few gotchas that
surprised us — it didn't setup our RAID array out-of-the-box, which led to a
good hour of frustration as we tried to verify that the install wouldn't wipe
it, and then to verify that we could re-assemble it. (We succeeded.)
Additionally, we second-guessed a few things we shouldn't have, which led to
needing to back out and reconfigure. But what was over a 12 hour install with
Gentoo we accomplished in a matter of a few hours with Ubuntu server — so it
was a huge success that way.

Unfortunately, our mysqldump of all databases… wasn't, a fact we discovered
only after importing it into the new system. I ended up losing my blog database
and PEAR channel database. Fortunately, the PEAR channel has not changed at all
in the past year, so we had an old backup that worked, and I had a snapshot of
my blog database from three weeks ago I was able to use. As a result, there are
a few missing entries, but for the most part, all works. If you commented on
one of those missing entries, my apologies.

Now that the install is done, I'm also finalizing some design changes to my
blog — it's time to leave the black and white for more colorful grounds. Look
for a revamp in the coming weeks!
