---
id: 34-POD-for-PHP
author: matthew
title: 'POD for PHP'
draft: false
public: true
created: '2004-03-28T19:33:39-05:00'
updated: '2004-09-20T13:53:49-04:00'
tags:
    - perl
    - personal
    - php
---
I was lamenting at work the other day that now that I've discovered OO and
templating with PHP, the only major feature missing for me is a way to easily
document my programs. I'm a big fan of perl's POD, and use it fairly
extensively, even for simple scripts — it's a way to provide a quick manual
without needing to worry too much about how to format it.

So, it hit me on the way home Friday night: what prevents me from using POD in
multiline comments of PHP scripts? I thought I'd give it a try when I got home.

First I googled for 'POD for PHP', and found a link to perlmongers where
somebody recounted seeing that exact thing done, and how nicely it worked.

Then I tried it… and it indeed worked. So, basically, I've got all the tools I
love from perl in PHP, one of which is borrowed directly from the language!
