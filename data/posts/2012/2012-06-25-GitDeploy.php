<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-06-24-git-deploy');
$entry->setTitle('Automatic deployment with git and gitolite');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-06-24 21:50', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-06-24 21:50', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'perl',
  'git',
));

$body =<<<'EOT'
<p>
    I read a <a 
    href="http://seancoates.com/blogs/deploy-on-push-from-github" 
    target="_blank">post recently by Sean Coates about deploy on 
    push</a>. The concept is nothing new: you set up a hook that 
    listens for commits on specific branches or tags, and it then
    deploys your site from that revision.
</p>

<p>
    Except I'd not done it myself. This is how I got there.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    Sean's approach uses <a href="https://help.github.com/articles/post-receive-hooks" target="_blank">Github webhooks</a>,
    which are a fantastic concept. Basically, once your commit completes, Github
    will send a JSON-encoded payload to a specific URI. Sean uses this to 
    trigger an API call to a specific page in his website, which will then
    trigger a deployment activity.
</p>

<p>
    Awesome, this should be easy; I already have a deploy script written
    that I trigger manually.
</p>

<p>
    One small problem: my site, while in Git, is not on Github. I maintain
    it on my own <a href="https://github.com/sitaramc/gitolite" target="_blank">Gitolite</a>
    repository. Which means I needed to write my own hooks.
</p>

<p>
    I originally went down the route of using a post-receive hook. However,
    I had problems determining what branch the given commit was on, despite
    a variety of advice I found on the subject on 
    <a href="http://stackoverflow.com/" target="_blank">StackOverflow</a> and git mailing
    lists. I ended up finding a great example using post-update, which
    was actually perfect for my needs.
</p>

<p>
    In order to keep the post-update script non-blocking when I commit, I made 
    it do very little: It simply determines what branch the commit was on, and if
    it was the master branch, it touches a specific file on the filesystem and
    finishes. The entire hook looks like this:
</p>

<div class="example"><pre>
#!/bin/bash
branch=$(git rev-parse --symbolic --abbrev-ref $1)
echo "Commit was for branch $branch"
if [[ "$branch" == "master" ]];then
    echo "Preparing to deploy"
    echo "1" > /var/local/mwop.net.update
fi
</pre></div>

<p>
    Now I needed something to detect such a push, and act on it.
</p>

<p>
    I considered using cron for this; it'd be relatively easy to have it fire
    up once a minute, and simply act on it. But I decided instead to write a 
    simple little daemon using perl. Perl daemons are trivially easy to write,
    and if you use module such as <code>Proc::Daemon</code> and follow a few
    trivial defensive coding practices, you can keep memory leaks contained (or
    at least minimal). Besides, it gave me a chance to dust off my perl chops.
</p>

<p>
    I decided I'd have it check for the file in 30 second intervals, simply
    sleeping if no changes were detected. If the file was found, however, it
    should attempt to deploy. Additionally, I wanted it to quit if it was
    unable to remove the file (as this could lead to multiple deploy attempts),
    and log success and failure status of the deploy. The full script looks like
    this:
</p>

<div class="example"><pre>
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
</pre></div>

<p>
    The <code>system()</code> calls for logging could have been done using 
    Perl, but I didn't want to deal with additional error handling and file
    pointers; simply proxying to the system seemed reasonable and expedient.
</p>

<p>
    When all was ready, I started the above listener, which automatically
    daemonizes itself. I then installed the post-update hook into my bare
    repository, and tested it out. And it runs! When I push to master, my
    site is automatically deployed, typically within 15-20 seconds from
    completion.
</p>

<h4>Caveats</h4>

<p>
    This solution, of course, relies on a daemonized process. If that process
    were to terminate, I'd have no idea until I discovered my site didn't
    refresh after the most recent push. Clearly, some sort of monitor checking
    for the status of the daemon should be in place.
</p>

<p>
    Also, note that I'm having this update on changes to the master branch;
    you may need to adapt it for your own needs, depending on your branching
    strategy.
</p>

<p>
    Finally, this approach does not address issues that might require a 
    roll-back. Ideally, the script should probably log what revision was 
    current prior to the deployment, allowing roll-back to the previous
    state. Alternately, the deployment script should create a new clone
    of the site and swap symlinks to allow quick roll-back when required.
</p>

EOT;
$entry->setExtended($extended);

return $entry;
