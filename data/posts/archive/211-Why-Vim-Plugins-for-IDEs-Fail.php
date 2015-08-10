<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('211-Why-Vim-Plugins-for-IDEs-Fail');
$entry->setTitle('Why Vim Plugins for IDEs Fail');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1236861000);
$entry->setUpdated(1237204997);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I'm an unabashed <a href="http://www.vim.org/">Vim</a> user. It has been my
    primary editor for over seven years now, when I switched to it to make it
    easier to edit files over SSH. At the time, I made myself use it exclusively
    for a month so that the habits would be ingrained, and took the time to go
    through <a href="http://vimdoc.sourceforge.net/htmldoc/usr_01.html">vimtutor</a>
    as well as to order and read Steve Oualline's 
    <a href="http://www.amazon.com/iMproved-VIM-Landmark-Steve-Oualline/dp/0735710015">Vim book</a>.
    And when I say "exclusively," I mean it -- I switched to using 
    <a href="http://www.mutt.org/">Mutt</a> for email at that time, and also
    started doing all code development, outlining, and more in vim. And after a
    month, I realized I didn't want to use anything else.
</p>

<p>
    Ironically, I find myself <a href="http://www.zend.com/">working for a company</a> 
    that sells an Integrated Development Environment (IDE). As a result, I've
    done some test runs with Eclipse, Zend Studio for Eclipse, and even
    NetBeans, to see what features they offer and to see if there would be
    anything compelling that might make me change my mind.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    One of the immediate sticking points for me is that my brain is now
    hardwired to edit text a certain way. I use my home row keys to navigate
    through a document (as it's wicked efficient), switch to visual mode to
    highlight text (even more fun is highlighting specific columns!), and more.
    This has driven me to try a number of plugins for the aforementioned editors
    that add 'vi' or 'vim' capabilities to them.
</p>

<p>
    I've tried the following:
</p>

<ul>
    <li><a href="http://eclim.sourceforge.net/">Eclim</a> (Eclipse)</li>
    <li><a href="http://vrapper.sourceforge.net/home/">Vwrapper</a> (Eclipse)</li>
    <li><a href="http://jvi.sourceforge.net/">jVi</a> (NetBeans)</li>
</ul>

<p>
    So far, they universally fail.
</p>

<p>
    Why? Because they reimplement vi(m) keybindings, but don't actually re-use
    anything from vim itself. Why does this matter? Because this means the tools
    completely ignore the entire vim ecosystem. Vim has a wealth of
    user plugins, syntax highlighting codecs, filetype plugins, and other
    utilities -- and you can use none of them. Vim allows you to create your own
    keybindings, and provides a language for creating your own plugins... and
    you cannot use them. Vim allows you to specify your own settings in a
    configuration file -- these tools not only ignore the file, but have no
    ability to source it whatsoever.
</p>

<p>
    As an example, I have bound 'jj' to the &lt;Esc&gt; key. This
    micro-optimization prevents me from needing to reach outside my home row in
    order to switch modes. I cannot use this in eclim. I have bound
    &lt;Ctrl&gt;-L to the linter in a variety of languages; I cannot use this in
    vwrapper. I use a plugin to autocreate phpdoc docblocks; I cannot use this
    in jVi. Basically, each of these tools provides what I consider a
    <em>crippled</em> version of vim, as I cannot do things I would do in vim.
</p>

<p>
    What these tools are trying to do is make the IDE environment more familiar
    to vim users -- as far as I can see, it's more like a migration tool. "See
    -- you can make it look and act a lot like vim! Now that you're here, maybe
    you should try some of our other features!"
</p>

<p>
    And that's where the final nail in the coffin occurs, to my thinking,
    because there's one thing vim does that no other IDE I've tried is capable
    of: it loads in under a second. I often want to look something up in my
    code. Do I wait for my IDE to startup? or, once loaded, do I peform a
    variety of mouse convolutions to find the file I want to see? Or do I simply
    open up vim and use c-tags to load my file in 1 or 2 seconds?
</p>

<p>
    In summary, these projects fail because they make you long for something you
    already had: a fast, extensible editor.
</p>

<p>
    So, all in all, while I think these projects are interesting, I think it
    makes more sense to either go total immersion in the IDE, or go back to your
    editor of choice. I know which one will be in my own toolbox for many years
    to come.
</p>
EOT;
$entry->setExtended($extended);

return $entry;