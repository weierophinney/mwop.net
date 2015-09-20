---
id: 194-Vimgrep-and-Vim-Project
author: matthew
title: 'Vimgrep and Vim Project'
draft: false
public: true
created: '2008-10-21T07:36:49-04:00'
updated: '2008-10-22T21:55:03-04:00'
tags:
    - programming
    - perl
    - php
    - vim
---
Chris Hartjes today was
[on a quest for a "find in project" feature for Vim](http://www.littlehart.net/atthekeyboard/2008/10/20/vim-programming-bounty-fuzzyfind-inside-files/).
"Find in Project" was a feature of Textmate that he'd grown accustomed to and
was having trouble finding an equivalent for.

The funny thing is that Textmate is a newcomer, and, of course, vim has had
such a feature for years. The thing to remember with vim, of course, is its
unix roots; typically if you know the unix command for doing something, you can
find what you need in vim. In this case, the key is the vimgrep plugin, which
ships in the standard vim distribution.

<!--- EXTENDED -->

There are a variety of resources on vimgrep. The vim documentation includes a
chapter on it, and a quick [google search](http://www.google.com/search?q=vimgrep)
on the subject turns up some nice tutorials immediately. If you've ever used
grep, the syntax is very straightforward:

```
vimgrep /{pattern}/[g][j] {file} ...
```

The "g" option indicates that all matches for a search will be returned instead
of just one per line, and the "j" option tells vim *not* to jump to the first
match automatically. What does the "g" flag really mean, though, and how are
searches returned?

Vimgrep returns search results in what's known as a "quickfix" window, and this
is where the vimgrep documentation falls apart… it doesn't explain what this
is, or link to it (which would be a nice indication that it actually has a
separate topic for this).

The Quickfix window is a pane that shows a search result per line. Each line
shows the file that matches, the line number, and the contents of that line:

```
/home/matthew/git/bugapp/application/controllers/helpers/GetForm.php|10| * @var Zend_Loader_PluginLoader
```

You can't do much from this window; it simply serves as a visual indicator of
what file you're currently looking at from the list. However, in the main
window, you can start iterating through the results one at a time, using a
subset of the Quickfix commands. As a quick summary:

- **:cc** will move to the next match in the list
- **:cn** will move to the next match in the list
- **:cp** will move to the previous match in the list
- **:cr** will rewind to the first match in the list
- **:cla** will fast forward to the last match in the list

When done, you can simply close the Quickfix window/pane, and continue working.

I should note that vimgrep *is* cross-platform. On *nix-based systems, it
defaults to using the native grep command, but it also contains an internal
(slower) implementation for use on operating systems that do not provide grep
by default. You may also map the command to alternate implementations if
desired.

I personally use this feature most with the [project plugin](http://www.vim.org/scripts/script.php?script_id=69).
Project maps vimgrep to two different commands: `<Leader>g` and `<Leader>G`.
The first will grep all files in the current project at the current level; the
second does the same, but also recurses into subprojects. This is an incredibly
easy way to refactor code, particularly for name changes.
