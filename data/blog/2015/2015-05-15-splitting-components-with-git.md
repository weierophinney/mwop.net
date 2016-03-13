---
id: 2015-05-15-splitting-components-with-git
author: matthew
title: 'Splitting the ZF2 Components'
draft: false
public: true
created: '2015-05-15T19:30:00-05:00'
updated: '2015-05-15T19:30:00-05:00'
tags:
    - git
    - php
    - 'zend framework'
---
Today we accomplished one of the major goals towards Zend Framework 3:
splitting the various components into their own repositories. This proved to be
a huge challenge, due to the amount of history in our repository (the git
repository has history going back to 2009, around the time ZF 1.8 was
released!), and the goals we had for what component repositories should look
like. This is the story of how we made it happen.

<!--- EXTENDED -->

Why split them at all?
----------------------

"But you can already install components individually!"

True, but if you knew how that occurs, you'd cringe. We've tried a variety of
solutions, and every single one has failed us at some point or another,
typically when we move to a new minor version of the framework, but
occasionally even on trivial bugfix releases. We've tried `filter-branch` with
`subdirectory-filter`, we've tried `subtree split`, and even
[subsplit](https://github.com/dflydev/git-subsplit). We've used manual scripts
that rsync the contents of each commit and create a reference commit. Our
current version is a combination of several approaches, but we've found we must
run it manually and verify the results before pushing, as we've had a number of
situations, as recently as the 2.4.0 release, where contents were not correct.

On top of all this, there's another concern: why do all components get bumped
in version, even when no changes are present? As an example, a number of
components have had zero new *features* since the 2.0 release; they're either
stable, or have smaller user bases. It doesn't make sense to bump their
versions, but they get bumped regardless whenever we do a new release of the
framework. When we start considering a new major version of the framework, it
doesn't necessarily make sense to bump such components, as there will be
literally zero breaking changes, and, in many cases, no new features.

In other cases, such as the `EventManager`, `ServiceManager`, and a handful of
other components, we know that these will require major versions due to
necessary architectural changes. However, as long as we're still developing
minor release branches of the framework, we cannot have meaningful development
on those features due to the complexities of keeping changes in sync between
branches.

In short, we'd like to be able to version the individual components separately,
in their own cycles.

On top of that, when we look at maintenance, having a monolithic repository
poses a challenge: we have to limit the number of developers with commit rights
to ensure that those who *can* commit are aware of the impact a change might
have across the framework. This means that a number of developers with time and
energy to spend on improving a single component or small subset of components
are hampered by how quickly their changes can be reviewed by the maintainers.

Splitting the components gives us the opportunity to expand the number of
contributors with commit access. The framework itself can pin to specific
versions of components, and maintainers with commit access to the *framework*
can review and change those versions based on integration and smoke tests. In
the meantime, a larger set of contributors can be gradually improving the
individual components, and *users* can selectively adopt those new versions
into their applications, on their own review cycles.

In the end:

- We get components that follow [Semantic Versioning](http://semver.org) properly.
- We get accelerated development in components that need it.
- We expand the number of active, able maintainers.
- We enable users to adopt new features at their own pace.
- We retain framework stability.

The Goal
--------

Since we branched ZF2 development, our repository has looked something like the
following:

```
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
```

The structure follows [PSR-0](http://www.php-fig.org/psr/psr-0/), with each
component below the `library/Zend/` directory.

The goal is to have individual component repositories, each with the following
structure:

```
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
```

In the above structure, note the following differences:

- Source and unit test files now follow
  [PSR-4](http://www.php-fig.org/psr/psr-4/), and can be found directly beneath
  the new `src/` and `test/` directories (which replace `library/` and
  `tests/`, respectively), without any directory nesting based on namespace
  (unless any subnamespaces are present).
- The `README.md` file will need to be specific to the component. Additionally,
  it can incorporate what was in the `INSTALL.md` file originally.
- The `composer.json` file will need to be for the component, not the
  framework. Additionally, we don't currently list dev/testing dependencies in
  our component repos, so those will need to be added.
- The `TestConfiguration.php.*` files define constants referenced by the unit
  tests; those can be migrated to the `phpunit.xml.*` files — which we can move
  to the project root to simplify testing.
- The `.travis.yml` file can be streamlined, as we're now only testing one
  component.
- Most testing infrastructure can be removed, as it's around simplifying
  running tests for individual components within the larger framework. The
  `Bootstrap.php` gets renamed to `bootstrap.php` to avoid being confused with
  unit test files.
- `README-GIT.md` gets replaced with a lengthier `CONTRIBUTING.md` file.

On top of all this, we had the following requirements:

- The components **MUST** have full history from 2.0.0rc7 forward. This is so
  those working on the components can see the *why* and *who* behind commits.
- Commit messages **MUST** reference original issues and pull requests on the
  ZF2 repository; again, this is to facilitate the *why* behind changes.
- Ideally, history should contain *only* history for the given component.
- The directory structure in *each* commit, including (and especially!) tags,
  **MUST** follow the proposed structure.

How we got there
----------------

One of the huge benefits to using Git is the ability to rewrite history. (It's
also one of its scariest features.) It provides a number of facilities for
doing so, from `rebase` to grafts to `subtree` to `filter-branch`. In our
component split research, we evaluated several solutions.

### Grafts

[Grafts](https://git.wiki.kernel.org/index.php/GraftPoint) provide a way to
merge two different lines of history together, but, for our purposes, also
allow us to *prune* history. Why would we do this? Because we don't really need
history prior to 2.0.0 development at this point. In large part, this is
because it's irrelevant; files were moved around and changed so much between
forking from the 1.X tree and 2.0 that tracing the history is quite difficult.

I eventually found a methodology for pruning that looks like this:

```bash
$ echo bb50be26b24a9e0e62a8f4abecce53259d707b61 > .git/info/grafts
$ git filter-branch --tag-name-filter cat -- --all
$ git reflog expire --expire=now --all
$ git gc --prune=now --aggressive
$ rm .git/info/grafts
```

It's supposed to essentially remove history before the given sha1. What I found
was that by itself, I noticed little to no change in the repository, other than
size; I could still reach earlier commits. However, when coupled with the final
techniques we used, it meant that we effectively saw no commits prior to this
point.

### subtree

`git subtree` is a "contributed" git command; it's not available in default
distributions of git, but often available as an add-on package; if you install
git from source, it's in the `contrib` tree, where you can compile and install
it. Subtree provides a rich set of functionality around dealing with repository
subtrees, allowing you to split them off, add subtrees from other projects, and
even push commits back and forth between them.

At first blush, it seems like an ideal, simple solution:

- Split each of the `library/` and `tests/` component subtrees into their own branches.
- Create a new repository, and add each of the above as subtrees.

```bash
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
```

Indeed, if you do the above, when done, the directory looks exactly like it
should! **However**, the history is all wrong; if you check out any tags, you
get the full ZF2 tree for the tag. As such, subtree fails one of the most
important criteria right off the bat: that each commit and tag represent *only*
the component.

### subdirectory-filter

`subdirectory-filter` is one of the `git filter-branch` strategies. It operates
similarly to `subtree`, but also rewrites history. We used [this approach](https://gist.github.com/ralphschindler/9494556)
when splitting the various "service" (API wrapper) components from the main
repository prior to the first ZF2 stable release.

The basic idea is similar to that of `subtree`; the difference is that you have
to begin with separate checkouts for each of the source and tests.

```bash
$ git clone zendframework/zf2 zend-http-src
$ git clone zendframework/zf2 zend-http-test
$ cd zend-http-src
$ git filter-branch --subdirectory-filter library/Zend/Http --tag-name-filter cat -- -all
$ cd ../zend-http-test
$ git filter-branch --subdirectory-filter tests/ZendTest/Http --tag-name-filter cat -- -all
$ cd ..
$ git init zend-http
$ cd zend-http
# add in basic assets, and create initial commit
$ git remote add -f src ../zend-http-src
$ git remote add -f test ../zend-http-test
$ git merge -s ours --no-commit src/master
$ git read-tree -u --prefix=src/ src/master
$ git commit -m 'Merging src tree'
$ git merge -s ours --no-commit test/master
$ git read-tree -u --prefix=test/ test/master
$ git commit -m 'Merging test tree'
```

Again, this looks great at first blush; all the contents for the given
component are rewritten perfectly. But when you start looking at previous tags
and commits, you see an interesting picture: based on the commit and which
remote you added first, you'll see a completely different directory structure.
Like `subtree`, this fails our criteria that the repo be in a usable state at
any given commit.

### tree-filter

Like `subdirectory-filter`, `tree-filter` is a `filter-branch` strategy.
`tree-filter` allows you to rewrite the tree contents *any way you want*, while
retaining the commit message and metadata. This turned out to be what we were
looking for!

However, there were a few more pieces we needed to address:

- Rewriting commit messages referencing issues and pull requests to link to the
  main ZF2 repository.
- Pruning empty commits.
- Ensuring tags contain the expected tree.

Fortunately, `filter-branch` has other strategies for just these purposes:

- `msg-filter` allows you to rewrite commit messages.
- `commit-filter` provides tools for detecting and removing empty commits.
- `tag-name-filter` ensures that tag references are rewritten when the parent commits change or are removed.

So, what we ended up with was something like the following:

```bash
git filter-branch -f \
    --tree-filter "php /path/to/tree-filter.php" \
    --msg-filter "sed -re 's/(^|[^a-zA-Z])(\#[1-9][0-9]*)/zendframework\/zf2/g'" \
    --commit-filter 'git_commit_non_empty_tree "$@"' \
    --tag-name-filter cat \
    -- --all
```

`/path/to/tree-filter.php` is a script that contains the logic for re-arranging
the directory structure, as well as rewriting the contents of files as
necessary (e.g., rewriting the contents of `composer.json`, or filling in the
name of the component in the `CONTRIBUTING.md`). The `msg-filter` looks for
issue and pull request identifiers (a `#` character followed by one or more
digits), and rewrites them to reference the repository. The `commit-filter`
checks to see if the repository contents have changed in this commit, and, if
not, instructs `git` to ignore the commit (and, since `tree-filter` always
executes before `commit-filter`, the comparison is always between rewritten
trees). The `tag-name-filter` **MUST** be present, and essentially just ensures
that the tag is rewritten; if absent, tags are not rewritten, and refer to the
original contents!

### Stumbling blocks

We had a few stumbling blocks getting the above to work. The first was that,
for purposes of testing, we had to specify a *commit range*, instead of `-- --all`.
This was necessary because of the size of the repo; at ~27k commits, running
over every single commit can take between 5 and 12 hours, depending on git
version, HDD vs ramdisk, speed of I/O, etc. For small subsets, we could get
consistent results. When we expanded the range, we started seeing strange
errors, such as some tags not getting written.

To compound the situation, we also made a last minute change to only do history
from the 2.0.0rc7 tag forward, and this is when things completely fell apart. A
large number of tags would not get rewritten, the set of malformed tags varied
between components, and we couldn't figure out why.

At a certain point, I recalled that `git` stores commits as a tree, and that's
when I realized what was happening: when we specified a commit range, we were
essentially specifying a specific path through the commits. If a tag was made
on a branch falling outside that path, it would not get rewritten.

This meant that the only way to get consistent results that met our criteria
was to run a test over the full history. Fortunately, sometime around that
point, a community member, [Renato](http://www.renatomefi.com.br/), suggested I
try a run using a [tmpfs filesystem](http://en.wikipedia.org/wiki/Tmpfs) —
essentially a ramdisk. This sped up runs by a factor of 2, and I was able to
validate my hypothesis within an evening.

Another stumbling block was empty commits. We originally used `filter-branch`'s
`--prune-empty` switch, but found it was generally unreliable when used with
`tree-filter`. The solution to this problem is the `commit-filter` as listed
above; it did a stellar job.

### Empty merge commits

There was one lingering issue, however: when inspecting the filtered
repository, we still had a large number of empty merge commits that had nothing
to do with the component. After a lot of searching, I found this gem:

```bash
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
```

The above uses a `commit-filter` which internally uses `rev-parse` to determine
if the commit is a merge and that both parents are present in the repository;
if not, it skips (removes) the commit. The `reflog expire` and `gc` commands
clean up and remove any objects in the repository that are now no longer
reachable.

Final Solution
--------------

With a working `graft`, `tree-filter`, and `commit-filter` in place, we could
finally proceed. We created a repository containing all scripts we needed, as
well as the assets necessary for rewriting the component repository trees. We
then had a tool that could be executed as simply as:

```bash
$ ./bin/split.sh -c Authentication 2>&1 | tee authentication.log
```

And with that, we could sit back and watch the component get split, and push
the results when done.

You can see the work in our
[component-split](https://github.com/zendframework/component-split) repository.

But what about the speed?
-------------------------

"But didn't you say it takes between 5 and 12 hours to run per component? And
aren't there something like 50 components? That would take weeks!"

You're quite astute! And for that, we had a secret weapon: a community
contributor, [Gianluca Arbezzano](https://github.com/gianarb) working for an
AWS partner, [Corley](http://www.corley.it), which sponsored splitting all
components in parallel at once, allowing us to complete the entire effort in a
single day. I'll let others tell that story, though!

The results
-----------

I'm quite pleased with the results. The ZF2 repository has ~27k commits, 67
releases, and over 700 contributors; a clean checkout is around 150MB. As a
contrast, the rewritten `zend-http` component repository ended up with ~1.7k
commits, 50 releases, ~160 contributors, and a clean checkout clocks in at
5.4MB! So the individual components are substantially leaner! Additionally,
they contain all the QA tooling necessary to start developing against for those
wanting to patch issues or create features, making development a simpler
process.

The lessons learned:

- `tree-filter` is your friend, if your restructuring involves more than one
  directory and/or adding or removing files.
- `tag-name-filter` **MUST** be used anytime you use `filter-branch`; otherwise
  your tags may end up invalid!
- `filter-branch` should be used on ranges *sparingly*, and ideally only if
  you're not worried about tags. In most cases, you want to run over the entire
  history.
- `commit-filter` is your best option for ensuring empty commits of any type
  are stripped, particularly if you're using `tree-filter`; the `--prune-empty`
  flag is not terribly reliable.
- Always do a full test run. It's tempting to use a commit range to verify that
  your filters work, but the results will differ from running over the entire
  history. Which leads to:
- Schedule plenty of time, particularly if your repository is large. Those full
  test runs will take time, and, if you follow the scientific process and make
  one change at a time, you may need quite a few iterations to get your scripts
  right.

All-in-all, this was a stressful, time-consuming, thankless task. But I *am*
quite happy with the results; our components look like they are and were always
developed as first-class components, and have a rich history referencing their
original development as part of the encompassing framework.

Kudos!
------

I cannot thank Gianluca and Corley enough for their generous efforts! What
looked like a task that would take days and/or weeks happened literally
overnight, allowing us to complete a major task in Zend Framework 3
development, and setting the stage for a ton of new features. Grazie!
