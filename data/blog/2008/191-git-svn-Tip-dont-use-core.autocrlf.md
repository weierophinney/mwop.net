---
id: 191-git-svn-Tip-dont-use-core.autocrlf
author: matthew
title: 'git-svn Tip: don''t use core.autocrlf'
draft: false
public: true
created: '2008-09-24T12:16:27-04:00'
updated: '2008-09-24T12:16:27-04:00'
tags:
    0: programming
    1: git
    3: subversion
---
I've been playing around with [Git](http://git.or.cz/) in the past couple
months, and have been really enjoying it. Paired with subversion, I get the
best of all worlds — distributed source control when I want it (working on new
features or trying out performance tuning), and non-distributed source control
for my public commits.

[Github](http://github.com/guides/dealing-with-newlines-in-git) suggests that
when working with remote repositories, you turn on the `autocrlf` option, which
ensures that changes in line endings do not get accounted for when pushing to
and pulling from the remote repo. However, when working with `git-svn`, this
actually causes issues. After turning this option on, I started getting the
error "Delta source ended unexpectedly" from `git-svn`. After a bunch of aimless
tinkering, I finally asked myself the questions, "When did this start
happening?" and, "Have I changed anything with Git lately?" Once I'd backed out
the config change, all started working again.

In summary: don't use `git config --global core.autocrlf true` when using `git-svn`.
