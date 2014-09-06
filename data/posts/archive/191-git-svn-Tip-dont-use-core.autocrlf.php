<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('191-git-svn-Tip-dont-use-core.autocrlf');
$entry->setTitle('git-svn Tip: don\'t use core.autocrlf');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1222272987);
$entry->setUpdated(1222272987);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'git',
  3 => 'subversion',
));

$body =<<<'EOT'
<p>
    I've been playing around with <a href="http://git.or.cz/">Git</a> in the 
    past couple months, and have been really enjoying it. Paired with
    subversion, I get the best of all worlds -- distributed source control when
    I want it (working on new features or trying out performance tuning), and
    non-distributed source control for my public commits.
</p>

<p>
    <a href="http://github.com/guides/dealing-with-newlines-in-git">Github</a> 
    suggests that when working with remote repositories, you turn on the
    autocrlf option, which ensures that changes in line endings do not get
    accounted for when pushing to and pulling from the remote repo.  However,
    when working with git-svn, this actually causes issues. After turning this
    option on, I started getting the error "Delta source ended unexpectedly"
    from git-svn. After a bunch of aimless tinkering, I finally asked myself the
    questions, "When did this start happening?" and, "Have I changed anything
    with Git lately?" Once I'd backed out the config change, all started working
    again.
</p>

<p>
    In summary: don't use "git config --global core.autocrlf true" when using
    git-svn.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;