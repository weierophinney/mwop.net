<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('222-Cloning-the-ZF-SVN-repository-in-Git');
$entry->setTitle('Cloning the ZF SVN repository in Git');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1251737677);
$entry->setUpdated(1251740513);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've been using <a href="http://git-scm.com/">Git</a> for around a year now.
    My interest in it originally was to act as a replacement for 
    <a href="http://svk.bestpractical.com/">SVK</a>, with which I'd had some
    bad experiences (when things go wrong with svk, they go very wrong). Why was
    I using a distributed version control system, though?
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<ul>
    <li>
        I travel several times a year for work. Oftentimes I have long layovers,
        or hotels and/or conference venues with spotty or pricey internet
        connectivity.  I need a way to continue working such that I can continue
        making atomic commits without needing to worry about whether or not I
        have network access.
    </li>

    <li>
        I work from home. This gives me great flexibility in terms of where I
        work. Sometimes I work from the laundromat -- and when I do, I often
        bring a list of bugs I want to work on. However, the laundromat I go to
        has no connectivity. (This is a good thing, as far as I'm concerned; I'm
        forced to concentrate on nothing but my list of bugs.)
    </li>

    <li>
        Merges. Subversion is a great tool, but merging can be oftentimes
        painful. The tools in git for merging are incredible, and it's rare that
        I run into conflicts. Subversion, on the other hand... oof. While I can
        fix conflicts relatively easily, I like to avoid them in the first
        place. If you need convincing, try "git cherry-pick" sometime.
    </li>

    <li>
        Ease of installation. The ZF sources take a lot of disk space, and when
        I have multiple branches, trunk, and the incubator checked out, the size
        balloons. Additionally, it gets crazy trying to determine what to put on
        the include_path, particularly when running tests. 
    </li>

    <li>
        Prototyping. I often like to work in an experimental branch where I know
        I won't conflict with anyone else -- but also don't care about the
        commit history so long as I merge in the changes I need to trunk. With
        subversion, I have to create a branch, which means adding to the
        repository history needlessly.
    </li>
</ul>

<p>
    Git's svn integration provides a featureset that answers all of my concerns.
    Additionally, the ability to clone a git+svn repository locally allows me to
    have multiple versions available at any given time -- which can be
    tremendously useful when needing to diff tags, test sites against different
    versions, etc.
</p>

<p>
    The easiest way to clone an svn repository for use with git is to use either
    "git svn init" or "git svn clone". With both of these, you point git to an
    SVN repository and tell it some information about the layout (where trunk
    is, and where the branches and tags are), and it creates a local git
    repository that remotes to the svn repository. As an example:
</p>

<div class="example"><pre><code class="language-bash">
% git svn init --trunk=trunk --tags=tags --branches=branches http://framework.zend.com/svn/framework/standard
</code></pre></div>

<p>
    The above would initialize a git repository in the current directory
    pointing at the ZF standard repository. You can simplify the switches to
    simply "--stdlayout", as the above reflects the standard Subversion
    recommendations for repository layout.
</p>

<p><em>
    Note: I used "git svn init", followed by "git svn fetch". This allowed me to
    specify a revision I wanted to start my repository from. Trust me: you do
    not want to try and clone the entire ZF repository. Besides the size of the
    repo, we also changed the layout mid-May 2008 -- which makes cloning
    problematic. Initialize and fetch from a more recent revision; I've
    personally rebuilt my checkout a few times, most recently dating from the
    1.8.0 release at the end of April 2009.
</em></p>

<p>
    There's one place that ZF differs, however, that has posed a problem for me:
    the incubator. We have placed the incubator as follows:
</p>

<pre>
standard
|-- branches/
|-- incubator/
|-- tags/
`-- trunk/
</pre>

<p>
    In other words, it's a sibling to "branches", "tags", and "trunk" -- and
    doesn't fit the normal paradigms. My problem has been how to inform git
    about its location.
</p>

<p>
    The answer proved incredibly simple. In the repository's ".git/config" file,
    you will have the following after repository intialization:
</p>

<div class="example"><pre><code class="language-ini">
[core]
	repositoryformatversion = 0
	filemode = true
	bare = false
	logallrefupdates = true
[svn-remote \&quot;svn\&quot;]
	url = http://framework.zend.com/svn/framework
	fetch = standard/trunk:refs/remotes/trunk
	branches = standard/branches/*:refs/remotes/*
	tags = standard/tags/*:refs/remotes/tags/*
</code></pre></div>

<p>
    To add the incubator, we add an additional svn-remote, point it to the
    incubator, tell it where to start fetching commits, and then checkout the
    new branch.
</p>

<p>
    First, to add the new svn-remote, simply add the following lines to the
    above ".git/config" file:
</p>

<div class="example"><pre><code class="language-ini">
[svn-remote \&quot;incubator\&quot;]
    url = http://framework.zend.com/svn/framework/standard/incubator
    fetch = :refs/remotes/svn-incubator
</code></pre></div>

<p>
    Then, fetch svn commits on the incubator remote from a given commit; I used
    r15241 myself:
</p>

<div class="example"><pre><code class="language-bash">
% git svn fetch incubator -r 15241
</code></pre></div>

<p>
    Then, checkout the incubator branch locally. This works just like any other
    remote branch -- you check out a local branch, and indicate the remote to
    utilize:
</p>

<div class="example"><pre><code class="language-bash">
% git checkout -b incubator svn-incubator
</code></pre></div>

<p>
    The above creates a local "incubator" branch that points to the
    "svn-incubator" remote created in the config file earlier.
</p>

<p>
    Finally, pull in all other commits since that revision using the standard
    svn rebase:
</p>

<div class="example"><pre><code class="language-bash">
% git svn rebase
</code></pre></div>

<p>
    From this point, you can now switch back and forth between the incubator,
    trunk, and any other branches you've created by simply using "svn checkout
    &lt;branchname&gt;".
</p>

<p>
    For those of you ZF developers who are using git or considering doing so, I
    hope this post helps. If you're interested in trying it out, I can also
    provide a tarball of my own repository; drop me an email if you're
    interested (be aware, though, it's not small...).
</p>
EOT;
$entry->setExtended($extended);

return $entry;
