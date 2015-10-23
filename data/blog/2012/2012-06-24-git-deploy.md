---
id: 2012-06-24-git-deploy
author: matthew
title: 'Automatic deployment with git and gitolite'
draft: false
public: true
created: '2012-06-24T21:50:00-05:00'
updated: '2012-06-24T21:50:00-05:00'
tags:
    - perl
    - git
---
I read a [post recently by Sean Coates about deploy on push](http://seancoates.com/blogs/deploy-on-push-from-github).
The concept is nothing new: you set up a hook that listens for commits on
specific branches or tags, and it then deploys your site from that revision.

Except I'd not done it myself. This is how I got there.

<!--- EXTENDED -->

Sean's approach uses [Github webhooks](https://help.github.com/articles/post-receive-hooks),
which are a fantastic concept. Basically, once your commit completes, Github
will send a JSON-encoded payload to a specific URI. Sean uses this to trigger
an API call to a specific page in his website, which will then trigger a
deployment activity.

Awesome, this should be easy; I already have a deploy script written that I
trigger manually.

One small problem: my site, while in Git, is not on Github. I maintain it on my
own [Gitolite](https://github.com/sitaramc/gitolite) repository. Which means I
needed to write my own hooks.

I originally went down the route of using a post-receive hook. However, I had
problems determining what branch the given commit was on, despite a variety of
advice I found on the subject on [StackOverflow](http://stackoverflow.com/) and
git mailing lists. I ended up finding a great example using `post-update`, which
was actually perfect for my needs.

In order to keep the `post-update` script non-blocking when I commit, I made it
do very little: It simply determines what branch the commit was on, and if it
was the master branch, it touches a specific file on the filesystem and
finishes. The entire hook looks like this:

```bash
#!/bin/bash
branch=$(git rev-parse --symbolic --abbrev-ref $1)
echo "Commit was for branch $branch"
if [[ "$branch" == "master" ]];then
    echo "Preparing to deploy"
    echo "1" > /var/local/mwop.net.update
fi
```

Now I needed something to detect such a push, and act on it.

I considered using cron for this; it'd be relatively easy to have it fire up
once a minute, and simply act on it. But I decided instead to write a simple
little daemon using perl. Perl daemons are trivially easy to write, and if you
use module such as `Proc::Daemon` and follow a few trivial defensive coding
practices, you can keep memory leaks contained (or at least minimal). Besides,
it gave me a chance to dust off my perl chops.

I decided I'd have it check for the file in 30 second intervals, simply
sleeping if no changes were detected. If the file was found, however, it should
attempt to deploy. Additionally, I wanted it to quit if it was unable to remove
the file (as this could lead to multiple deploy attempts), and log success and
failure status of the deploy. The full script looks like this:

```perl
#!/usr/bin/perl
use strict;
use warnings;
use Proc::Daemon;

Proc::Daemon::Init;

my $continue = 1;
$SIG{TERM} = sub { $continue = 0 };

my $updateFile   = "/var/local/mwop.net.update";
my $updateScript = "/home/matthew/bin/deploy-mwop";
my $logFile      = "/var/local/mwop.net-deploy.log";
while ($continue) {
    # 30s intervals between iterations
    sleep 30;

    # Check for update file, and restart loop if not found
    unless (-e $updateFile) {
        next;
    }

    # Remove update file
    if (!unlink($updateFile)) {
        # If unable to unlink, we need to quit
        system('echo "' . time() . ': Failed to REMOVE ' . $updateFile . '" >> ' . $logFile);
        $continue = 0;
        next;
    }

    # Deploy
    system($updateScript);
    if ( $? == -1 ) {
        system('echo "' . time() . ': FAILED to deploy: ' . $! . '" >> ' .  $logFile);
    } else {
        system('echo "' . time() . ': Successfully DEPLOYED" >> ' . $logFile);
    }
}
```

The `system()` calls for logging could have been done using Perl, but I didn't
want to deal with additional error handling and file pointers; simply proxying
to the system seemed reasonable and expedient.

When all was ready, I started the above listener, which automatically
daemonizes itself. I then installed the `post-update` hook into my bare
repository, and tested it out. And it runs! When I push to master, my site is
automatically deployed, typically within 15-20 seconds from completion.

#### Caveats

This solution, of course, relies on a daemonized process. If that process were
to terminate, I'd have no idea until I discovered my site didn't refresh after
the most recent push. Clearly, some sort of monitor checking for the status of
the daemon should be in place.

Also, note that I'm having this update on changes to the master branch; you may
need to adapt it for your own needs, depending on your branching strategy.

Finally, this approach does not address issues that might require a roll-back.
Ideally, the script should probably log what revision was current prior to the
deployment, allowing roll-back to the previous state. Alternately, the
deployment script should create a new clone of the site and swap symlinks to
allow quick roll-back when required.
