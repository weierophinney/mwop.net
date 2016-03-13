---
id: 2014-11-03-utopic-and-amd
author: matthew
title: 'Fixing AMD Radeon Display Issues in Ubuntu 14.10'
draft: false
public: true
created: '2014-11-03T14:15:00-06:00'
updated: '2014-11-03T14:15:00-06:00'
tags:
    - ubuntu
    - linux
---
After upgrading to [Ubuntu 14.10](https://wiki.ubuntu.com/UtopicUnicorn/ReleaseNotes),
I faced a blank screen after boot. As in: no GUI login prompt, just a blank
screen. My monitors were on, I'd seen the graphical splash screen as Ubuntu
booted, but nothing once complete.

Fortunately, I *could* switch over to a TTY prompt (using Alt+F1), so I had
some capacity to try and fix the situation. The question was: what did I need
to do?

<!--- EXTENDED -->

Go Back To Basics
-----------------

While the Linux kernel was recognizing my Radeon 6750, and even the X server
had no problems detecting it and setting it up, I still faced a display issue.
Fortunately, there's a fix for that: remove the proprietary drivers.

The steps for removing the proprietary drivers are as follows:

```bash
$ sudo apt-get purge 'fglrx*'
$ sudo update-alternatives --remove-all x86_64-linux-gnu_gl_conf
$ sudo apt-get install --reinstall libgl1-mesa-dri libgl1-mesa-glx
```

Some people will tell you then to reinstall the fglrx drivers Ubuntu ships, or
even the "fglrx-updates" set, but I found it best to go all the way back to
basics.

After executing the above steps, reboot so that they drivers are present in the
kernel.

Once you do, you can try your luck with the proprietary drivers, using the
"Additional Drivers" tool built into Ubuntu. I personally found that neither
the proprietary fglrx drivers, fglrx-updates, nor the official AMD Catalyst
sources worked — and, after each failed attempt, I'd run the above to get back
to a working state.

My conclusion is that the proprietary drivers are likely not yet tested with
the kernel sources currently in use by 14.10. Fortunately, the OSS variants
with which Ubuntu ships appear to be quite stable, and cover all the features
that the proprietary versions covered previously.

As always with a post like this: your mileage may vary. Hopefully the steps
above will help at least a few of you; they worked for me on both my
workstation and laptop.
