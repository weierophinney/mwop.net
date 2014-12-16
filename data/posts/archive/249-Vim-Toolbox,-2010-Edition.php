<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('249-Vim-Toolbox,-2010-Edition');
$entry->setTitle('Vim Toolbox, 2010 Edition');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1292417340);
$entry->setUpdated(1292575853);
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
I've been using <a href="http://www.vim.org/">Vim</a> for close to a decade. I've often said
that <em>"Unix is my IDE"</em> -- because Vim is built in the Unix philosophy, allowing
me to pipe input into it, out of it, and every which way I want. It fits very
nicely with the Unix philosophy of doing one task well, and allowing
redirection. I've found it ideal for web development in general and PHP
development specifically -- in fact, I've had excellent experiences in every
language I've tried my hand at developing in when using Vim.
</p>

<p>
Vim is also my chosen productivity suite. When I want to write a document, I
don't go into OO.o Writer or MS Word or some other word processor; I open up a
window and start typing. In most cases, I can either cut and paste my work into
other tools, or pipe it to transformation tools. I worry about the <em>content</em>
first, and the <em>presentation</em> later... like any good MVC application. ;-)
</p>

<p>
Like any good tool, you have to invest time in it in order to reap its benefits.
My learning has, to date, fallen into three time periods:
</p>

<ul>
<li>
The initial months in which I first learned Vim, via vimtutor and Steve
   Oualline's Vim book.
</li>
<li>
A period in 2006-2007 when I felt the need to make my coding more efficient,
   and first started playing with exuberant-ctags and omni-completion.
