<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('258-Git-Subtree-Merging-Guide');
$entry->setTitle('Git Subtree Merging Guide');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1299767400);
$entry->setUpdated(1300217674);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'subversion',
));

$body =<<<'EOT'
<p>
I've been investigating ways to incorporate third-party repositories and
libraries into my <a href="http://git-scm.org/">Git</a> projects. Subversion's
<code>svn:externals</code> capabilities are one compelling feature for that particular VCS,
and few, if any, other VCS systems, particularly the DVCS systems, have a truly
viable equivalent. Git <code>submodules</code> aren't terrible, but they assume you want the
entire repository -- whereas SVN allows you to cherry-pick subdirectories if
desired.
</p>

<p>
Why might I want to link only a subdirectory? Consider a project with this
structure:
</p>

<pre>
docs/
    api/
    manual/
        html/
        module_specs/
library/
    Foo/
        ComponentA/
        ComponentB/
tests/
    Foo/
        ComponentA/
        ComponentB/
</pre>

<p>
On another project, I want to use ComponentB. With <code>svn:externals</code>, this is
easy:
</p>

<pre>
library/Foo/ComponentB http://repohost/svn/trunk/library/Foo/ComponentB
</pre>

<p>
and now the directory is added and tracked.
</p>

<p>
With Git, it's a different story. One solution I've found is using
<a href="https://github.com/apenwarr/git-subtree">git-subtree</a>, an extension to Git. It
takes a bit more effort to setup than <code>svn:externals</code>, but offers the benefits
of easily freezing on a specific commit, and squashing all changes into a single
commit.
</p>

<p>
<a href="http://h2ik.co">Jon Whitcraft</a> recently had some questions about how to use it,
and I answered him via email. Evidently what I gave him worked for him, as he
then requested if he could post my guide -- which
<a href="http://h2ik.co/2011/03/having-fun-with-git-subtree/">you can find here</a>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;