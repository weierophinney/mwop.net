---
id: 222-Cloning-the-ZF-SVN-repository-in-Git
author: matthew
title: 'Cloning the ZF SVN repository in Git'
draft: false
public: true
created: '2009-08-31T12:54:37-04:00'
updated: '2009-08-31T13:41:53-04:00'
tags:
    0: php
    2: 'zend framework'
---
I've been using [Git](http://git-scm.com/) for around a year now. My interest in
it originally was to act as a replacement for
[SVK](http://svk.bestpractical.com/), with which I'd had some bad experiences
(when things go wrong with svk, they go very wrong). Why was I using a
distributed version control system, though?

<!--- EXTENDED -->

- I travel several times a year for work. Oftentimes I have long layovers, or
  hotels and/or conference venues with spotty or pricey internet connectivity. I
  need a way to continue working such that I can continue making atomic commits
  without needing to worry about whether or not I have network access.
- I work from home. This gives me great flexibility in terms of where I work.
  Sometimes I work from the laundromat — and when I do, I often bring a list of
  bugs I want to work on. However, the laundromat I go to has no connectivity.
  (This is a good thing, as far as I'm concerned; I'm forced to concentrate on
  nothing but my list of bugs.)
- Merges. Subversion is a great tool, but merging can be oftentimes painful. The
  tools in git for merging are incredible, and it's rare that I run into
  conflicts. Subversion, on the other hand… oof. While I can fix conflicts
  relatively easily, I like to avoid them in the first place. If you need
  convincing, try `git cherry-pick` sometime.
- Ease of installation. The ZF sources take a lot of disk space, and when I have
  multiple branches, trunk, and the incubator checked out, the size balloons.
  Additionally, it gets crazy trying to determine what to put on the
  `include_path`, particularly when running tests.
- Prototyping. I often like to work in an experimental branch where I know I
  won't conflict with anyone else — but also don't care about the commit history
  so long as I merge in the changes I need to trunk. With subversion, I have to
  create a branch, which means adding to the repository history needlessly.

Git's svn integration provides a featureset that answers all of my concerns.
Additionally, the ability to clone a `git+svn` repository locally allows me to
have multiple versions available at any given time — which can be tremendously
useful when needing to diff tags, test sites against different versions, etc.

The easiest way to clone an svn repository for use with git is to use either
`git svn init` or `git svn clone`. With both of these, you point git to an SVN
repository and tell it some information about the layout (where trunk is, and
where the branches and tags are), and it creates a local git repository that
remotes to the svn repository. As an example:

```bash
$ git svn init --trunk=trunk --tags=tags --branches=branches http://framework.zend.com/svn/framework/standard
```

The above would initialize a git repository in the current directory pointing at
the ZF standard repository. You can simplify the switches to simply
`--stdlayout`, as the above reflects the standard Subversion recommendations for
repository layout.

*Note: I used `git svn init`, followed by `git svn fetch`. This allowed me to
specify a revision I wanted to start my repository from. Trust me: you do not
want to try and clone the entire ZF repository. Besides the size of the repo, we
also changed the layout mid-May 2008 — which makes cloning problematic.
Initialize and fetch from a more recent revision; I've personally rebuilt my
checkout a few times, most recently dating from the 1.8.0 release at the end of
April 2009.*

There's one place that ZF differs, however, that has posed a problem for me: the
incubator. We have placed the incubator as follows:

```
standard
|-- branches/
|-- incubator/
|-- tags/
`-- trunk/
```

In other words, it's a sibling to "branches", "tags", and "trunk" — and doesn't
fit the normal paradigms. My problem has been how to inform git about its
location.

The answer proved incredibly simple. In the repository's `.git/config` file, you
will have the following after repository intialization:

```git
[core]
    repositoryformatversion = 0
    filemode = true
    bare = false
    logallrefupdates = true
[svn-remote "svn"]
    url = http://framework.zend.com/svn/framework
    fetch = standard/trunk:refs/remotes/trunk
    branches = standard/branches/*:refs/remotes/*
    tags = standard/tags/*:refs/remotes/tags/*
```

To add the incubator, we add an additional `svn-remote`, point it to the
incubator, tell it where to start fetching commits, and then checkout the new
branch.

First, to add the new `svn-remote`, simply add the following lines to the above
`.git/config` file:

```git
[svn-remote "incubator"]
    url = http://framework.zend.com/svn/framework/standard/incubator
    fetch = :refs/remotes/svn-incubator
```

Then, fetch svn commits on the incubator remote from a given commit; I used
r15241 myself:

```bash
$ git svn fetch incubator -r 15241
```

Then, checkout the incubator branch locally. This works just like any other
remote branch — you check out a local branch, and indicate the remote to utilize:

```bash
$ git checkout -b incubator svn-incubator
```

The above creates a local "incubator" branch that points to the "svn-incubator"
remote created in the config file earlier.

Finally, pull in all other commits since that revision using the standard svn rebase:

```bash
$ git svn rebase
```

From this point, you can now switch back and forth between the incubator, trunk,
and any other branches you've created by simply using `svn checkout <branchname>`.

For those of you ZF developers who are using git or considering doing so, I hope
this post helps. If you're interested in trying it out, I can also provide a
tarball of my own repository; drop me an email if you're interested (be aware,
though, it's not small…).
