---
id: 25-Design-Ideas
author: matthew
title: 'Design Ideas'
draft: false
public: true
created: '2004-02-04T22:43:14-05:00'
updated: '2004-09-20T13:40:04-04:00'
tags:
    - programming
    - perl
    - personal
---
I had some success last night with the `My::Portal` `CGI::Application` superclass I'm building — I actually got it working with `CGI::Wiki::Simple` (after I debugged the latter to fix some delegation issues!). Now that I know the "proof-of-concept" works, I'm ready to start in on some other issues.

The first issue is: how can I specify different directories for different applications to search for templates, while retaining the default directory so that the superclass can build the final page? I *could* always simply keep all templates in a single directory and simply prefix them, but that seems inelegant, somehow. I'll need to explore how HTML::Template integration works with CGI::App.

Second, and closely related: how do I want it to look, in the end? I could see keeping the design we have — it's clean, simple, and yet somehow functionally elegant. Okay, I'm exaggerating — it's your standard three-column with header and footer. But it goes with the idea of blocks of content. I need to think about that.

I saw a design idea for a WikiWikiWeb today, though, that totally changed my ideas of how a Wiki should look. I hadn't been to [Wikipedia](http://en.wikipedia.org) for some time, but a Google link to Gaston Julia showed up on Slashdot as it shut down a site in Australia, and so I visited it. I *like* the new design — it separates out the common links needed into a nice left menu, and puts a subset of that at the top and bottom of the main column as well, using nice borders to visually separate things. I much prefer it to PhpWiki's default style, as well as to anything else I've really seen so far relating to Wiki layout.
