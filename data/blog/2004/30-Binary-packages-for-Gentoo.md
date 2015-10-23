---
id: 30-Binary-packages-for-Gentoo
author: matthew
title: 'Binary packages for Gentoo'
draft: false
public: true
created: '2004-04-27T22:48:53-04:00'
updated: '2004-09-20T13:48:36-04:00'
tags:
    - linux
    - personal
---
I'd read that you could get binary packages for gentoo, thus alleviating the
need to compile everything. (Of course, then you lose some of the benefits of
compiling everything, but you gain in speed…) Unfortunately, I mistook this
with ebuilds, and never quite figured it out.

The key is to throw the `-g` flag:

```bash
$ emerge -g gnumeric # which is like 'emerge --getbinpkg gnumeric'
```

I also learned how to update packages tonight:

```bash
$ emerge sync             # to sync your mirror with the gentoo mirrors
$ emerge --update portage # if necessary
$ emerge --update system  # updates core system files
$ emerge --update world   # updates all packages
```
