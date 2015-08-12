<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('194-Vimgrep-and-Vim-Project');
$entry->setTitle('Vimgrep and Vim Project');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1224589009);
$entry->setUpdated(1224726903);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'php',
  3 => 'vim',
));

$body =<<<'EOT'
<p>
    Chris Hartjes today was <a href="http://www.littlehart.net/atthekeyboard/2008/10/20/vim-programming-bounty-fuzzyfind-inside-files/">on a quest for a "find in project" feature for Vim</a>.
    "Find in Project" was a feature of Textmate that he'd grown accustomed to
    and was having trouble finding an equivalent for.
</p>

<p>
    The funny thing is that Textmate is a newcomer, and, of course, vim has had
    such a feature for years. The thing to remember with vim, of course, is its
    unix roots; typically if you know the unix command for doing something, you
    can find what you need in vim. In this case, the key is the vimgrep plugin,
    which ships in the standard vim distribution.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    There are a variety of resources on vimgrep. The vim documentation includes
    a chapter on it, and a quick <a href="http://www.google.com/search?q=vimgrep">google search</a>
    on the subject turns up some nice tutorials immediately. If you've ever used
    grep, the syntax is very straightforward:
</p>

<code><pre>
vimgrep /{pattern}/[g][j] {file} ...
</pre></code>

<p>
    The "g" option indicates that all matches for a search will be returned
    instead of just one per line, and the "j" option tells vim <em>not</em> to
    jump to the first match automatically. What does the "g" flag really mean,
    though, and how are searches returned?
</p>

<p>
    Vimgrep returns search results in what's known as a "quickfix" window, and
    this is where the vimgrep documentation falls apart... it doesn't explain
    what this is, or link to it (which would be a nice indication that it
    actually has a separate topic for this).
</p>

<p>
    The Quickfix window is a pane that shows a search result per line. Each line
    shows the file that matches, the line number, and the contents of that line:
</p>

<code><pre>
/home/matthew/git/bugapp/application/controllers/helpers/GetForm.php|10| * @var Zend_Loader_PluginLoader
</pre></code>

<p>
    You can't do much from this window; it simply serves as a visual indicator
    of what file you're currently looking at from the list. However, in the main
    window, you can start iterating through the results one at a time, using a
    subset of the Quickfix commands. As a quick summary:
</p>

<ul>
    <li><b>:cc</b> will move to the next match in the list</li>
    <li><b>:cn</b> will move to the next match in the list</li>
    <li><b>:cp</b> will move to the previous match in the list</li>
    <li><b>:cr</b> will rewind to the first match in the list</li>
    <li><b>:cla</b> will fast forward to the last match in the list</li>
</ul>

<p>
    When done, you can simply close the Quickfix window/pane, and continue
    working.
</p>

<p>
    I should note that vimgrep <em>is</em> cross-platform. On *nix-based
    systems, it defaults to using the native grep command, but it also contains
    an internal (slower) implementation for use on operating systems that do not
    provide grep by default. You may also map the command to alternate
    implementations if desired.
</p>

<p>
    I personally use this feature most with the <a href="http://www.vim.org/scripts/script.php?script_id=69">project plugin</a>.
    Project maps vimgrep to two different commands: &lt;Leader&gt;g and
    &lt;Leader&gt;G. The first will grep all files in the current project at the
    current level; the second does the same, but also recurses into subprojects.
    This is an incredibly easy way to refactor code, particularly for name
    changes.
</p>
EOT;
$entry->setExtended($extended);

return $entry;