</li>
<li>
The last quarter of 2010 (yes, that's now) when I was introduced to a number
   of new tools via Twitter.
</li>
</ul>
   
<p>
So, this is my Vim Toolbox, 2010 edition.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.1">Getting Plugins</h2>

<p>
I've added two primary ways to add new plugins to my arsenal:
</p>

<ul>
<li>
<a href="https://github.com/c9s/Vimana">Vimana</a>, which is a command-line tool for
   discovering, downloading, installing, and upgrading scripts found on
   <a href="http://www.vim.org">vim.org</a>. It's not perfect, but if you know the name of
   the script, and it's provided in either a
   <a href="http://www.vim.org/scripts/script.php?script_id=1502">vimball</a> format and/or
   follows the Vim runtime file structure, it's a great way to keep your
   plugins, syntax files, etc. up-to-date.
</li>
<li>
<a href="http://tammersaleh.com/posts/the-modern-vim-config-with-pathogen">Vim Pathogen</a> 
   allows you to install plugins as "bundles", allowing you to keep
   them up-to-date separately, in their own file tree. This looks like the
   following:
<pre>
.vim/
    bundle/
        vim-task/
            ftdetect/
            ftplugin/
            syntax/
</pre>
   In short, a "bundle" mimics the structure of a Vim directory.
</li>
</ul>

<p>
The latter, Pathogen, is my preferred installation method of choice at this
point. Why? One acronym: DVCS.
</p>

<p>
A ton of popular Vim plugins are now being either developed or mirrored on
GitHub or other DVCS sites. This allows you to clone them and then create a
branch that's specific to your configuration. As an example, the popular
<a href="http://www.vim.org/scripts/script.php?script_id=2540">snipMate plugin</a> has its
key-bindings hard-coded -- which causes problems if you're already using those
bindings. Which, if you're using any form of omni-completion, is all too likely.
I simply <a href="https://github.com/weierophinney/snipmate.vim">cloned the snipMate repo</a>, 
and created a branch for my configuration (I use <code>&lt;Leader&gt;&lt;tab&gt;</code> to invoke it).
</p>

<p>
Now, it gets even better: I've made <a href="http://git.mwop.net/?a=summary&amp;p=vimrc">a git repository for my Vim configuration</a>; 
with judicious use of <code>git submodule</code>, I can now add pathogen bundles as
submodules of my repository. Right now, I've got bundles for html5.vim,
mustache.vim, NErdtree, snipMate, TagList, vim-fugitive, vim-task, and
vimwiki. This keeps my repository lean, while retaining the features I need and
use daily.
</p>

<p>
As part of creating my Vim configuration repository, I also made a few
changes to facilitate the process. First, I moved <code>$HOME/.vimrc</code> to
<code>$HOME/.vim/vimrc</code>, and symlinked the former to the latter. This allows me to
keep all my Vim configuration in one place.
</p>

<p>
Next, I moved my Vim view files outside the directory; this data is volatile and
constantly changing, and really does not need to be versioned. These are now
in my <code>$HOME/.vim.view/</code> directory. Finally, I moved my tag files into a new 
<code>$HOME/.vim.tags/</code> directory. More on that later, but, again, the rationale is
that this data is volatile and does not need to be versioned.
</p>

<h2 id="toc_1.2">DVCS</h2>

<p>
I mentioned I created a Git repository for my Vim configuration. In part, this
is due to the fact that I'm proficient with Git -- I use it day-in, day-out.  Hg
and other DVCS systems are also great; I'm not using them nearly as often,
however.
</p>

<p>
To that end, I'm now using <a href="https://github.com/tpope/vim-fugitive">vim-fugitive</a>.
The author boasts that it's "a Git wrapper so awesome, it should be illegal"; I
wouldn't necessarily go that far, but I do find it incredibly useful. While I'm
typically working in a console, I also find myself in GVim windows regularly as
well -- and having a nice, familiar interface to Git is very useful. If you use
both Vim and Git, I highly recommend checking out vim-fugitive.
</p>

<h2 id="toc_1.3">Filesystem Navigation and Projects</h2>

<p>
At some point, unless you're one of those developers who likes to code
everything in a single file, you need to do some sort of navigation. A couple
years back, <a href="http://zmievski.org/">Andrei Zmievski</a> introduced me to
<a href="http://www.vim.org/scripts/script.php?script_id=1658">NErdtree</a>, a dead-simple,
colorized navigation. I use this every. single. day.
</p>

<p>
I also use a tool called <a href="http://www.vim.org/scripts/script.php?script_id=69">Project</a>. 
This tool allows you to specify "files of interest" to a project -- either by
automatically scanning a tree, or manually. Additionally, the way you specify
the hierarchy can be entirely arbitrary -- allowing you to flatten the tree when
it's getting in the way. I use this tool regularly as well, though not quite as
much as NErdtree.
</p>

<h2 id="toc_1.4">Navigating Code</h2>

<p>
One often touted feature of modern IDEs is code completion and hinting. These
are definitely useful features, particularly when working on unfamiliar code, or
code you haven't touched in some time.
</p>

<p>
Vim actually has some great tools for this already. One is built-in:
omni-completion (<code>:he new-omni-completion</code> for Vim's help on the feature). By
default, it inspects the files in open buffers to provide completion (assuming
it has a definition for that language and/or syntax highlighting) -- but it can
also utilize <em>tag</em> files.
</p>

<p>
The built-in omni-completion for PHP is reasonable -- you can jump around by
class names, function/method names, variables, etc. It gets much, much more
useful, however, when you utilize tag files, as you don't need the files already
open in order to get completion. I've <a href="http://weierophinney.net/matthew/archives/134-exuberant-ctags-with-PHP-in-Vim.html">blogged about ctags before</a>; 
however, I've updated my scripts a bit.
</p>

<p>
First, exuberant-ctags is much more PHP aware now than when I blogged. This
means you don't need to do any special regex-fu in order to properly identify
abstract classes, interfaces, and methods. Second, I found that I could generate
a single script with prompts to indicate the directory and tag file name.
<a href="http://git.mwop.net/?a=viewblob&amp;p=vimrc&amp;h=3a8ca75bcd6a28dbea2cee02f31d72e82f415e5b&amp;hb=c1a728c96fa593be5c570c78cac5dcb2b7052fd3&amp;f=bin/mkTags">That script</a> 
basically looks like this:
</p>

<div class="example"><pre><code class="language-sh">
#!/bin/sh
dir=\&quot;\&quot;
name=\&quot;\&quot;
if [ $# -ge 2 ] ; then
    # Two arguments: first is directory, second is \&quot;alias\&quot;
    dir=$1
    name=$2
else
    if [ $# -eq 1 ] ; then
        # One argument: use as directory, and use basename of directory as alias
        dir=$1
        name=`basename $1`
    else
        # Otherwise: prompt
        echo \&quot;Enter the path to a directory containing PHP code you wish\&quot;
        echo \&quot;to create tags for:\&quot;
        read dir
        echo \&quot;Enter the name of the tagfile you wish to create:\&quot;
        read name
    fi
fi

echo \&quot;Creating tags for directory '$dir' using alias '$name'\&quot;
cd $dir
exec ctags-exuberant -f ~/.vim.tags/$name \
-h \&quot;.php\&quot; -R \
--exclude=\&quot;\.svn\&quot; \
--totals=yes \
--tag-relative=yes \
--fields=+afkst \
--PHP-kinds=+cf 
echo \&quot;[DONE]\&quot;
</code></pre></div>

<p>
Two things to note: 
</p>

<ul>
<li>
It creates the tag files in <code>$HOME/.vim.tags/</code>. I do this as my tag files
   change fairly regularly, and can be re-generated on the fly as needed.
   There's no reason to version them.
</li>
<li>
Once generated, you need to load them. I created a "LoadTags" Vim function
   that will load a tag file by the given name from the <code>$HOME/.vim.tags/</code>
   directory. By default, I load the ones I most commonly use (ZF1, ZF2,
   PHPUnit). Otherwise, a quick <code>:Ltag &lt;tag filename&gt;</code> will load on-demand.
</li>
</ul>

<p>
Once the tags are created, you can use Vim's normal tag features to load files,
jump to files, etc. The most common commands I use are:
</p>

<ul>
<li>
<code>:stag &lt;tagname&gt;</code>, which splits the current window and loads the given tag in
   the newly created split.
</li>
<li>
<code>&lt;Ctrl-w&gt;]</code>, when on text you suspect of being a tag (such as a classname),
   will split the current window and load that tag file in the new pane.
</li>
</ul>

<p>
These two commands I use constantly, and are huge timesavers -- I can basically
use the code as my documentation.
</p>

<p>
Additionally, the main use of omni-completion is to give tab-completion for
known tags. This means that you can start typing, hit <code>&lt;Tab&gt;</code>, and either have
it immediately complete, or give you a list of potential matches. It's not quite
as useful as a good IDE -- it's not context-aware, so you'll get <strong>any</strong> potential
match from <strong>any</strong> class -- but it's better than nothing, provides reasonable
hinting, and helps protect you from spelling errors.
</p>

<p>
That said, there's also something to be said about just having the signatures
and prototypes of the various methods easily accessible. For that, there's the
<a href="http://vim-taglist.sourceforge.net/">Vim TagList</a> plugin. This plugin will scan
open files and produce a list of classes, variables, and methods. With this
list, you can get the method prototypes, as well as jump directly to their
definitions. Pressing <code>&lt;Space&gt;</code> will show you the prototype, <code>&lt;CR&gt;</code> will jump to
it.
</p>

<p>
Between these two features (omni-completion with tags and TagList), I have most
of the useful features of any IDE immediately available.
</p>

<h2 id="toc_1.5">Working With Code</h2>

<p>
Since I sling code for a living, it's useful to have some plugins and syntax
highlighting to make working with code easier.
</p>

<p>
First off, I've been experimenting with HTML5; as such, I added the
<a href="https://github.com/othree/html5.vim">html5.vim</a> syntax highlighting as a
Pathogen module. This adds support for a bunch of HTML5-specific features, while
retaining the fantastic HTML support already in the official HTML syntax
provided with Vim.
</p>

<p>
Next, I use the <a href="http://www.vim.org/scripts/script.php?script_id=967">php.vim</a>
syntax file from vim.org. This particular syntax file has support for PHP 5.3
features, which come in very handy while I'm coding for ZF2. The author of this
syntax has also created a script (<code>php_vimgen.php</code>) for generating syntax files
for core classes as well as extensions using the Reflection API. I've modified
the tool in my repository to strip out the generated syntax, and instead
source it from the file created with the <code>php_vimgen.php</code> script; I've also
altered said script to create the syntax in <code>__DIR__ . '/php_syntax_vimgen.vim'</code>, 
ensuring I can always source it from the same location. This allows me to keep
my PHP syntax highlighting up-to-date.
</p>

<p>
Finally, I use <a href="http://www.vim.org/scripts/script.php?script_id=2540">snipMate</a>,
a tool that emulates TextMate's "snippet" features. Basically, this is
dead-simple, templated code generation. You can write your own files (
<a href="http://git.mwop.net/?a=tree&amp;p=vimrc&amp;h=5990c978d877f9dcad2a02239ae3af74bcb75ba4&amp;hb=c1a728c96fa593be5c570c78cac5dcb2b7052fd3&amp;f=snippets/php">I did</a>),
or use those that come with it. Once you've got some snippets, you type a word
(usually a mnemonic for the operation you're trying to perform), and it will
either just spit up a template, or optionally provide "prompts" for you to fill
in (along with variable completion!). Basically, I never code accessors and
mutators anymore; snipMate does these for me, with a little prompting.
</p>

<h2 id="toc_1.6">Organization</h2>

<p>
I use Vim day-in, day-out, for all sorts of things: mail, drafting blog posts,
drafting presentation outlines, taking meeting notes, managing my todo list, and
more. As such, I try to keep as much of my "organization" within Vim -- it's
just easier.
</p>

<p>
I've tried a number of tools over the years. For a good 4 or 5 years, my primary
tool was <a href="http://www.vimoutliner.org/">VimOutliner</a>. It provided decent syntax,
decent folding, and reasonable HTML generation from the outline. However, in
recent years, I feel the project had stalled, and I also found that the way I
wanted to use it had changed: outlining is great, but I often want to use the
outline as a starting point for generating content; task tracking is fine, but I
found, for whatever reason, that the way VimOutliner handled task status often
didn't work well -- either from a tooling or a syntax standpoint.
</p>

<p>
At some point, <a href="http://twitter.com/tswicegood">Travis Swicegood</a> introduced me to
<a href="http://code.google.com/p/vimwiki/">vimwiki</a>. This tool provides a personal wiki
<em>within</em> Vim. This tool allowed me to organize my notes in an ad-hoc,
semi-hierarchical way, link back and forth between them, and have not only
reasonable in-editor highlighting, but great HTML generation. This allowed me to
ditch VimOutliner for everything but task tracking. Once I made my "wiki"
directory a Git repository, I then received versioning basically for free
(especially with vim-fugitive, which makes it easy for me to hit <code>:Gwrite</code> and
<code>:Gcommit</code> when I create and/or update files).
</p>

<p>
Another feature vimwiki provides is a "diary". You access it using
<code>&lt;Leader&gt;w&lt;Leader&gt;w</code>, which opens up a new wiki page for the current day (or, if
you already opened it before, re-opens the one created earlier in the day). This
is a really useful tool for taking notes during meetings, or when doing
research, etc.
</p>

<p>
Couple these features with integrated search (<code>:VWS /pattern/</code>), and vimwiki is
<em>the</em> killer productivity tool in my toolbox.
</p>

<p>
At another point, Travis then pointed out another tool:
<a href="https://github.com/samsonw/vim-task">vim-task</a>. This is perhaps the most
dead-simple task tracker I've ever used; each line is a task, and is either
incomplete (starts with a "-"), or complete (starts with a checkmark). A simple
keybinding, which I've mapped to <code>&lt;Leader&gt;m</code>, toggles status - and complete
items get highlighted in green and italicized, making you feel good and giving a
good visual queue as to what you've completed.
</p>

<p>
At some point, Travis also tossed out the idea that combining vimwiki with
vim-task would be useful -- and I latched onto this idea. I've now created
<a href="https://github.com/weierophinney/vimwiki/tree/feature/vim-task">a fork of vimwiki with vim-task integration</a>,
which allows me to keep my tasks and notes in a single place... and, since my
wiki is versioned, my tasks are as well.
</p>

<h2 id="toc_1.7">Various Oddities</h2>

<p>
As I mentioned at the start of this post, I've been using Vim for close to a
decade. Part of the reason Vim was appealing to me was due to the fact that it
kept me in the "home row" of the keyboard -- which provides a huge amount of
efficency. You don't have to move to the arrow keys to scroll, no leaving the
keyboard for the mouse, etc. That said, some key combinations are difficult to
reach:
</p>

<ul>
<li>
The placement of the <code>&lt;Esc&gt;</code> key varies from keyboard to keyboard, and is
   rarely in a place that is easy to reach. On my current keyboard, it's in the
   top left corner, above the function keys; it's impossible to reach without
   moving my hand. A tip I picked up pretty much when I began using Vim was to
   map "jj" to <code>&lt;Esc&gt;</code>; it's rare to type a "j" repeatedly in the English
   language, and it's dead-center on the home row. This is incredibly efficent.
</li>
<li>
I've mapped my Caps Lock key to <code>&lt;Ctrl&gt;</code> on every system I've owned in the
   past decade. I never used it, and it's almst always on the home row. Again,
   hugely efficient.
</li>
<li>
Keybindings are great, but there's so many already in use that it's hard
   <em>not</em> to overwrite existing ones. Using the <code>&lt;Leader&gt;</code> key to define
   keybindings has been great. As examples, I mapped <code>&lt;Leader&gt;m</code> to toggle
   tasks, and <code>&lt;Leader&gt;&lt;Tab&gt;</code> to invoke snipMate.
</li>
</ul>

<p>
In Vim, <code>&lt;C-m&gt;</code> has long been the "make" binding, and <code>&lt;C-l&gt;</code> for linters. In
languages like PHP and JavaScript, these often don't make sense. However, I've
bound these in both languages -- in PHP, "make" executes the current script
using the PHP executable, while "lint" runs it through the PHP linter. In JS, I
leave "make" unbound, while "lint" runs the script through jslint.
</p>

<p>
I've also added the "php-doc.vim" plugin, and mapped <code>&lt;C-P&gt;</code> to create PHP
docblocks; the plugin is context aware, and will create appropriate annotations.
</p>

<h2 id="toc_1.8">Cloning my repo</h2>

<p>
As noted, I've created a repository for my Vim configuration. If you want to
clone it and explore it, you can do so as follows:
</p>

<ul>
<li>
Browse the repository: <a href="http://git.mwop.net/?a=summary&amp;p=vimrc">http://git.mwop.net/?a=summary&amp;p=vimrc</a>
</li>
<li>
Clone the repo: <code>git clone git://mwop.net/vimrc.git</code>
</li>
</ul>

<p>
Be aware that there a number of git submodules in play (all the pathogen modules
are git submodules). To initialize these, simply run <code>git submodule init</code>
followed by <code>git submodule update</code> after you clone the repository.
</p>

<h2 id="toc_1.9">Resources</h2>

<p>
I didn't learn all this overnight. As with any toolset, it's only as good as the
amount of time you invest learning it. For me, my primary resources lately have
been:
</p>

<ul>
<li>
<a href="http://twitter.com/#!/search/%23vim">#vim hashtag on Twitter</a>
</li>
<li>
<a href="http://vimcasts.org/">VimCasts</a> are a fantastic source of information,
   provided by Drew Neil. Seriously, these are <strong>completely</strong> worth the time spent
   watching them.
</li>
<li>
<a href="http://twitter.com/tswicegood">Travis Swicegood</a> has tweeted a number of
   times about interesting things he does with Vim and Git, and inspired me to
   write the vim-task syntax for vimwiki.
</li>
</ul>

<h2 id="toc_1.10">More Tools</h2>

<p>
This post has been on my <i>Vim</i> toolbox. I've also been usig a number of
other tools lately -- <a href="http://tmux.sourceforge.net/">tmux</a>, <a
    href="http://www.zsh.org/">zsh</a> (in particular, git prompts), <a
    href="http://hotot.org/">Hotot</a> (GTK2 + WebKit Twitter client), and more;
I may blog about those in the future -- using Vim. ;-)
</p>
EOT;
$entry->setExtended($extended);

return $entry;
