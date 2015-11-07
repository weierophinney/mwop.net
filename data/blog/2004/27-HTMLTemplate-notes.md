---
id: 27-HTMLTemplate-notes
author: matthew
title: 'HTML::Template notes'
draft: false
public: true
created: '2004-02-05T20:18:20-05:00'
updated: '2004-09-20T13:44:23-04:00'
tags:
    - programming
    - perl
    - personal
---
I've used `HTML::Template` a little, mainly in the Secret Santa project I did
this past Christmas for my wife's family. One thing I disliked was using the
normal syntax: `<TMPL_VAR NAME=IMAGE_SRC>` — it made looking at it difficult (it
wasn't always easy to tell what was an HTML tag, what was plain text, and what
was `HTML::Template` stuff), and it made it impossible to validate my pages
before they had data.

Fortunately, there's an alternate syntax: wrap the syntax in HTML comments:
`<!-- TMPL_VAR NAME=IMAGE_SRC -->` does the job. It uses more characters, true,
but it gets highlighted different than HTML tags, as well, and that's worth a
lot.

And why do I have to say "NAME=" every time? That gets annoying. As it turns
out, I can simply say: `<!-- TMPL_VAR IMAGE_SRC -->`, and that, too will get the
job done.

Finally, what about those times when I want to define a template, but have it
broken into parts, too? Basically, I want `HTML::Template` to behave a little
like SSI. No worries; there's a `TMPL_INCLUDE` tag that can do this: `<!--
TMPL_INCLUDE NAME="filename.tmpl" -->`.
