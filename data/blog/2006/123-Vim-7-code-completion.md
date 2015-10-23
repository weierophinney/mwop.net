---
id: 123-Vim-7-code-completion
author: matthew
title: 'Vim 7 code completion'
draft: false
public: true
created: '2006-09-19T17:45:00-04:00'
updated: '2006-09-22T09:21:27-04:00'
tags:
    - programming
    - perl
    - php
---
I may work at [Zend](http://www.zend.com/), but I've never been a fan of IDEs.  They simply don't suit my programming style. I can usually keep track of file locations in my head pretty easily, and what I really need is a blank slate on which I can write, and one that doesn't consume resource that can better be used running web servers and other programs. Syntax highlighting, good indentation — these are important, but you can get these from good, minimal text editors very easily. [Vim is my editor of choice](http://www.vim.org).

I will admit, though, that one area where I have had IDE-envy is the area of code completion. I often find myself doing quick lookups to php.net or perldoc to determine the order of arguments to a function or method call, or checking for the expected return value. Most of the time, this doesn't take much time, however, so I just live with it.

Today, however, cruising through the blogosphere, I came across [an article showcasing some new features of Vim 7.0](http://linuxhelp.blogspot.com/2006/09/visual-walk-through-of-couple-of-new.html), and discovered Vim 7's code completion.

Basically, while in insert mode, you can type `<C-x> <C-o>` to have vim attempt to autocomplete the current keyword. If more than one possibility exists, it shows a dropdown, and you can use your arrow keys to highlight the keyword that you wish to use.

But it gets better! Not only does it do this kind of autocompletion, but it also opens a small 'scratch preview' pane showing the function/method signature — i.e., the expected arguments and return value!

I thought I had little need for IDEs before… now I have even less! Bram and the rest of the Vim team, my hat's off to you for more fine work!
