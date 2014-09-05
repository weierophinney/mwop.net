<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('22-Wheres-that-module');
$entry->setTitle('Where\'s that module?');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1075263330);
$entry->setUpdated(1095701608);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'perl',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    One continual pain for me with perl is when I need to try to find the
    location of a specific module on my filesystem so that I can examine it
    myself; I end up first having to find out what my @INC path is, then having
    to dig through it until I find the module. Fortunately, I'm not the only
    one; somebody <a href="http://www.perlmonks.org/index.pl?node_id=274701">posted a
    solution to this problem</a> on <a href="http://www.perlmonks.org">Perl
    Monks</a>:
</p>
<p>
    <b>Updated: </b> The original listing presented didn't work! The following
    one, garnered from a comment to the original PM post, <em>does</em>, and is
    what I'm now using.
</p>
<pre>#!/usr/bin/perl -w
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

</pre>
<p>
    Just place it in your $PATH and let 'er rip!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;