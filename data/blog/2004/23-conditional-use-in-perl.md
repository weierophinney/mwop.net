---
id: 23-conditional-use-in-perl
author: matthew
title: 'conditional use in perl'
draft: false
public: true
created: '2004-02-01T16:03:37-05:00'
updated: '2004-09-20T13:34:37-04:00'
tags:
    - perl
    - personal
---
I've been struggling with how to use modules at runtime instead of compile time (I even wrote about this once before). I finally figured it out:

```perl
my $module = "ROX::Filer";
eval "use $module";
die "couldn't load module : $!n" if ($@);
```

Now I just need to figure out how to create objects from dynamic module names…!

**Update:** Creating objects from dynamic names is as easy as dynamically loading the module at run-time:

```perl
my $obj = $module->new();
```
