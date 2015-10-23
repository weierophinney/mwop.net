---
id: 55-Cgiapp-1.5.2-released
author: matthew
title: 'Cgiapp 1.5.2 released'
draft: false
public: true
created: '2005-01-14T00:12:03-05:00'
updated: '2005-01-14T00:12:22-05:00'
tags:
    - personal
    - php
---
At work, we've been developing a new platform for our website, based entirely on
Cgiapp. This week we released the first stage of it:
[garden.org](http://www.garden.org/home) and
[assoc.garden.org](http://assoc.garden.org). These should stand as good
testament to Cgiapp's robustness!

With all that development, and also with some communication from other Cgiapp
users, I've made some changes to Cgiapp, and release version 1.5.2 this evening.

1.5.2 is mainly security and bugfixes. Error handling was somewhat broken in
1.5.1 — it wouldn't restore the original error handler gracefully. This is now
corrected. Additionally, I've made `run()` use the array returned by `query()` —
consisting of the `$_GET` and `$_POST` arrays — in determining the run mode.
Finally, I've modified the behaviour of how `run()` determines the current run
mode: if the mode parameter is a method or function name, it cannot be a
Cgiapp method or a PHP internal function. This allows more flexibility on
the part of the programmer in determining the mode param — words like 'run' and
'do' can now be used without causing massive problems (using 'run' would cause a
race condition in the past).

As usual, Cgiapp is available [in the downloads area](download?mode=view_download&id=11). Grab your tarball today!
