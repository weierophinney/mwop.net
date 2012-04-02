<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('11-Making-RCS-a-little-easier...');
$entry->setTitle('Making RCS a little easier...');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1074741626);
$entry->setUpdated(1095131443);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'personal',
));

$body =<<<'EOT'
<p>
    One thing I noticed today when using RCS is that it isn't terribly user
    friendly -- you need to checkout a file to make edits. Often, I make edits,
    and <em>then</em> want to commit my changes.
</p>
<p>
    So I wrote a wrapper script called <b>revise</b>. It makes a temporary copy
    of the file you've been editing, checks it out of RCS with locking, makes it
    writeable, moves the temporary copy to the permanent name, checks it in and
    unlocks it (which prompts for a log message), and then makes the file
    writeable for the user and group again. The script is outlined here:
</p>
<pre>
    #!/bin/bash
    FILE=$1
    cp $FILE $FILE.new
    co -l $FILE
    chmod u+w $FILE
    mv $FILE.new $FILE
    ci -u $FILE
    chmod ug+w $FILE
</pre>
<p>
    Being the ROX-Filer centric person I am, I also wrote a quick perl script
    called <b>rox-revise</b> that I can then put in my SendTo menu. It parses
    the file's path, changes to that directory, and then calls the <b>revise</b>
    script on the filename, from within a terminal. This script follows:
</p>
<pre>
    #!/usr/bin/perl -w
    use strict;

    use vars qw/$path $file $TERMCMD $REVISE $ZENITY/;

    # Configurable variables
    $TERMCMD = "myTerm";    # What terminal command to use; must be xterm compliant
    $REVISE  = "revise";    # What command to use to revise (i.e. rcs ci) the file
    $ZENITY  = "zenity";    # The zenity or dialog or xdialog command to use

    # Grab the filename from the command line
    $path = shift;
    $file = $path;

    # If no file given, raise a dialog and quit
    if (!$path || ($path eq '')) {
        system(
            $ZENITY, 
            '--title=Error', 
            '--warning', 
            "--text=No path given to $0; rox-revise quit!"
        );
        exit 0;
    }

    # Get the path to the file and switch to that directory
    if ($path =~ m#/#) {
        $path =~ s#^(.*)/.*?$#$1#;
        if ($path !~ m#^/#) { $path = "./$path"; }
        chdir $path or die "$path not found!n";
    } else {
    # Or else assume we're in the current directory
        $path = './';
    }

    # Get the filename
    $file =~ s#^.*/(.*?)$#$1#;

    # Execute the revise statement
    my $failure = system($TERMCMD, '-e', $REVISE, $file);
    if ($failure) {
        # on failure, raise a dialog
        system(
            $ZENITY, 
            '--title=Error', 
            '--warning', 
            "--text=Unable to revise $file"
        );
    }

    1;
</pre>
<p>
    Now I just need to check out <a href="http://subversion.tigris.org">Subversion</a>, and I can have some
    robust versioning!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;