---
id: 9-RCS-quickstart
author: matthew
title: 'RCS quickstart'
draft: false
public: true
created: '2004-01-21T16:45:22-05:00'
updated: '2004-09-10T22:37:55-04:00'
tags:
    - personal
---
Gleaned from *Linux Server Hacks*

- Create an RCS directory
- Execute a `ci -i filename`
- Execute a `co -l filename` and edit as you wish.
- Execute a `ci -u filename` to check in changes.

The initial time you checkout the copy, it will be locked, and this can cause
problems if someone else wishes to edit it; you should probably edit it once
and put in the version placeholder in comments somewhere at the top or bottom:

```
$VERSION$
```

and then check it back in with the `-u` flag to unlock it.
