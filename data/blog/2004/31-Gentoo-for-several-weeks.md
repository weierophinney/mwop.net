---
id: 31-Gentoo-for-several-weeks
author: matthew
title: 'Gentoo for several weeks'
draft: false
public: true
created: '2004-04-22T22:10:10-04:00'
updated: '2004-09-20T13:50:08-04:00'
tags:
    - linux
    - personal
---
I've had a bunch of problems with my new computer — it uses ACPI, but if I load
the ACPI modules, it won't boot; if I don't load them, I have to go through
contortions to get the ethernet working, and it won't power down; and a bunch of
other little stuff.

So, a few weeks ago, I thought, what the heck? Why not try
[Gentoo](http://www.gentoo.org)? I've been reading about it since it first came
out, and I remember talking with Duane about it once, and it has a reputation
for both being cutting edge and stable. Heck, even Wil Wheaton's endorsing it…
it can't be **all** bad, right?

I had a few misstarts — bad CDs, not quite getting how the chroot thing worked,
problems with DNS (which I *still* don't understand; and Rob has them as well,
so it's not just me). But once I got it installed… well, I'm impressed.

The thing about Gentoo is, it *compiles* everything from source. It's like
[Debian](http://www.debian.org), in that it fetches all dependencies and
installs those… but it's all source. So it's not exactly fast. But because
everything is compiled, and because you setup C flags specific to your machine,
what you get is incredibly optimized for your own machine. This 1.6GHz machine
simply flies. And the memory usage *just stays low*.

I'd like to use it for my server… but I can't really take the server down at
this point when it's making both my mom and myself money. But what a great
system… I only wish I'd used it for the mail server at work.
