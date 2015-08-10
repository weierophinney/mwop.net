<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-05-15-splitting-components-with-git');
$entry->setTitle('Splitting the ZF2 Components');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-05-15 19:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-05-15 19:30', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'git',
  'php',
  'zend framework',
));

$body =<<<'EOT'
<p>
  Today we accomplished one of the major goals towards Zend Framework 3: 
  splitting the various components into their own repositories. This proved to 
  be a huge challenge, due to the amount of history in our repository (the git 
  repository has history going back to 2009, around the time ZF 1.8 was 
  released!), and the goals we had for what component repositories should look 
  like. This is the story of how we made it happen.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Why split them at all?</h2>

<p>
  "But you can already install components individually!"
</p>

<p>
  True, but if you knew how that occurs, you'd cringe. We've tried a variety of
  solutions, and every single one has failed us at some point or another,
  typically when we move to a new minor version of the framework, but
  occasionally even on trivial bugfix releases. We've tried <code>filter-branch</code>
  with <code>subdirectory-filter</code>, we've tried <code>subtree split</code>,
  and even <a href="https://github.com/dflydev/git-subsplit">subsplit</a>. We've used
  manual scripts that rsync the contents of each commit and create a reference commit.
  Our current version is a combination of several approaches, but we've found we
  must run it manually and verify the results before pushing, as we've had a number
  of situations, as recently as the 2.4.0 release, where contents were not correct.
</p>

<p>
  On top of all this, there's another concern: why do all components get bumped
  in version, even when no changes are present? As an example, a number of components
  have had zero new <em>features</em> since the 2.0 release; they're either stable,
  or have smaller user bases. It doesn't make sense to bump their versions, but
  they get bumped regardless whenever we do a new release of the framework. When
  we start considering a new major version of the framework, it doesn't necessarily
  make sense to bump such components, as there will be literally zero breaking
  changes, and, in many cases, no new features.
</p>
  
<p>
  In other cases, such as the <code>EventManager</code>, <code>ServiceManager</code>,
  and a handful of other components, we know that these will require major versions
  due to necessary architectural changes. However, as long as we're
  still developing minor release branches of the framework, we cannot have meaningful
  development on those features due to the complexities of keeping changes in sync
  between branches.
</p>

<p>
  In short, we'd like to be able to version the individual components separately,
  in their own cycles.
</p>

<p>
  On top of that, when we look at maintenance, having a monolithic repository poses
  a challenge: we have to limit the number of developers with commit rights to ensure
  that those who <em>can</em> commit are aware of the impact a change might have
  across the framework. This means that a number of developers with time and energy
  to spend on improving a single component or small subset of components are hampered
  by how quickly their changes can be reviewed by the maintainers.
</p>

<p>
  Splitting the components gives us the opportunity to expand the number of contributors
  with commit access. The framework itself can pin to specific versions of components,
  and maintainers with commit access to the <em>framework</em> can review and change
  those versions based on integration and smoke tests. In the meantime, a larger
  set of contributors can be gradually improving the individual components, and <em>users</em>
  can selectively adopt those new versions into their applications, on their own
  review cycles.
</p>

<p>
  In the end:
</p>

<ul>
  <li>We get components that follow <a href="http://semver.org">Semantic Versioning</a> properly.</li>
  <li>We get accelerated development in components that need it.</li>
  <li>We expand the number of active, able maintainers.</li>
  <li>We enable users to adopt new features at their own pace.</li>
  <li>We retain framework stability.</li>
</ul>

<h2>The Goal</h2>

<p>
  Since we branched ZF2 development, our repository has looked something like the following:
</p>

<pre><code>
.coveralls.yml
.gitattributes
.gitignore
.php_cs
.travis.yml
bin/
CHANGELOG.md
composer.json
CONTRIBUTING.md
demos/
INSTALL.md
library/
    Zend/
        {component directories}
LICENSE.txt
README-GIT.md
README.md
resources/
tests/
    _autoload.php
    Bootstrap.php
    phpunit.xml.dist
    run-tests.php
    run-tests.sh
    TestConfiguration.php.dist
    TestConfiguration.php.travis
    ZendTest/
        {component directories}
