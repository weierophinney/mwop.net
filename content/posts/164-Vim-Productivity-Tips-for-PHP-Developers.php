<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('164-Vim-Productivity-Tips-for-PHP-Developers');
$entry->setTitle('Vim Productivity Tips for PHP Developers');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1206196886);
$entry->setUpdated(1206459575);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'vim',
));

$body =<<<'EOT'
<p>
    I use <a href="http://www.vim.org/">Vim</a> for all my editing needs -- TODO
    lists, email, presentation outlines, coding in any language... everything.
    So, I thought I'd start sharing some of my vim habits and tools with others,
    particularly those that pertain to using Vim with PHP.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Mapping the PHP interpreter and linter to keystrokes</h3>
<p>
    Probably the most useful thing I've done as a PHP developer is to add
    mappings to run the current file through (a) the PHP interpreter (using
    Ctrl-M), and (b) the PHP interpreter's linter (using Ctrl-L). These are
    accomplished with the following:
</p>

<div>
<div class="example"><pre><code lang="vim">
\&quot; run file with PHP CLI (CTRL-M)
:autocmd FileType php noremap &lt;C-M&gt; :w!&lt;CR&gt;:!$HOME/bin/php %&lt;CR&gt;

\&quot; PHP parser check (CTRL-L)
:autocmd FileType php noremap &lt;C-L&gt; :!$HOME/bin/php -l %&lt;CR&gt;
</code></pre></div>
</div>

<p>
    (I have ~/bin/php as my PHP interpreter, which allows me to run PHP with a
    custom config file, as well as to change which PHP binary I'm using.)
</p>

<p>
    These two commands allow me to quickly and easily check that my syntax is
    okay, as well as to run unit test suites easily.
</p>
    
<h3>Vim Project</h3>

<p>
    Next up is the excellent 
    <a href="http://www.vim.org/scripts/script.php?script_id=69">Project plugin</a>.
</p>

<p>
    "Project", at its most basic, allows you to setup a navigation pane with a
    list of files related to your project. The files are typically organized by
    directory, but the beauty is that the hierarchy can be defined however it
    makes sense for your given project. It also has tools for creating projects
    based on a given directory, recursively pulling in files based on filters
    you specify. Type ':help project' to get documentation on this after you
    install it; <code>\C</code> will help you create your first project.
</p>

<p>
    Each project can consist of one or more project folds; these can be sub
    projects, or a self-defined hierarchy or grouping of files. For instance, in
    my Zend Framework project file, I have "library", "tests", and
    "documentation" folds -- "library" points to "library/Zend/", "tests" points
    to "tests/", and "documentation" points to "documentation/manual/en/".
    Within each, I then have folds for each subdirectory. Since directories and
    subprojects are specified as folds, you can use Vim's native folding
    mechanisms to keep only the file of interest visible, which is very handy.
</p>

<div><img src="/uploads/2008-03-22-VimProject.png" alt="Vim Project" /></div>

<p>
    Basically, Project allows vim to act like a minimal IDE. With the file list
    on the left, you simply hit enter on a file, and it loads in the main pane.
    More fun is when you use the \S command, which will split the main pane and
    load the file into the new pane. This is particularly useful when doing Test
    Driven Development, as you can have one pane for the unit test code, and
    another for the class file, allowing you to jump back and forth between
    them. Add to this the Ctrl-M and Ctrl-L commands I listed earlier, and
    you're now also able to quickly and easily check your files for syntax
    errors and run tests directly within the Vim window.
</p>

<div><img src="/uploads/2008-03-22-VimUnitTests.png" alt="Vim Project" /></div>

<p>
    There are other commands, too. You can run all files through a particular
    script, grep all files in a project, map particular file types to specific
    launchers, etc. Combine it with other Vim functionality, and you have a
    minimal, yet powerful, IDE at your disposal that launches in under a second.
</p>

<p>
    By default, Project stores projects in $HOME/.vimprojects. I find that I
    don't necessarily want all my projects at any given time, so I've created a
    $HOME/.projects/ directory that has a project entry for each project -- I
    simply save the contents of a project fold to files under this tree. I can
    then perform <code>:r ~/.projects/&lt;projectname&gt;</code> to read in a
    given project when I want to work on it. This helps me keep my workspace
    uncluttered, and also helps me focus on a given project at a time.
</p>

<h3>Ctags</h3>

<p>
    I've <a href="/matthew/archives/134-exuberant-ctags-with-PHP-in-Vim.html">covered ctags</a> 
    elsewhere, so I won't cover them here, but with ctags defined, I get tab
    completion for most classes and methods (and Vim takes care of
    tab-completion for class members in the current class file), as well as the
    ability to quickly and easily open class files for classes I've tagged --
    which is useful when you want to see what methods are available and how they
    work.
</p>

<hr />

<p>
    I'll try and cover other vim techniques I use in upcoming blog entries.
    Those listed in here, though, have greatly increased my productivity, and
    are things I use daily.
</p>
EOT;
$entry->setExtended($extended);

return $entry;