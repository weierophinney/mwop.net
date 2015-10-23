---
id: 37-Making-a-RAID-array-from-the-command-line
author: matthew
title: 'Making a RAID array from the command line'
draft: false
public: true
created: '2004-03-11T21:34:18-05:00'
updated: '2004-09-20T13:58:24-04:00'
tags:
    - linux
    - personal
---
Last night, I created my first RAID array from the commandline. It was quite simple, I discovered.

1. Create your partitions using fstab. Remember, primary partitions must be created *before* extended partitions.
2. Look in `/proc/partions` and note the new partition IDs.
3. Edit `/etc/raidtab` and create a new RAID array. If unsure of the syntax, look up the [Linux Software RAID HOWTO](http://www.tldp.org/HOWTO/Software-RAID-HOWTO.html) for more details.
4. Type `mkraid /dev/md?`, where `?` is the id of the RAID device you just entered in `/etc/raidtab`.
5. Format the new RAID device with your favorite filesystem, assign it a mount point, and start using it!

I was impressed with how easy it was; the choices that the Anaconda installer present for creating a RAID array made it seem like the underlying process must be difficult, when in fact it may have been almost the same complexity if not easier.