</code></pre>

<p>
  The structure follows <a href="http://www.php-fig.org/psr/psr-0/">PSR-0</a>, with
  each component below the <code>library/Zend/</code> directory.
</p>

<p>
  The goal is to have individual component repositories, each with the following structure:
</p>

<pre><code>
.coveralls.yml
.gitattributes
.gitignore
.php_cs
.travis.yml
composer.json
CONTRIBUTING.md
src/
LICENSE.txt
phpunit.xml.dist
phpunit.xml.travis
README.md
test/
    bootstrap.php
    {component test cases}
</code></pre>

<p>
  In the above structure, note the following differences:
</p>

<ul>
  <li>Source and unit test files now follow <a
      href="http://www.php-fig.org/psr/psr-4/">PSR-4</a>, and can be found
    directly beneath the new <code>src/</code> and <code>test/</code>
    directories (which replace <code>library/</code> and <code>tests/</code>,
    respectively), without any directory nesting based on namespace (unless any
    subnamespaces are present).</li>
  <li>The <code>README.md</code> file will need to be specific to the
    component. Additionally, it can incorporate what was in the
    <code>INSTALL.md</code> file originally.</li>
  <li>The <code>composer.json</code> file will need to be for the component,
    not the framework. Additionally, we don't currently list dev/testing
    dependencies in our component repos, so those will need to be added.</li>
  <li>The <code>TestConfiguration.php.*</code> files define constants
    referenced by the unit tests; those can be migrated to the
    <code>phpunit.xml.*</code> files &mdash; which we can move to the project
    root to simplify testing.</li>
  <li>The <code>.travis.yml</code> file can be streamlined, as we're now only
    testing one component.</li>
  <li>Most testing infrastructure can be removed, as it's around simplifying
    running tests for individual components within the larger framework. The <code>Bootstrap.php</code>
    gets renamed to <code>bootstrap.php</code> to avoid being confused with
    unit test files.</li>
  <li><code>README-GIT.md</code> gets replaced with a lengthier
    <code>CONTRIBUTING.md</code> file.</li>
</ul>

<p>
  On top of all this, we had the following requirements:
</p>

<ul>
  <li>The components <strong>MUST</strong> have full history from 2.0.0rc7
    forward. This is so those working on the components can see the
    <em>why</em> and <em>who</em> behind commits.</li>
  <li>Commit messages <strong>MUST</strong> reference original issues and pull
    requests on the ZF2 repository; again, this is to facilitate the
    <em>why</em> behind changes.</li>
  <li>Ideally, history should contain <em>only</em> history for the given
    component.</li>
  <li>The directory structure in <em>each</em> commit, including (and
    especially!) tags, <strong>MUST</strong> follow the proposed
    structure.</li>
</ul>

<h2>How we got there</h2>

