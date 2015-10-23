---
id: 164-Vim-Productivity-Tips-for-PHP-Developers
author: matthew
title: 'Vim Productivity Tips for PHP Developers'
draft: false
public: true
created: '2008-03-22T10:41:26-04:00'
updated: '2008-03-25T11:39:35-04:00'
tags:
    0: php
    2: vim
---
I use [Vim](http://www.vim.org/) for all my editing needs — TODO lists, email,
presentation outlines, coding in any language… everything. So, I thought I'd
start sharing some of my vim habits and tools with others, particularly those
that pertain to using Vim with PHP.

<!--- EXTENDED -->

### Mapping the PHP interpreter and linter to keystrokes

Probably the most useful thing I've done as a PHP developer is to add mappings
to run the current file through (a) the PHP interpreter (using `Ctrl-M`), and (b)
the PHP interpreter's linter (using `Ctrl-L`). These are accomplished with the
following:

```
" run file with PHP CLI (CTRL-M)
:autocmd FileType php noremap <C-M> :w!<CR>:!$HOME/bin/php %<CR>

" PHP parser check (CTRL-L)
:autocmd FileType php noremap <C-L> :!$HOME/bin/php -l %<CR>
```

(I have `\~/bin/php` as my PHP interpreter, which allows me to run PHP with a
custom config file, as well as to change which PHP binary I'm using.)

These two commands allow me to quickly and easily check that my syntax is okay,
as well as to run unit test suites easily.

### Vim Project

Next up is the excellent [Project plugin](http://www.vim.org/scripts/script.php?script_id=69).

"Project", at its most basic, allows you to setup a navigation pane with a list
of files related to your project. The files are typically organized by
directory, but the beauty is that the hierarchy can be defined however it makes
sense for your given project. It also has tools for creating projects based on
a given directory, recursively pulling in files based on filters you specify.
Type `:help project` to get documentation on this after you install it; `\C`
will help you create your first project.

Each project can consist of one or more project folds; these can be sub
projects, or a self-defined hierarchy or grouping of files. For instance, in my
Zend Framework project file, I have "library", "tests", and "documentation"
folds — "library" points to "library/Zend/", "tests" points to "tests/", and
"documentation" points to "documentation/manual/en/". Within each, I then have
folds for each subdirectory. Since directories and subprojects are specified as
folds, you can use Vim's native folding mechanisms to keep only the file of
interest visible, which is very handy.

![Vim Project](/uploads/2008-03-22-VimProject.png)

Basically, Project allows vim to act like a minimal IDE. With the file list on
the left, you simply hit enter on a file, and it loads in the main pane. More
fun is when you use the `\S` command, which will split the main pane and load
the file into the new pane. This is particularly useful when doing Test Driven
Development, as you can have one pane for the unit test code, and another for
the class file, allowing you to jump back and forth between them. Add to this
the `Ctrl-M` and `Ctrl-L` commands I listed earlier, and you're now also able to
quickly and easily check your files for syntax errors and run tests directly
within the Vim window.

![Vim Project](/uploads/2008-03-22-VimUnitTests.png)

There are other commands, too. You can run all files through a particular
script, grep all files in a project, map particular file types to specific
launchers, etc. Combine it with other Vim functionality, and you have a
minimal, yet powerful, IDE at your disposal that launches in under a second.

By default, Project stores projects in `$HOME/.vimprojects`. I find that I don't
necessarily want all my projects at any given time, so I've created a
`$HOME/.projects/` directory that has a project entry for each project — I
simply save the contents of a project fold to files under this tree. I can then
perform `:r ~/.projects/<projectname>` to read in a given project when I want
to work on it. This helps me keep my workspace uncluttered, and also helps me
focus on a given project at a time.

### Ctags

I've [covered ctags](/blog/134-exuberant-ctags-with-PHP-in-Vim.html) elsewhere,
so I won't cover them here, but with ctags defined, I get tab completion for
most classes and methods (and Vim takes care of tab-completion for class
members in the current class file), as well as the ability to quickly and
easily open class files for classes I've tagged — which is useful when you want
to see what methods are available and how they work.

* * * * *

I'll try and cover other vim techniques I use in upcoming blog entries. Those
listed in here, though, have greatly increased my productivity, and are things
I use daily.
