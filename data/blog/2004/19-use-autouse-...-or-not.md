---
id: 19-use-autouse-...-or-not
author: matthew
title: 'use autouse ... or not'
draft: false
public: true
created: '2004-01-24T20:36:17-05:00'
updated: '2004-09-20T13:29:21-04:00'
tags:
    - perl
    - personal
---
Due to my cursory reading in the *Perl Cookbook, 2nd Edition*, earlier this
week, I've been investigating the `use autouse` pragma, to see if it will
indeed solve my issue of wanting to use different modules based on the current
situation. Unfortunately, I cannot find any documentation on it in `perldoc`.

I remember seeing something about wrapping this stuff into a `BEGIN` block, but
that would require knowing certain information immediately, and I might need
the code to work through some steps before getting there.

Fortunately, [this node](http://www.perlmonks.org/index.pl?node_id=323606) just
appeared on Perl Monks today, and I got to see other ways of doing it:

- The `if` module lets you do something like `use if $type eq 'x', "Some::Module";`
  However, `$type` must be known at compile time (i.e., it's based on system
  info or on `@ARGV`); this probably wouldn't work in a web-based application.
- Use `require` and `import` instead: 
  `if $type wq 'ex') { require Some::Module; Some::Module->import if Some::Module->can("import"); }`
  If your module doesn't export anything, you can even omit the call to
  `import`.
- Use an `eval`: `if ($type eq 'x') { eval "use Some::Module"; }` This gets
  around the `import` problem, but could possibly run into other compile time
  issues.

So, basically, I already had the tools to do the job; just needed to examine
the problem more.