<p>
  One of the huge benefits to using Git is the ability to rewrite history.
  (It's also one of its scariest features.) It provides a number of facilities
  for doing so, from <code>rebase</code> to grafts to <code>subtree</code> to
  <code>filter-branch</code>. In our component split research, we evaluated
  several solutions.
</p>

<h3>Grafts</h3>

<p>
  <a href="https://git.wiki.kernel.org/index.php/GraftPoint">Grafts</a> provide
  a way to merge two different lines of history together, but, for our purposes,
  also allow us to <em>prune</em> history. Why would we do this? Because we
  don't really need history prior to 2.0.0 development at this point. In large
  part, this is because it's irrelevant; files were moved around and
  changed so much between forking from the 1.X tree and 2.0 that tracing the 
  history is quite difficult.
</p>

<p>
  I eventually found a methodology for pruning that looks like this:
</p>

<pre><code class="lang-bash">
$ echo bb50be26b24a9e0e62a8f4abecce53259d707b61 > .git/info/grafts
$ git filter-branch --tag-name-filter cat -- --all
$ git reflog expire --expire=now --all
$ git gc --prune=now --aggressive
$ rm .git/info/grafts
</code></pre>

<p>
  It's supposed to essentially remove history before the given sha1. What I
  found was that by itself, I noticed little to no change in the repository,
  other than size; I could still reach earlier commits. However, when coupled
  with the final techniques we used, it meant that we effectively saw no commits
  prior to this point.
</p>

<h3>subtree</h3>

<p>
  <code>git subtree</code> is a "contributed" git command; it's not available
  in default distributions of git, but often available as an add-on package; if
  you install git from source, it's in the <code>contrib</code> tree, where you
  can compile and install it. Subtree provides a rich set of functionality
  around dealing with repository subtrees, allowing you to split them off, add
  subtrees from other projects, and even push commits back and forth between
  them.
</p>

<p>
  At first blush, it seems like an ideal, simple solution:
</p>

<ul>
  <li>Split each of the <code>library/</code> and <code>tests/</code> component
    subtrees into their own branches.</li>
  <li>Create a new repository, and add each of the above as subtrees.</li>
</ul>

<pre><code class="lang-bash">
$ git clone zendframework/zf2
$ git init zend-http
$ cd zf2
$ git subtree split --prefix=library/Zend/Http -b src
$ git subtree split --prefix=tests/ZendTest/Http -b test
$ cd ../zend-http
$ # add in basic assets, and create initial commit
$ git remote add zf2 ../zf2
$ git subtree add --prefix=src/ zf2 src
$ git subtree add --prefix=test/ zf2 test
</code></pre>

<p>
  Indeed, if you do the above, when done, the directory looks exactly like it
  should! <strong>However</strong>, the history is all wrong; if you check out
  any tags, you get the full ZF2 tree for the tag. As such, subtree fails one of
  the most important criteria right off the bat: that each commit and tag
  represent <em>only</em> the component.
</p>

<h3>subdirectory-filter</h3>

<p>
  <code>subdirectory-filter</code> is one of the <code>git filter-branch</code>
  strategies. It operates similarly to <code>subtree</code>, but also rewrites
  history. We used <a href="https://gist.github.com/ralphschindler/9494556">this
  approach</a> when splitting the various "service" (API wrapper) components
  from the main repository prior to the first ZF2 stable release.
</p>

<p>
  The basic idea is similar to that of <code>subtree</code>; the difference is
  that you have to begin with separate checkouts for each of the source and
  tests.
</p>

<pre><code class="lang-bash">
$ git clone zendframework/zf2 zend-http-src
$ git clone zendframework/zf2 zend-http-test
$ cd zend-http-src
$ git filter-branch --subdirectory-filter library/Zend/Http --tag-name-filter cat -- -all
$ cd ../zend-http-test
$ git filter-branch --subdirectory-filter tests/ZendTest/Http --tag-name-filter cat -- -all
$ cd ..
$ git init zend-http
$ cd zend-http
$ # add in basic assets, and create initial commit
$ git remote add -f src ../zend-http-src
$ git remote add -f test ../zend-http-test
$ git merge -s ours --no-commit src/master
$ git read-tree -u --prefix=src/ src/master
$ git commit -m 'Merging src tree'
$ git merge -s ours --no-commit test/master
$ git read-tree -u --prefix=test/ test/master
$ git commit -m 'Merging test tree'
</code></pre>

<p>
  Again, this looks great at first blush; all the contents for the given
  component are rewritten perfectly. But when you start looking at previous tags
  and commits, you see an interesting picture: based on the commit and which
  remote you added first, you'll see a completely different directory structure.
  Like <code>subtree</code>, this fails our criteria that the repo be in a
  usable state at any given commit.
</p>

<h3>tree-filter</h3>

<p>
  Like <code>subdirectory-filter</code>, <code>tree-filter</code> is a
  <code>filter-branch</code> strategy. <code>tree-filter</code> allows you to
  rewrite the tree contents <em>any way you want</em>, while retaining the
  commit message and metadata. This turned out to be what we were looking for!
</p>

<p>
  However, there were a few more pieces we needed to address:
</p>

<ul>
  <li>Rewriting commit messages referencing issues and pull requests to link to
    the main ZF2 repository.</li>
  <li>Pruning empty commits.</li>
  <li>Ensuring tags contain the expected tree.</li>
</ul>

<p>
  Fortunately, <code>filter-branch</code> has other strategies for just these
  purposes:
</p>

<ul>
  <li><code>msg-filter</code> allows you to rewrite commit messages.</li>
  <li><code>commit-filter</code> provides tools for detecting and removing empty
    commits.</li>
  <li><code>tag-name-filter</code> ensures that tag references are rewritten
    when the parent commits change or are removed.</li>
</ul>

<p>
  So, what we ended up with was something like the following:
</p>

<pre><code class="lang-bash">
git filter-branch -f \
    --tree-filter "php /path/to/tree-filter.php" \
    --msg-filter "sed -re 's/(^|[^a-zA-Z])(\#[1-9][0-9]*)/\1zendframework\/zf2\2/g'" \
    --commit-filter 'git_commit_non_empty_tree "$@"' \
    --tag-name-filter cat \
    -- --all
</code></pre>

<p>
  <code>/path/to/tree-filter.php</code> is a script that contains the logic for
  re-arranging the directory structure, as well as rewriting the contents of
  files as necessary (e.g., rewriting the contents of
  <code>composer.json</code>, or filling in the name of the component in the
  <code>CONTRIBUTING.md</code>). The <code>msg-filter</code> looks for issue and
  pull request identifiers (a <code>#</code> character followed by one or more
  digits), and rewrites them to reference the repository. The
  <code>commit-filter</code> checks to see if the repository contents have
  changed in this commit, and, if not, instructs <code>git</code> to ignore the
  commit (and, since <code>tree-filter</code> always executes before
  <code>commit-filter</code>, the comparison is always between rewritten trees).
  The <code>tag-name-filter</code> <strong>MUST</strong> be present, and
  essentially just ensures that the tag is rewritten; if absent, tags are not
  rewritten, and refer to the original contents!
</p>

<h3>Stumbling blocks</h3>

<p>
  We had a few stumbling blocks getting the above to work. The first was that,
  for purposes of testing, we had to specify a <em>commit range</em>, instead of <code>--
  --all</code>. This was necessary because of the size of the repo; at ~27k
  commits, running over every single commit can take between 5 and 12 hours,
  depending on git version, HDD vs ramdisk, speed of I/O, etc. For small
  subsets, we could get consistent results. When we expanded the range, we
  started seeing strange errors, such as some tags not getting written.
</p>

<p>
  To compound the situation, we also made a last minute change to only do
  history from the 2.0.0rc7 tag forward, and this is when things completely fell apart. A
  large number of tags would not get rewritten, the set of malformed tags varied 
  between components, and we couldn't figure out why.
</p>

<p>
  At a certain point, I recalled that <code>git</code> stores commits as a tree,
  and that's when I realized what was happening: when we specified a commit
  range, we were essentially specifying a specific path through the commits. If
  a tag was made on a branch falling outside that path, it would not get
  rewritten.
</p>

<p>
  This meant that the only way to get consistent results that met our criteria
  was to run a test over the full history. Fortunately, sometime around that
  point, a community member, <a href="http://www.renatomefi.com.br/">Renato</a>,
  suggested I try a run using a <a
    href="http://en.wikipedia.org/wiki/Tmpfs">tmpfs filesystem</a> &mdash;
  essentially a ramdisk. This sped up runs by a factor of 2, and I was able to
  validate my hypothesis within an evening.
</p>

<p>
  Another stumbling block was empty commits. We originally used
  <code>filter-branch</code>'s <code>--prune-empty</code> switch, but found it
  was generally unreliable when used with <code>tree-filter</code>. The solution
  to this problem is the <code>commit-filter</code> as listed above; it did a stellar job.
</p>

<h3>Empty merge commits</h3>

<p>
  There was one lingering issue, however: when inspecting the filtered
  repository, we still had a large number of empty merge commits that had
  nothing to do with the component. After a lot of searching, I found this gem:
</p>

<pre><code class="lang-bash">
$ git filter-branch -f \
> --commit-filter '
>    if [ z$1 = z`git rev-parse $3^{tree}` ];then
>        skip_commit "$@";
>    else
>        git commit-tree "$@";
> fi' \
> --tag-name-filter cat -- --all
$ git reflog expire --expire=now --all
$ git gc --prune=now --aggressive
</code></pre>

<p>
  The above uses a <code>commit-filter</code> which internally uses
  <code>rev-parse</code> to determine if the commit is a merge and that both
  parents are present in the repository; if not, it skips (removes) the commit.
  The <code>reflog expire</code> and <code>gc</code> commands clean up and
  remove any objects in the repository that are now no longer reachable.
</p>

<h2>Final Solution</h2>

<p>
  With a working <code>graft</code>, <code>tree-filter</code>, and
  <code>commit-filter</code> in place, we could finally proceed. We created a
  repository containing all scripts we needed, as well as the assets necessary
  for rewriting the component repository trees. We then had a tool that could be
  executed as simply as:
</p>

<pre><code class="lang-bash">
$ ./bin/split.sh -c Authentication 2>&1 | tee authentication.log
</code></pre>

<p>
  And with that, we could sit back and watch the component get split, and push
  the results when done.
</p>

<p>
  You can see the work in our <a href="https://github.com/zendframework/component-split">component-split</a>
  repository.
</p>

<h2>But what about the speed?</h2>

<p>
  "But didn't you say it takes between 5 and 12 hours to run per component? And
  aren't there something like 50 components? That would take weeks!"
</p>

<p>
  You're quite astute! And for that, we had a secret weapon: a community
  contributor, <a href="https://github.com/gianarb">Gianluca Arbezzano</a> 
  working for an AWS partner, <a href="http://www.corley.it">Corley</a>, which 
  sponsored splitting all components in parallel at once, allowing us to complete
  the entire effort in a single day. I'll let others tell that story, though!
</p>

<h2>The results</h2>

<p>
  I'm quite pleased with the results. The ZF2 repository has ~27k commits, 67
  releases, and over 700 contributors; a clean checkout is around 150MB. As a
  contrast, the rewritten <code>zend-http</code> component repository ended up
  with ~1.7k commits, 50 releases, ~160 contributors, and a clean checkout
  clocks in at 5.4MB! So the individual components are substantially leaner!
  Additionally, they contain all the QA tooling necessary to start developing
  against for those wanting to patch issues or create features, making
  development a simpler process.
</p>

<p>
  The lessons learned:
</p>

<ul>
  <li><code>tree-filter</code> is your friend, if your restructuring involves
    more than one directory and/or adding or removing files.</li>
  <li><code>tag-name-filter</code> <strong>MUST</strong> be used anytime you use
    <code>filter-branch</code>; otherwise your tags may end up invalid!</li>
  <li><code>filter-branch</code> should be used on ranges <em>sparingly</em>,
    and ideally only if you're not worried about tags. In most cases, you want
    to run over the entire history.</li>
  <li><code>commit-filter</code> is your best option for ensuring empty commits
    of any type are stripped, particularly if you're using
    <code>tree-filter</code>; the <code>--prune-empty</code> flag is not
    terribly reliable.</li>
  <li>Always do a full test run. It's tempting to use a commit range to verify
    that your filters work, but the results will differ from running over the
    entire history. Which leads to:</li>
  <li>Schedule plenty of time, particularly if your repository is large. Those
    full test runs will take time, and, if you follow the scientific process and
    make one change at a time, you may need quite a few iterations to get your
    scripts right.</li>
</ul>

<p>
  All-in-all, this was a stressful, time-consuming, thankless task. But I
  <em>am</em> quite happy with the results; our components look like they are
  and were always developed as first-class components, and have a rich history
  referencing their original development as part of the encompassing framework.
</p>

<h2>Kudos!</h2>

<p>
  I cannot thank Gianluca and Corley enough for their generous efforts! What looked
  like a task that would take days and/or weeks happened literally overnight, allowing
  us to complete a major task in Zend Framework 3 development, and setting the stage
  for a ton of new features. Grazie!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
