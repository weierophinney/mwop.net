---
id: 32-Cgiapp-A-PHP-Class
author: matthew
title: 'Cgiapp: A PHP Class'
draft: false
public: true
created: '2004-03-30T21:57:31-05:00'
updated: '2004-09-20T13:51:28-04:00'
tags:
    - programming
    - perl
    - personal
    - php
---
After working on some OO classes yesterday for an application backend I'm
developing for work, I decided I needed to create a `BREAD` class to make this
simpler. You know, **B**rowse-**R**ead-**E**dit-**A**dd-**D**elete.

At first, I figured I'd build off of what I'd done yesterday. But then I got to
thinking (ah, thinking, my curse). I ran into the `BREAD` concept originally
when investigating `CGI::Application`; a number of individuals had developed
`CGI::Apps` that provided this functionality. I'd discarded them usually because
they provided more functionality than I needed or because they introduced more
complexity than I was willing to tackle right then.

But once my thoughts had gone to `BREAD` and `CGI::App`, I started thinking how
nice it would be to have `CGI::Application` for PHP. And then I thought, why
not? What prevents me from porting it? I have the source…

So, today I stayed home with Maeve, who, on the tail end of an illness,
evidently ran herself down when at daycare yesterday, and stayed home sleeping
most of the day. So, while she was resting, I sat down with a printout of the
non-POD code of `CGI::App` and hammered out what I needed to do. Then, when she
fell asleep for a nap, I typed it all out and started testing. And, I'm proud to
say, it works. For an example, visit
[my development site](http://dev.weierophinney.net/cgiapp/test.php) to see a
very simple, templated application in action.
