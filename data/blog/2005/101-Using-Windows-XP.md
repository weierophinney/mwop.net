---
id: 101-Using-Windows-XP
author: matthew
title: 'Using Windows XP'
draft: false
public: true
created: '2005-12-10T17:18:00-05:00'
updated: '2005-12-11T05:52:00-05:00'
tags:
    - programming
---
Those of you who know me well know that I've been using Linux as my primary OS for many years now. At this point, for me, using Windows is like deciding I'm going to use a limited vocabulary; I can get the idea across, but not quite as well.

Due to the nature of where I work and the fact that I'm telecommuting, I've been having to maintain a dual-boot system. I use [Ubuntu](http://www.ubuntu.com/) for my daily OS, and boot into Windows when I need to interact with people at work via [Webex](http://www.webex.com/) or [Skype](http://www.skype.com/) (we're using the new Skype beta with video, and it only works on Windows XP at this time).

This week, however, I've had to stay on Windows quite a bit — lots of impromptu conference calls and such. So, I've been customizing my environment, and been pretty pleased with the results.

Continue reading for some tips on customizing your Windows XP environment to work and feel a little more like… linux.

<!--- EXTENDED -->

First off, one word: [Cygwin](http://www.cygwin.com/). Cygwin provides a GNU layer for Windows, including the Bash shell, and X if you need it. Of particular use are the inclusion of developer and sysadmin tools like SSH, CVS, rsync, wget, and more. This is invaluable and a must have for any developer.

Speaking of developer tools, I'm a [Vim](http://www.vim.org/) geek, and the next thing I did was download VIM for Windows. Sure, I can use vim via Cygwin, but I love having gvim available and easily launced via the [moxex plugin for Firefox](http://mozex.mozdev.org), so the Windows version is nice to have. And, since I like to use **perldo** with vim, I donwloaded [ActiveState Perl](http://www.activestate.com/Products/ActivePerl/).

The next step in my oddessey was to get my GUI environment working like I have it in Linux. My goals:

- **Sloppy Focus**. I like to be able to move my mouse over a window and have that window raise — instead of clicking into it. I was able to achieve this via [Microsoft's TweakUI PowerTools](http://www.microsoft.com/windowsxp/downloads/powertoys/xppowertoys.mspx).
- **Multiple workspaces**. If you don't know what multiple workspaces are, or haven't used them, you're missing out. If you find your workspace getting cluttered with windows, you should be using multiple workspaces. I group related windows in workspaces, and switch workspaces as I work in different projects. As an example, I might have one workspace for one project, another for another, another for using GIMP, etc. I achieved this functionality with [Virtual Dimension](http://virt-dimension.sourceforge.net/).
- **Multiple panels/panel with launchers**. On my Linux desktop, I have a top panel that includes the equivalent of a start menu, a tasklist, and the system tray. A right panel autohides and has launchers for often used programs. Windows only allows a single taskbar (equivalent of a panel), so this threw me into a quandary. Then I stumbled upon [ObjectDock](http://www.objectdock.com/). I now have an auto-hiding dock on the right side of my screen with launchers for my favorite programs
- **Ability to shade windows**. Shading windows means that the window rolls up into just the title bar. This is useful for saving screen real estate, as well as 'hiding' windows that are not currently in use, while keeping a visual cue that the window is available on the desktop. I found [FreeShade](http://www.hmmn.org/FreeShade/) for this functionality; now a double-click on the title bar rolls up the window.
- **Ability to maximize vertically or horizontally**. I rarely maximize a window, but often like to maximize a window vertically (particularly gvim or shell windows). Again, [FreeShade](http://www.hmmn.org/FreeShade/) came to the rescue; right-click on the title bar gives a menu that includes these options.
- **Switch Ctrl and Caps Lock**. I find I use Ctrl often, and Caps Lock almost never; I've never understood why Caps Lock is in a position that's so easy to hit, while Ctrl is next to impossible to hit when your fingers are in standard typing position. In Linux, it's trivial to switch the Ctrl and Caps Lock keys, and I've done so for many years. I discovered how to switch them [via an obscure site](http://www.pitt.edu/~kconover/keithbet.htm) that covered both using a Win95 kernel toy patch as well as making the change directly in the registry. I went with the Win95 Kernel Toys, which, surprising, worked fine in WinXP, and added a 'Remap' tab to the Keyboard Properties capplet.

I did a number of other tweaks. TweakUI, for instance, allows you to determine when windows are grouped in the taskbar. The system tray preferences let you choose when system tray items are hidden or displayed.

One thing I always need is a good music player. On Linux, I use [mpd](http://www.musicpd.org/) with [the pympd client](http://pympd.sourceforge.net/). I've found on Windows that [iTunes](http://www.apple.com/itunes/) is fantastic, and incredibly fast at ripping CDs to mp3 (and plays them simultaneously!).

There are several things more I want to try. For instance, I'd like to get [Postfix](http://www.postfix.org/) for Cygwin working, so I don't have to rely on an SMTP server; this will mean getting init for Cygwin working, too. I also want to figure out how to compile PHP to work under Cygwin at some point, so that I can have PHP installs that I've tailored for my needs.

A post like this isn't complete without the requisite screenshot:

![](/uploads/screenshot.jpg)

All in all, I am reluctantly finding my way around Windows, and the experience isn't too bad. Now, to get that [coLinux](http://www.colinux.org/) install going... but that's a subject for another post.
