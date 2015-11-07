---
id: 210-Syntax-Highlighting-for-Technical-Presentations
author: matthew
title: 'Syntax Highlighting for Technical Presentations'
draft: false
public: true
created: '2009-03-10T21:02:50-04:00'
updated: '2009-03-12T19:49:24-04:00'
tags:
    - php
---
Being a technical presenter, I've often run up against the issue of how to present code snippets.

The easiest route is to simply cut-and-paste into your presentation software. However, such code is basically unreadable: it's hard to get indentation correct, and the lack of syntax highlighting makes them difficult to read (syntax highlighting helps users understand the purpose of the various language constructs).

<!--- EXTENDED -->

The first trick I tried was to take screenshots of gvim. However, this had distinct downsides: I typically use a dark color scheme, which makes contrast on projector screens poor, and the resolution of the images is such that the text is often too small. I can of course rectify both situations by changing my GUI preferences, but this leads to a need to switch back and forth between profiles.

![Screenshot created with Vim](/uploads/2009-03-10-VimExample.png)

The next trick I tried was to use Zend Studio or Eclipse to create my screen shots. In these cases, since the editor is not my primary editor, I could set the font size and color schema how I desire, and this worked relatively well.

![Screenshot created with Eclipse](/uploads/2009-03-10-EclipseExample.png)

Except that both options really are awful. The workflow is something like this:

1. Write some code
2. Take a screenshot of the application window
3. Load said screenshot in GIMP
   1. Crop to expose only the code desired
   2. Create whatever effects are desired (drop shadow, reflection, rounded corners, etc)
4.  Insert screenshot into presentation

And what happens when you discover a typo or an error? You have to go back and do it all over. Additionally, you still can't zoom in on the text if it's too small.

I'd finally had enough, and decided to look for syntax highlighting plugins for OpenOffice.org Impress. I didn't find any. But in searching, I stumbled across an even better solution.

[Highlight](http://www.andre-simon.de/) is a syntax highlighting utility written in C. It can syntax highlight a couple dozen languages using any of a couple dozen different highlighting schemas, and, better yet, create a variety of output formats. One of these, RTF (Rich Text Format) can be directly imported into most office software, including OO.o Impress.

The usage is pretty simple: pass in a few options including an input file, output file, output type, and optionally the language (it usually autodetects fine, though), and it does the work (there are other options you can specify as well, including line width, font size, and more). Even better, you can provide directories for the source and output files — allowing you to batch them. When I'm creating a presentation now, I create a shell script that invokes the options I want and passes in a source and target directory, and run it anytime I add or update examples. Within OO.o, I then simply go to the "Import" menu, and choose "File..." — and it comes in as a native object that I can actually manipulate — including changing font size, line spacing and more.

I think the results speak for themselves:

![Highlight](/uploads/2009-03-10-Highlight.png)

The point: make your technical presentations easier to read, and easier to create: syntax highlight your code examples in a readable way.
