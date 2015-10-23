---
id: 48-Cgiapp-1.5-released
author: matthew
title: 'Cgiapp 1.5 released'
draft: false
public: true
created: '2004-10-28T14:27:41-04:00'
updated: '2004-12-31T23:03:16-05:00'
tags:
    - personal
    - php
---
Cgiapp 1.5 has been released; you may now [download it](download?mode=view_download&id=11).

This release fixes a subtle bug I hadn't encountered before; namely, when a
method name or function name is passed as an argument to `mode_param()`, `run()`
was receiving the requested run mode… and then attempting to process that as the
mode param. The behaviour is now fixed, and is actually simpler than the
previous (non-working) behaviour.

Also, on reading [Chris Shiflet's](http://shiflett.org) paper on PHP security, I
decided to reinstate the `query()` method. I had been using `$_REQUEST` to check
for a run mode parameter; because this combines the `$_GET`, `$_POST`, **and**
`$_COOKIE` arrays, it's considered a bit of a security risk. `query()` now
creates a combined array of `$_GET` and `$_POST` variable (`$_POST` taking
precedence over `$_GET`) and stores them in the property `$_CGIAPP_REQUEST`; it
returns a reference to that property.  `run()` uses that property to determine
the run mode now.

Enjoy!
