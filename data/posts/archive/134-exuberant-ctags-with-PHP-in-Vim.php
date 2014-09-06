<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('134-exuberant-ctags-with-PHP-in-Vim');
$entry->setTitle('exuberant ctags with PHP in Vim');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1170271200);
$entry->setUpdated(1269548339);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'vim',
));

$body =<<<'EOT'
<p>
    One reason I've heard PHP developers use for adopting an IDE when developing
    is the ability to click on a class or function name and jump to the
    declaration. Sounds like magic, and it's definitely something I've desired.
</p>
<p>
    One way I get around it is by adopting PEAR coding standards for naming my
    classes. Since they define a one-to-one mapping of class name to the file
    system (substitute the underscore character ('_') with the directory
    separator), I can usually very quickly and easily open a class file,
    particularly if I start in the base directory of the project install.
</p>
<p>
    Today, however, I found <a href="http://ctags.sourceforge.net">exuberant ctags</a>,
    a library which can be used to generate an index file mapping language
    objects to source files and the line in the source file where they are
    declared. Contrary to its name, it's not just for the C language; it
    currently supports 33 different programming languages, including PHP.
</p>
<p>
    I decided to try it out on the Zend Framework core library today. At first
    run, it was pretty useful. However, it was only mapping classes, and, in
    addition, only those defined with the single word 'class' -- abstract classes
    and interfaces were entirely left out. So, I looked into the documentation
    to see if I could change the behaviour.
</p>
<p>
    And, being a Unix program, of course I could. First off, you can add
    functions to the items it indexes with a simple flag. Additionally, you can
    use POSIX regular expressions to refine what it searches.
</p>
<p>
    I whipped up the following script to create my tags index:
</p>
<div class="example"><pre><code lang="bash">
#!/bin/bash
cd /path/to/framework/library
exec ctags-exuberant -f ~/.vim/mytags/framework \
-h \&quot;.php\&quot; -R \
--exclude=\&quot;\.svn\&quot; \
--totals=yes \
--tag-relative=yes \
--PHP-kinds=+cf \
--regex-PHP='/abstract class ([^ ]*)/\1/c/' \
--regex-PHP='/interface ([^ ]*)/\1/c/' \
--regex-PHP='/(public |static |abstract |protected |private )+function ([^ (]*)/\2/f/'
</code></pre></div>
<p>
    This script creates the tag index in the file
    <kbd>$HOME/.vim/mytags/framework</kbd>. It scans for PHP files recursively
    through the tree, excluding any files found in a <kbd>.svn</kbd> directory
    (I'm using a checkout from the subversion repository). The file paths in the
    index are created relative to the tags file; this was important, because if
    this wasn't provided, vim was unable to jump to the file, as it couldn't
    find it. <kbd>--PHP-kinds=+cf</kbd> tells it to index classes and functions.
    Next, I've got three regular expressions.  The first tells it to match
    classes beginning with 'abstract class' as classes. The second tells it to
    match interfaces as classes. The last is so that PHP 5 methods, which begin
    with a visibility operator, to be matched as functions.
</p>
<p>
    Once the index file is generated (it takes less than a second), all you need
    to do in vim is tell it to load it: <kbd>:set
        tags=~/.vim/mytags/framework</kbd>. At this point, you can do all sorts
    of fun stuff. Place the cursor on a class name or method name, anywhere in
    it, and hit <kbd>Ctrl-]</kbd>, and you'll jump to the file and line of its
    declaration; <kbd>Ctrl-T</kbd> then takes you back. If you change the
    invocation to <kbd>Ctrl-W ]</kbd>, it will split the current window and open
    the declaration in the new pane. (If you're familiar with how help works
    with Vim, this should seem pretty familiar.)
</p>
<p>
    One more reason to stick with Vim for your PHP editing needs. :-)
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;