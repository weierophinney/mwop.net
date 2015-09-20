---
id: 109-Cgiapp-1.9.0-released
author: matthew
title: 'Cgiapp 1.9.0 released'
draft: false
public: true
created: '2006-05-02T19:01:00-04:00'
updated: '2006-05-02T10:04:28-04:00'
tags:
    - php
---
I released Cgiapp 1.9.0 into the wild last night. The main difference between 1.8.0 and 1.9.0 is that I completely removed the plugin system. I hadn't had any users reporting that they were using it, and, in point of fact, the overloading mechanism I was using was causing some obscure issues, particularly in the behaviour of `cgiapp_postrun()`.

As usual, you can find more information and links to downloads [at the Cgiapp site.](http://cgiapp.sourceforge.net/)
