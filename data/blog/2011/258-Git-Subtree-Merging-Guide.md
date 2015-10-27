---
id: 258-Git-Subtree-Merging-Guide
author: matthew
title: 'Git Subtree Merging Guide'
draft: false
public: true
created: '2011-03-10T09:30:00-05:00'
updated: '2011-03-15T15:34:34-04:00'
tags:
    0: php
    2: subversion
---
I've been investigating ways to incorporate third-party repositories and
libraries into my [Git](http://git-scm.org/) projects. Subversion's
`svn:externals` capabilities are one compelling feature for that particular
VCS, and few, if any, other VCS systems, particularly the DVCS systems, have a
truly viable equivalent. Git `submodules` aren't terrible, but they assume you
want the entire repository — whereas SVN allows you to cherry-pick
subdirectories if desired.

Why might I want to link only a subdirectory? Consider a project with this
structure:

```
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
```

On another project, I want to use ComponentB. With `svn:externals`, this is easy:

```
library/Foo/ComponentB http://repohost/svn/trunk/library/Foo/ComponentB
```

and now the directory is added and tracked.

With Git, it's a different story. One solution I've found is using
[git-subtree](https://github.com/apenwarr/git-subtree), an extension to Git. It
takes a bit more effort to setup than `svn:externals`, but offers the benefits
of easily freezing on a specific commit, and squashing all changes into a
single commit.

[Jon Whitcraft](http://h2ik.co) recently had some questions about how to use
it, and I answered him via email. Evidently what I gave him worked for him, as
he then requested if he could post my guide — which
[you can find here](http://h2ik.co/2011/03/having-fun-with-git-subtree/).
