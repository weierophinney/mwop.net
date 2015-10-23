---
id: 11-Making-RCS-a-little-easier...
author: matthew
title: 'Making RCS a little easier...'
draft: false
public: true
created: '2004-01-21T22:20:26-05:00'
updated: '2004-09-13T23:10:43-04:00'
tags:
    - linux
    - personal
---
One thing I noticed today when using RCS is that it isn't terribly user friendly — you need to checkout a file to make edits. Often, I make edits, and *then* want to commit my changes.

So I wrote a wrapper script called **revise**. It makes a temporary copy of the file you've been editing, checks it out of RCS with locking, makes it writeable, moves the temporary copy to the permanent name, checks it in and unlocks it (which prompts for a log message), and then makes the file writeable for the user and group again. The script is outlined here:

```bash
#!/bin/bash
FILE=$1
cp $FILE $FILE.new
co -l $FILE
chmod u+w $FILE
mv $FILE.new $FILE
ci -u $FILE
chmod ug+w $FILE
```

Being the ROX-Filer centric person I am, I also wrote a quick perl script called **rox-revise** that I can then put in my `SendTo` menu. It parses the file's path, changes to that directory, and then calls the **revise** script on the filename, from within a terminal. This script follows:

```perl
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
```

Now I just need to check out [Subversion](http://subversion.tigris.org), and I can have some robust versioning!
