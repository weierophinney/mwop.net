<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('132-svnexternals');
$entry->setTitle('svn:externals');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1168009080);
$entry->setUpdated(1168456115);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    I was recently working with someone who was using Zend Framework in their
    project. To keep things stable and releasable, he was doing an export of
    framework into his repository and checking it in. Since files change so much
    in the ZF project currently, instead of doing an rsync from a checkout into
    his own repository, he decided instead to delete the directory from the
    repository and re-add it everytime he was updating framework.
</p>
<p>
    This seemed really inefficient to me, especially considering that it made it
    incredibly difficult to merge changes from his development branch into his
    production branch (deleting and re-adding directories breaks the merge
    process considerably). I knew there had to be a better way.
</p>
<p>
    I'd heard of the svn:externals property before, but never really played with
    it. As it turns out, it exists for just this very type of situation. The
    problem is that the 
    <a href="http://svnbook.red-bean.com/nightly/en/svn-book.html#svn.advanced.externals">documentation of svn:externals</a> 
    in the SVN book doesn't indicate at all how the property should be set, and
    most howto's I've read omit one or more very important details. I finally
    figured things out through some trial and error of my own, so I'm going to
    share the process so others hopefully can learn from the experience as well.
</p>
<p>
    It's actually pretty easy. This assumes that your project layout looks
    something like this:
</p>
<pre>
project/
    branch/
        production/
    tag/
    trunk/
</pre>
<ul>
    <li>In the top of your project trunk, execute the following:
<pre>
svn propedit svn:externals .
</pre>
    </li>
    <li>This will open an editor session. In the file opened by your editor,
    each line indicates a different external svn repo to pull. The first segment
    of the line is the directory where you want the pull to exist. The last
    segment is the svn repo URL to pull. You can have an optional middle
    argument indicating the revision to use. Some examples:
    <ul>
        <li>Pull framework repo from head:
<pre>
framework http://framework.zend.com/svn/framework/trunk
</pre>
        </li>
        <li>Pull framework repo from revision 2616:
<pre>
framework -r2616 http://framework.zend.com/svn/framework/trunk
</pre>
        </li>
    </ul>
    <li>After saving and exiting, update the repo:
<pre>
svn up
</pre>
    </li>
    <li>Commit changes:
<pre>
svn commit
</pre>
    </li>
</ul>
<p>
    One thing to note: any directory you specify for an svn:externals checkout
    should <b>not</b> already exist in your repository. If it does, you will get
    an error like the following:
</p>
<pre>
svn: Working copy 'sharedproject' locked
svn: run 'svn cleanup' to remove locks
</pre>
<p>
    I show using revisions above; you could also pin to tags by simply checkout
    the external repository from a given tag. Either way works well.
</p>
<p>
    Then, when moving from one branch to another, or from the trunk to a branch,
    you simply set a different svn:externals for each branch. For instance, your
    current production might check from one particular revision, but your trunk
    might simply track head; you then simply determine what the current revision
    being used is on your trunk, and update svn:externals in your production
    branch when you're ready to push changes in.
</p>
<p>
    Hope this helps some of you out there!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;