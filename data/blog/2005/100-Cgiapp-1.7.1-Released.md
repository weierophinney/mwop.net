---
id: 100-Cgiapp-1.7.1-Released
author: matthew
title: 'Cgiapp 1.7.1 Released'
draft: false
public: true
created: '2005-11-30T22:32:00-05:00'
updated: '2005-11-30T22:33:39-05:00'
tags:
    - php
---
I was able to roll a long-needed (and by some, long awaited) bugfix release of Cgiapp this morning. Cgiapp 1.7.1 corrects the following issues:

- `Cgiapp5::run()` was corrected to call `query()` instead of `cgiapp_get_query()` (which caused a fatal error)
- `Cgiapp::__call()` and `Cgiapp5::__call()` now report the name of the method called in errors when unable to find matching actions for that method.

As usual, downloads are available [on my site](/matthew/download) as well as [via SourceForge](http://prdownloads.sourceforge.net/cgiapp/Cgiapp-1.7.1.tgz?download).

**Update:** The link on my site for downloading Cgiapp has been broken; I've now fixed it.
