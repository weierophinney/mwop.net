---
id: 134-exuberant-ctags-with-PHP-in-Vim
author: matthew
title: 'exuberant ctags with PHP in Vim'
draft: false
public: true
created: '2007-01-31T14:20:00-05:00'
updated: '2010-03-25T16:18:59-04:00'
tags:
    - php
    - vim
---
One reason I've heard PHP developers use for adopting an IDE when developing is
the ability to click on a class or function name and jump to the declaration.
Sounds like magic, and it's definitely something I've desired.

One way I get around it is by adopting PEAR coding standards for naming my
classes. Since they define a one-to-one mapping of class name to the file
system (substitute the underscore character (`_`) with the directory
separator), I can usually very quickly and easily open a class file,
particularly if I start in the base directory of the project install.

Today, however, I found [exuberant ctags](http://ctags.sourceforge.net), a
library which can be used to generate an index file mapping language objects to
source files and the line in the source file where they are declared. Contrary
to its name, it's not just for the C language; it currently supports 33
different programming languages, including PHP.

I decided to try it out on the Zend Framework core library today. At first run,
it was pretty useful. However, it was only mapping classes, and, in addition,
only those defined with the single word 'class' — abstract classes and
interfaces were entirely left out. So, I looked into the documentation to see
if I could change the behaviour.

And, being a Unix program, of course I could. First off, you can add functions
to the items it indexes with a simple flag. Additionally, you can use POSIX
regular expressions to refine what it searches.

I whipped up the following script to create my tags index:

```bash
#!/bin/bash
cd /path/to/framework/library
exec ctags-exuberant -f ~/.vim/mytags/framework \
-h \".php\" -R \
--exclude=\"\.svn\" \
--totals=yes \
--tag-relative=yes \
--PHP-kinds=+cf \
--regex-PHP='/abstract class ([^ ]*)//c/' \
--regex-PHP='/interface ([^ ]*)//c/' \
--regex-PHP='/(public |static |abstract |protected |private )+function ([^ (]*)//f/'
```

This script creates the tag index in the file `$HOME/.vim/mytags/framework`. It
scans for PHP files recursively through the tree, excluding any files found in
a `.svn` directory (I'm using a checkout from the subversion repository). The
file paths in the index are created relative to the tags file; this was
important, because if this wasn't provided, vim was unable to jump to the file,
as it couldn't find it. `--PHP-kinds=+cf` tells it to index classes and
functions. Next, I've got three regular expressions. The first tells it to
match classes beginning with 'abstract class' as classes. The second tells it
to match interfaces as classes. The last is so that PHP 5 methods, which begin
with a visibility operator, to be matched as functions.

Once the index file is generated (it takes less than a second), all you need to
do in vim is tell it to load it: `:set tags=~/.vim/mytags/framework`. At this
point, you can do all sorts of fun stuff. Place the cursor on a class name or
method name, anywhere in it, and hit `Ctrl-]`, and you'll jump to the file and
line of its declaration; `Ctrl-T` then takes you back. If you change the
invocation to `Ctrl-W ]`, it will split the current window and open the
declaration in the new pane. (If you're familiar with how help works with Vim,
this should seem pretty familiar.)

One more reason to stick with Vim for your PHP editing needs. :-)
