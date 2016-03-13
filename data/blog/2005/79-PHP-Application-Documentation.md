---
id: 79-PHP-Application-Documentation
author: matthew
title: 'PHP Application Documentation'
draft: false
public: true
created: '2005-06-14T23:07:57-04:00'
updated: '2005-06-15T17:31:53-04:00'
tags:
    - php
---
[Paul Jones](http://www.paul-m-jones.com/) has written
[an interesting piece about documentation in the PEAR project](http://www.paul-m-jones.com/blog/?p=153),
in which he argues very convincingly for using wikis for end user
documentation.

I actually think that last point bears repeating: *using wikis for end user
documentation*. I talked to Paul about this issue at php|Tropics, and both of
us use phpDocumentor quite religiously. However, API documentation is very
different from end user documentation. And the issue with documentation at the
PEAR project has to do with the fact that there are many projects with little
or no end user documentation — which often makes it difficult for a developer
to determine how a module might be used.

The often-cited barrier for this is that end user documentation on the PEAR
website must be done in DocBook format.

<!--- EXTENDED -->

For the record, I **hate** maintaining DocBook format. I created the tutorials
for Cgiapp using it, and I dread having to go back into them to update or add
new sections.

Why? Well, for starters:

1. I don't use XML on a daily basis. If I need to deal with XML, I typically
   create a template and have a script fill it. Or I use a parser. But I don't
   write it by hand.
2. DocBook doesn't use the same tagset as HTML. This means that I have to try
   and remember different tags, and which work in which arena.
3. Related to (2) is that because the actual tags available can vary based on
   the DTD, VIM doesn't have keystroke macros to create the begin/close tags. This
   is a feature I use in editing HTML daily, and which speeds up my writing time.
   So, writing DocBook is slower than writing HTML (or plain text) by a
   significant factor. (Yes, I could write VIM macros for often used tags, but
   then I'd need to learn more about VIM scripting, and who has the time?)
4. I already know and use HTML on a daily basis. I use plain text on a daily
   basis (did I mention I use VIM?). I'm comfortable in these environments. Why
   would I use anything else?

But I think the point that is often overlooked is that *PHP was written to
create web pages*. Let that sink in for a moment. Why would a PHP project
encourage writing documentation in anything other than the language of the web,
HTML? Indeed, why would it *discourage* writing web-ready documentation?

As Paul noted, while wikis may not be great for all documentation purposes,
they're more than adequate for *most*. In most projects, you're not going to
need tables to document the project; several levels of headings, paragraphs,
and some list elements will do the trick. Wikis can do all of these things. And
they allow these things to be done easily, and for the results to be instantly
available (rather than on a once-daily basis, as is the case for the PEAR web
documentation, which must be compiled from DocBook).

My suggestion? Since DocBook can be exported to almost anything, export the
PEAR documentation to a wiki format, and then use wikis for all but the most
complex documentation. Do the complex docs in HTML, or, if you *really* feel
the need for an output-agnostic format, use DocBook then. (My guess is that if
the above were implemented, we wouldn't see much DocBook after more than a
year.)

Maybe by reducing the barrier to creating usable end-user documentation, we'll
start seeing a proliferation of documentation on PEAR to augment the great code
that's been appearing there.
