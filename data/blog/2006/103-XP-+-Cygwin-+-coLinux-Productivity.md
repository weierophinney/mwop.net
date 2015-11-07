---
id: 103-XP-+-Cygwin-+-coLinux-Productivity
author: matthew
title: 'XP + Cygwin + coLinux == Productivity'
draft: false
public: true
created: '2006-01-05T16:57:00-05:00'
updated: '2006-01-11T14:58:23-05:00'
tags:
    - php
    - programming
    - linux
---
I wrote earlier of my experiences [using Windows XP](/blog/101-Using-Windows-XP.html), a move I've considered somewhat unfortunate but necessary. I've added a couple more tools to my toolbox since that have made the environment even better.

<!--- EXTENDED -->

A co-worker told me about [coLinux](http://colinux.sourceforge.net/), a port of the linux kernel that allows it to run side-by-side with Windows on the same machine. It's kind of like vmware, only more optimized, and free. I'd looked at it, but was a bit daunted as I wanted to try and use my existing Ubuntu install with it, and was worried about messing up the machine.

I finally came to the realization, however, that I simply won't be using linux as my day-to-day OS until some of my tools are ported. So, I blew away my ubuntu install and made room for coLinux.

I'd heard that it was difficult to setup, but I found it fairly easy — download the coLinux tools, get a filesystem image, add the service, fire it up. You then need to do a few other things — bridge your network interface with the coLinux network interface, set your network IP for the coLinux install, setup your root password and any new users you want — but then it's running. You can then use [Cygwin](http://www.cygwin.com/) to SSH into the install.

The basic coLinux filesystem is Debian, and based on an old Sid version. It is very stripped down, and has no developer tools. I had to `apt-get` a ton of stuff — `gcc`, `cpp`, `cvs`, `subversion`, `darcs`, `libtool`, some development libraries, etc — so I could start compiling things. I compiled Vim by hand, because if you want Vim with perl support in Debian, it insists on installing a ton of X related stuff. I then compiled Apache2, PHP4, and PHP5 by hand (and needed to get additional development libraries for some features I wanted). But the compiles worked flawlessly, and I now have coLinux running on the machine with a flexible development environment that I control.

(I've also figured out a way to run PHP4 and PHP5 seemingly on the same Apache install, side-by-side, but that's a topic for another day.)

While you can access the system via SSH, I find that's not terribly convenient for doing simple things like editing files. So I installed Samba in my coLinux install, and set it up with a few shares. With that in place, I can now access files directly from Windows — editing them in gVim, etc.

I setup [Exim](http://www.exim.org/) via cygwin. However, I noticed when I'd try and send emails from my coLinux install via the cygwin exim, exim typically errored — usually an inability to fork a process. So I installed it via coLinux instead, and all is hunky dory — my PHP scripts can now send mail, and I have a local SMTP server for queuing and sending mail instead of having to rely on the company or personal mail server.

In reading on the coLinux site, I discovered that you can setup programs that utilize esd, and run esd off of cygwin. This has allowed me to once again use [mpd](http://musicpd.org/) as my preferred music player.

Since I'm constantly going into my coLinux install, I created a copy of the `cygwin.bat` script that adds a `-c "ssh myname@myCoLinuxInstall"` to the bash command; this allows me to click on a single icon in order to SSH into coLinux — very handy.

All-in-all, I now have what I consider to be the best of both worlds — access to the work programs I need, ease of configuration for a variety of tools (wireless, bluetooth, USB devices), and a robust server/development environment — all on the same box.
