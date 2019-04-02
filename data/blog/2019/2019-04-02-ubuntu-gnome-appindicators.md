---
id: 2019-04-02-ubuntu-gnome-appindicators
author: matthew
title: 'Fixing gnome-shell app indicators in Ubuntu'
draft: false
public: true
created: '2019-04-02T08:30:00-06:00'
updated: '2019-04-02T08:30:00-06:00'
tags:
    - linux
    - ubuntu
    - gnome-shell
---

I am a long-time [gnome-shell](https://wiki.gnome.org/Projects/GnomeShell) user.
I appreciate the simplicity and elegance it provides, as I prefer having a
minimalist environment that still provides me easy access to the applications I
use.

That said, just as with any desktop environment, I've still run into problems
now and again. One that's been plaguing me since at least the 18.04 release is
with display of app indicators, specifically those using legacy system tray
APIs.

Normally, gnome-shell ignores these, which is suboptimal as a number of popular
programs still use them (including Dropbox, Nextcloud, Keybase, Shutter, and
many others). To integrate them into Gnome, Ubuntu provides the gnome-shell
extension "kstatusnotifieritem/appindicator support" (via the package
`gnome-shell-extension-appindicator`). When enabled, they show up in your
gnome-shell panel. Problem solved!

Except that if you suspend your system or lock your screen, they disappear when
you wake it up.

Now, you can get them back by hitting `Alt-F2`, and entering `r` (for "restart")
at the prompt. But having to do that after every time you suspend or lock is
tedious.

Fortunately, I recently came across this gem:

```bash
$ sudo apt purge indicator-common
```

This removes some packages specific to Ubuntu's legacy Unity interface that
interfere with how appindicators are propagated to the desktop. Once I did this,
my appindicators persisted after all suspend/lock operations!
