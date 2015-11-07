---
id: 62-pear-channels-and-frameworks
author: matthew
title: 'PEAR, Channels, and Frameworks'
draft: false
public: true
created: '2005-04-11T21:32:20-04:00'
updated: '2005-04-11T21:55:40-04:00'
tags:
    - php
---
[Greg Beaver writes in his blog](http://greg.chiaraquartet.net/comment.php?type=trackback&entry_id=38)
about PEAR, the new PEAR channels, and some issues he sees with PEAR and its
developers. Greg is responsible for the latest version of PEAR and the PEAR
installer — and for the development of PEAR channels. The particular link
referenced above makes reference to a thread on the PEAR-dev mailing list…
that I originated, when asking whether or not Cgiapp might be a good fit for
PEAR.

<!--- EXTENDED -->

I've been following with some interest Greg's development of channels, but for a
while couldn't see quite what the point was… until pearified.net started
offering Smarty via a pear channel. Installing Smarty via the PEAR installer is
incredibly simple — and points towards a great method of distribution of PHP
code.

Greg is absolutely right in his post — the best thing about PEAR is the
installer. However, there's some wonderful code in the repository as well. I
couldn't do my job without the likes of Log, DB, Cache_Lite, Pager, and others;
they provide the little pieces that make a large job into a few lines of code.

However, I've also been observing the pear-dev list for over six months now,
while trying to decide whether or not to propose some of my own code for
inclusion. I feel that the code I have is definitely in the spirit of PEAR —
good, reusable, extensible, glue code that can be used for a variety of
projects. However, it also falls under an umbrella that appears to be anathema
to many PEAR developers: the framework. And that's how the whole thread
exploded.

I still think Cgiapp would be a good fit for PEAR. However, I am not going to
consider it at this time, for several reasons:

- I don't have the time or energy to argue why I think Cgiapp would fit. If I
  thought it would be an easy argument, or that it would simply involve tweaking
  Cgiapp slightly, I'd do it in an instant. But from the comments I've read in
  response to my query, it sounds like some very core and vocal members of PEAR
  simply feel frameworks of any sort are not PEAR's territory, and I think they
  would lobby effectively against the proposal.
- I truly feel that Cgiapp should stay as true as possible to its Perl
  predecessor. I want the APIs to be the same, and I want it to develop in the
  same direction. I suspect that if I were to go through the PEAR proposal
  process, I'd have to lose this integrity in order for it to pass muster.
- With the advent of PEAR channels in the upcoming 1.4.0 release, there's no
  reason I couldn't set up a PEAR channel of my own on the sourceforge site —
  or join pearified.net. I think Greg hit the nail on the head here: channels
  open up possibilities for PHP developers, and particularly for PHP
  developers who may not want to or have the time to go through the PEAR
  proposal process — or who are offering packages that fall outside PEAR's
  scope. The PEAR installer, coupled with channels, creates an incredible
  distribution channel.

I have the utmost respect for PEAR, and I've seen it advance tremendously in the
past year; as mentioned above, I couldn't do my job nearly as well or
effectively without the tools PEAR provides. However, I simply don't see how
Cgiapp could possibly thrive in PEAR at this time. I think Greg's admonishment
to his fellow PEAR devs should definitely be heeded. PEAR needs to look beyond
itself if it wishes to attract new and talented PHP developers.
