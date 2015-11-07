---
id: 57-New-Cgiapp-Site
author: matthew
title: 'New Cgiapp Site'
draft: false
public: true
created: '2005-02-19T14:38:08-05:00'
updated: '2005-03-25T22:51:18-05:00'
tags:
    - php
    - programming
    - personal
---
I've been extremely busy at work, and will continue to be through the end of
March. I realized this past week that I'd set a goal of having a
[SourceForge](http://sourceforge.net) website up and running for Cgiapp by the
end of January — and it's now mid-February. Originally, I was going to backport
some of my libraries from PHP5 to PHP4 so I could do so… and I think that was
beginning to daunt me a little.

Fortunately, I ran across a quick-and-dirty content management solution
yesterday called [Gunther](http://gunther.sourceforge.net/). It does templating
in Smarty, and uses a wiki-esque syntax for markup — though page editing is
limited to admin users only (something I was looking for). I decided to try it
out, and within an hour or so had a working site ready to upload.

Cgiapp's new site can be found at [cgiapp.sourceforge.net](http://cgiapp.sourceforge.net/).

#### UPDATE

Shortly after I wrote this original post, I figured out what the strength of
Gunther was — and why I no longer needed it. Gunther was basically taking
content entered from a form and then inserting that content (after some
processing for wiki-like syntax) into a Smarty template. Which meant that I
could do the same thing with Cgiapp and [Text_Wiki](http://pear.php.net/text_wiki).
Within an hour, I wrote an application module in Cgiapp that did just that, and
am proud to say that the Cgiapp website is 100% Cgiapp.
