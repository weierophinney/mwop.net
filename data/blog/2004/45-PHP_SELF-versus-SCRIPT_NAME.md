---
id: 45-PHP_SELF-versus-SCRIPT_NAME
author: matthew
title: 'PHP_SELF versus SCRIPT_NAME'
draft: false
public: true
created: '2004-10-12T21:34:57-04:00'
updated: '2004-10-12T21:35:03-04:00'
tags:
    - perl
    - personal
    - php
---
I've standardized my PHP programming to use the environment variable
`SCRIPT_NAME` when I want my script to refer to itself in links and form
actions. I've known that `PHP_SELF` has the same information, but I was more
familiar with the name `SCRIPT_NAME` from using it in perl, and liked the feel
of it more as it seems to describe the resource better (`PHP_SELF` could stand
for the path to the PHP executable if I were to go by the name only).

However, I just noticed a post on the php.general newsgroup where somebody asked
what the difference was between them. Semantically, there isn't any; they should
contain the same information. However, historically and technically speaking,
there is. `SCRIPT_NAME` is defined in the CGI 1.1 specification, and is thus a
standard. *However*, not all web servers actually implement it, and thus it
isn't necessarily *portable*. `PHP_SELF`, on the other hand, is implemented
directly by PHP, and as long as you're programming in PHP, will always be
present.

Guess I have some grep and sed in my future as I change a bunch of scripts…
