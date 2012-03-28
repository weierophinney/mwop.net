<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('123-Vim-7-code-completion');
$entry->setTitle('Vim 7 code completion');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1158702300);
$entry->setUpdated(1158931287);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
  'ep_no_nl2br' => 'true',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'perl',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    I may work at <a href="http://www.zend.com/">Zend</a>, but I've never been a
    fan of IDEs. They simply don't suit my programming style. I can usually keep
    track of file locations in my head pretty easily, and what I really need is
    a blank slate on which I can write, and one that doesn't consume resource
    that can better be used running web servers and other programs. Syntax
    highlighting, good indentation -- these are important, but you can get these
    from good, minimal text editors very easily. 
    <a href="http://www.vim.org">Vim is my editor of choice</a>.
</p>
<p>
    I will admit, though, that one area where I have had IDE-envy is the area of
    code completion. I often find myself doing quick lookups to php.net or
    perldoc to determine the order of arguments to a function or method call,
    or checking for the expected return value. Most of the time, this doesn't
    take much time, however, so I just live with it.
</p>
<p>
    Today, however, cruising through the blogosphere, I came across 
    <a href="http://linuxhelp.blogspot.com/2006/09/visual-walk-through-of-couple-of-new.html">an article showcasing some new features of Vim 7.0</a>, 
    and discovered Vim 7's code completion.
</p>
<p>
    Basically, while in insert mode, you can type &lt;C-x&gt; &lt;C-o&gt; to
    have vim attempt to autocomplete the current keyword. If more than one
    possibility exists, it shows a dropdown, and you can use your arrow keys to
    highlight the keyword that you wish to use.
</p>
<p>
    But it gets better! Not only does it do this kind of autocompletion, but it
    also opens a small 'scratch preview' pane showing the function/method
    signature -- i.e., the expected arguments and return value!
</p>
<p>
    I thought I had little need for IDEs before... now I have even less! Bram
    and the rest of the Vim team, my hat's off to you for more fine work!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;