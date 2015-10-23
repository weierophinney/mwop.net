---
id: 49-Cgiapp-1.5.1-released
author: matthew
title: 'Cgiapp 1.5.1 released'
draft: false
public: true
created: '2004-11-04T13:09:59-05:00'
updated: '2004-11-04T13:10:22-05:00'
tags:
    - personal
    - php
---
At work this week, I discovered a bug with how I was calling
`set_error_handler()` in Cgiapp's `run()` method. Evidently passing a reference
in a PHP callback causes issues! So, I corrected that.

I also made a minor, one-character change to `query()` to make it explicitly
return a reference to the `$_CGIAPP_REQUEST` property array.

You can see full details at the [Cgiapp download page](download?mode=view&id=11).
