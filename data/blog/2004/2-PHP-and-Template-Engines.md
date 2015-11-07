---
id: 2-PHP-and-Template-Engines
author: matthew
title: 'PHP and Template Engines'
draft: false
public: true
created: '2004-09-04T19:12:21-04:00'
updated: '2004-09-13T23:22:28-04:00'
tags:
    - personal
    - php
---
On [PhpPatterns](http://www.phppatterns.com), I recently read
[an article on Template Engines in PHP](http://www.phppatterns.com/index.php/article/articleview/4/1/1/).
It got my ire up, as it said (my interpretation):

- "template engines are a bad idea"
- "templating using PHP natively can be a good idea"
- "template engines… are not worth the text their written in"

Okay, so that's actually direct quotes from the article. I took issue with it,
immediately — I use [Smarty](http://smarty.php.net) for everything I do, and the
decision to do so was not done lightly. I have in fact been advocating the use
of template engines in one language or another for several years with the
various positions in which I've been employed; I think they are an essential
tool for projects larger than a few pages. Why?

- Mixing of languages causes inefficiency. When I'm programming, it's incredibly
  inefficient to be writing in up to four different languages: PHP or Perl,
  X/HTML, CSS, and Javascript. Switching between them while in the same file is
  cumbersome and confusing, and trying to find HTML entities buried within
  quoting can be a nightmare, even when done in heredocs. Separating the
  languages into different files seems not only natural, but essential.
- Views contain their own logic. In an MVC pattern, the final web page View
  may be dependent on data passed to it via the Controller; however, this
  doesn't mean that I want the full functionality of a language like PHP or
  Perl to do that. I should only be doing simple logic or looping constructs —
  and a full scripting language is overkill. (I do recognize, however, that
  template engines such as Smarty are written using PHP, so the language is
  being invoked regardless. What I speak of here is the language used to
  *compose the template*.)
- Abstraction and Security. The fewer internals that are divulged on the
  template page, the better. For security purposes, I may not want clients able
  to know *how* data got to the page, only what data is available to them. In
  addition, if this data is abstracted enough, any number of backends could be
  connected to the page to produce output.

So, reading the aforementioned article really got my hackles up. However, it got
me thinking, as well. One issue raised is that PHP can be used as your
templating language. While I can understand why this might be desirable —
everything from load issues to flexibility — I also feel that this doesn't give
enough abstraction.

Using PHP seems to me to be inefficient on two fundamental levels, based on my
understanding of *The Pragmatic Programmer*:

- **Domain Langauge.** *The Pragmatic Programmer* suggests that *subsets* of a
  language should be used, or wholly new mini-languages developed, that speak to
  the domain at hand. As an example, you might want to use a sharp tool to open
  a can; an axe would be overkill, but a knife might work nicely. Using PHP to
  describe a template is like using an axe to open a can; it'll do the job, but
  it may also make a mess of it all, simply because it's too much sharp edge for
  the job.
- **Metadata.** Metadata is data about data; to my thinking, templates describe
  the data they are communicating; the compiled template actually contains the
  data. In this regard, again, putting PHP into the script is overkill as doing
  so gives more than just some hints as to what the data is.

The author of the article also makes a case for teaching web designers PHP —
that the language is sufficiently easy to pick up that they typically will be
able to learn it as easily, if not more easily, than a template language. I
agree to a degree… But my experience has shown that web designers typically
struggle with HTML, let alone PHP. (Note: my experience in this regard is not
huge, and I'm sure that this is an exaggeration.) I find that it's typically
easiest for me to give an example template, explain what the funny, non-HTML
stuff can do, and let them go from there. Using this approach, they do not need
to learn anything new — they simply work with placeholders.

Still, I think the author raises some fine points. I wish he'd bothered to do
more research into *why* people choose template engines and the benefits that
arise from using them *before* simply outright slamming them. Of course, the
article is also a bit dated; it was written over two years ago, and much has
changed in the world of PHP and many of its template engines. I'm curious as to
whether they would feel the same way today.

Me? My mind is made up — the benefits, in my circumstances, far outweigh any
costs associated. I'll be using template engines, and
[Smarty](http://smarty.php.net) in particular, for years to come.
