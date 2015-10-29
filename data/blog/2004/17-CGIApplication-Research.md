---
id: 17-CGIApplication-Research
author: matthew
title: 'CGI::Application Research'
draft: false
public: true
created: '2004-01-23T21:59:02-05:00'
updated: '2004-09-20T13:26:14-04:00'
tags:
    - programming
    - perl
    - personal
---
I've been wanting to redevelop my home website for some time using
`CGI::Application`. The last time I rewrote it from PHP to perl, I developed
something that was basically a subset of the things `CGI::App` does, and those
things weren't done nearly as well.

The problem I've been running into has to do with having sidebar content, and
wanting to run basically a variety of applications. I want to have a
WikiWikiWeb, a photo gallery, some mail forms, and an article database/blog;
`CGI::App`-based modules for each of these all exist. But I want them all to
utilize the same sidebar content, as well — and that sidebar content may vary
based on the user.

My interest got sparked by [this node](http://www.perlmonks.org/index.pl?node_id=320946) on
[Perl Monks](http://www.perlmonks.org). The author tells of an acquaintance who goes
by the rule that a `CGI::App` should have 10-12 states at most; more than that,
and you need to either break it apart or rethink your design. And all `CGI::App`s
inherit from a common superclass, so that they share the same DB connections,
templates, etc.

So, I've been investigating this problem. [One node on PM](http://www.perlmonks.org/index.pl?node_id=229260)
notes that his ISP uses `CGI::App` with hundreds of run modes spread across
many applications; they created a module for session management and access
control that calls `use base CGI::Application`; each aplication then calls
`use base Control`, and they all automatically have that same session
management and access, as well as `CGI::Application`.

[Another node](http://www.perlmonks.org/index.pl?node_id=94879) mentions the
same thing, but gives a little more detail. That author writes a module per
application, each inheriting from a super class: `UserManager.pm`, `Survey.pm`,
`RSS.pm`, `Search.pm`, etc. You create an API for that super class, and each
`CGI::App` utilizes that API to do its work.

This also seems to be the idea behind [CheesePizza](http://cheesepizza.venzia.com),
a `CGI::App`-based framework for building applications. (All pizzas start out
as cheese pizzas; you simply add ingredients.) The problem with that, though,
is that I have to learn another framework on top of `CGI::App`, instead of
intuiting my own.

But how do I write the superclass? Going back to the original node that sparked
my interest, I found a [later reply](http://www.perlmonks.org/index.pl?node_id=321064) that described how you
do this. The big key is that you override the `print` method — this allows you
to customize the output, and from here you could call functions that create
your sidebar blocks, and output the content of the `CGI::App` you just called in
a main content area of your template.

Grist for the mill…
