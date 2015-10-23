---
id: 22-Wheres-that-module
author: matthew
title: 'Where''s that module?'
draft: false
public: true
created: '2004-01-27T23:15:30-05:00'
updated: '2004-09-20T13:33:28-04:00'
tags:
    - perl
    - personal
---
One continual pain for me with perl is when I need to try to find the location
of a specific module on my filesystem so that I can examine it myself; I end up
first having to find out what my `@INC` path is, then having to dig through it
until I find the module. Fortunately, I'm not the only one; somebody
[posted a solution to this problem](http://www.perlmonks.org/index.pl?node_id=274701) on
[Perl Monks](http://www.perlmonks.org):

**Updated:** The original listing presented didn't work! The following one,
garnered from a comment to the original PM post, *does*, and is what I'm now
using.

```perl
#!/usr/bin/perl -w
use strict;

use File::Spec::Functions qw/catfile/;

my @loaded = grep {
    eval "require $_";
    !$@ ? 1 : ($@ =~ s/(@INC contains: Q@INCE)//, warn ("Failed loading $_: $@"), 0);
} @ARGV;

my @pm = map catfile(split '::') . (/.pmz/ ? '' : '.pm'), @loaded;

print "@INC{@pm}n";
__END__

=pod

=head1 NAME

whichpm - lists real paths of specified modules

=head1 SYNOPSIS

  editor `whichpm Bar`

=head1 DESCRIPTION

Analogous to the UN*X command which.

=cut
```

Just place it in your `$PATH` and let 'er rip!
