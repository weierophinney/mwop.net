---
id: 12-PHP-standards-ruminations
author: matthew
title: 'PHP standards ruminations'
draft: false
public: true
created: '2004-01-22T00:16:49-05:00'
updated: '2004-09-13T23:14:50-04:00'
tags:
    - personal
    - php
---
I've been thinking about trying to standardize the PHP code we do at work. Rob and I follow similar styles, but there are some definite differences. It would make delving into eachother's code much easier if we both followed some basic, agreed upon, guidelines.

One thing I've been thinking about is function declarations. I find that I'm often retooling a function to make it more general, and in doing so either need to decrease or increase the number of arguments to it. This, of course, breaks compatability.

So I propose that we have all functions take two arguments: `$data` and `$db`. `$data` is a hash which can then be `extract`'d via PHP. To change the number of arguments, you can simply set defaults for arguments or return meaningful errors for missing arguments.

Another thought going through my mind deals with the fact that we reuse many of our applications across our various sites, and also export some of them. I think we should try and code the applications as functional libraries or classes, and then place them somewhere in PHP's include path. We can then have a "demo" area that shows how to use the libraries/classes (i.e., example scripts), and to utilize a given application, we need simply include it like: `include 'apps/eventCalendar/calendar.inc';`. This gives us maximum portability, and also forces us to code concisely and document vigorously.

I was also reading on php.general tonight, and noticed some questions about PHP standards. Several people contend that PEAR is becoming the de facto standard, as it's the de facto extension library. In addition, because it is becoming a standard, there's also a standard for documenting projects, and this is phpdocumenter. The relevant links are:

- [PEAR Coding Standards](http://pear.php.net/manual/en/standards.php)
- [phpDocumentor](http://www.phpdoc.org/)
