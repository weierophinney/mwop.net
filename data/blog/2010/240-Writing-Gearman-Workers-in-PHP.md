---
id: 240-Writing-Gearman-Workers-in-PHP
author: matthew
title: 'Writing Gearman Workers in PHP'
draft: false
public: true
created: '2010-05-06T09:00:00-04:00'
updated: '2010-05-13T02:30:39-04:00'
tags:
    0: php
    2: 'zend framework'
---
I've been hearing about and reading about [Gearman](http://gearman.org/) for a
couple years now, but, due to the nature of [my work](http://framework.zend.com/),
it's never really been something I needed to investigate; when you're writing
backend code, scalability is something you leave to the end-users, right?

Wrong! But perhaps an explanation is in order.

<!--- EXTENDED -->

Background
----------

We're looking at migrating to [Git](http://git-scm.com/) for our primary development on the framework. There are a lot of use cases we need to accommodate:

- We want to support atomic changesets (i.e., changesets that include all
  changes related to a single issue: usually, unit tests, code, and often
  documentation).
- At the same time, developers want the ability to pull in just the library code
  as a git [submodule](http://limestone.uoregon.edu/ftp/pub/software/scm/git-core/docs/git-submodule.html),
  or a single language of the manual, etc.
- Users want a read-only [Subversion](http://subversion.org/) respository so
  that they can continue using [svn:externals](http://svnbook.red-bean.com/en/1.0/ch07s03.html).
  Just because we're migrating doesn't mean our users should need to.
- Of course, lots of folks like to keep on top of commits via [RSS feeds](http://en.wikipedia.org/wiki/RSS)
- And then there's masochists like myself who like having commit emails. (Is it a wonder I never hit inbox zero?)

The first two items are hard to accomplish at the same time, as it turns out. If
you make every distinct sub-tree you want discretely cloneable, and then build a
repository consisting of a bunch of git submodules, you lose atomicity. (You end
up having a commit for each submodule you touch, plus one for the master
repository. Eww.)

I found a way to do it, however, using [subtree merges](http://progit.org/book/ch6-7.html).
However, since this post is about writing Gearman workers, I'll leave that for
another day. The important thing, however, is that I discovered something else
that was interesting.

Git allows you to define "hooks", scripts that are run at various points of the
git commit lifecycle. One hook that can run on the server is called
"post-receive". What I discovered is that even though `post-receive` runs after
a commit is accepted to the repository, if you perform operations on a git
repository while the hook is still running, you can get some strange behavior.
In my example, I was having the script trigger a `git pull` in a working tree.
While the working tree received the changesets, it couldn't apply them cleanly,
since the server actually hadn't finalized its state. The only way I could get a
clean pull was if I pulled *after* the hook was complete. Which foiled my
attempts at automation.

And now we get to Gearman. I realized I could have my `post-receive` script
queue a background task to Gearman. Since this is almost an instantaneous
operation, it meant that my hook was completed before Gearman started a worker;
if it wasn't, I could always do a `sleep()` within my worker to ensure it was.

Writing Gearman Tasks
---------------------

So, now I was able to do my task, I started thinking about what other things I
could do, and suddenly Gearman looked like an excellent solution for the
architecture. Basically, it prevents the end-user who is committing changes from
having any lag based on the hook scripts, while simultaneously allowing me do
perform the task automation we need.

So I wrote two tasks as a proof-of-concept, using a mixture of straight PHP and
Zend Framework; these are for the subtree merge I mentioned earlier (the actual
work is done in a bash script, actually), and also one for RSS feeds.

### The Gearman client: a post-receive hook

First, let's look at my hook script, which uses a Gearman client. I'm using
[ext/gearman](http://pecl.php.net//package/gearman), from
[PECL](http://pecl.php.net/). My `post-receive` hook script looks like this:

```php
#!/usr/bin/env php
<?php
$workload = implode(':', $argv);
$client = new GearmanClient();
$client->addServer();
$client->doBackground('post-receive', $workload);
$client->doBackground('rss', $workload);
```

The above should be pretty straight-forward: I create a `GearmanClient`, tell it
to use the default server (localhost), and trigger two Gearman functions,
`post-receive` and `rss`, using the arguments my script received as a payload. I
use the `doBackground()` method so that the tasks can execute asynchronously;
the hook script doesn't need to be blocked on the execution of any given task,
and can continue merrily on its way.

### The tasks

I wrote two classes, one for each Gearman job I wanted to create. I could have
done these as [lambdas](http://php.net/functions.anonymous), plain old
functions, etc. I chose objects so that I could test them, as well as consume
them from other scripts if I want. These classes implement a `Command`
interface, which simply defines an `execute()` method that accepts a
`GearmanJob` instance.

The first is the job that triggers my subtree merge:

```php
<?php

namespace ZF\Git;

class MergeSubtree implements Command
{
    protected $_logger;
    protected $_wd = '/var/spool/gearman';

    public function setWorkingDir($path)
    {
        if (!is_dir($path)) {
            throw new \Exception('Invalid path provided for working directory');
        }
        $this->_wd = $path;
    }

    public function getLogger()
    {
        if (null === $this->_logger) {
            $this->setLogger(new \Zend_Log(new \Zend_Log_Writer_Stream($this->_wd . '/merge_subtree_error.log')));
        }
        return $this->_logger;
    }

    public function setLogger(\Zend_Log $logger)
    {
        $this->_logger = $logger;
    }

    public function executeMerge()
    {
        chdir($_ENV['HOME'] . '/working/zf-master');
        $return = shell_exec($this->_wd . '/update-master.sh');
        return $return;
    }

    public function execute(\GearmanJob $job)
    {
        $this->getLogger()->info('Received merge request');
        $return = $this->executeMerge();
        if (strstr($return, 'Failed')) {
            $this->getLogger()->err('Failed pull: ' . $return);
            $job->sendFail();
            return;
        }
        $this->getLogger()->info('Merge complete');
    }
}
```

*(Note the backslashes in front of the ZF class names; since I'm using namespaces, I need to fully-qualify my classes.)*

The above class is probably overkill. But it has some nice features,
particularly for a Gearman environment: it logs anytime it sees failures in my
merge script. This way I can go look through my logs anytime I start seeing
discrepancies between my repositories.

My next class is a bit more complex, and yet for many, probably more useful. It
takes the most recent 15 `git log` entries, and creates an RSS feed:

```php
<?php
namespace ZF\Git;

class Log2RSS implements Command
{
    protected $_repo;
    protected $_feedDir  = '/var/spool/gearman/feeds';
    protected $_feedName = 'rss';
    protected $_baseLink = 'http://some.viewgit.repo/?a=commit&p=zf&h=';

    public function setRepo($repo)
    {
        if (!is_dir($repo) || !is_dir($repo . '/.git')) {
            throw new \Exception('Invalid repository specified; not a Git repository');
        }
        $this->_repo = $repo;
        return $this;
    }

    public function getRepo()
    {
        if (null === $this->_repo) {
            throw new \Exception('No repository directory specified');
        }
        return $this->_repo;
    }

    public function setBaseLink($url)
    {
        $this->_baseLink = $url;
        return $this;
    }

    public function getBaseLink()
    {
        return $this->_baseLink;
    }

    public function setFeedDir($path)
    {
        if (!is_dir($path) || !is_writable($path)) {
            throw new \Exception('Invalid feed directory specified, or not writeable');
        }
        $this->_feedDir = $path;
        return $this;
    }

    public function getFeedDir()
    {
        return $this->_feedDir;
    }

    public function setFeedName($feedName)
    {
        $this->_feedName = (string) $feedName;
        return $this;
    }

    public function getFeedName()
    {
        return $this->_feedName;
    }

    public function generateFeed()
    {
        $feed = new \Zend_Feed_Writer_Feed;
        $feed->setTitle('git commits');
        $feed->setLink('http://some.viewgit.repo/');
        $feed->setFeedLink('http://some.viewgit.repo/feeds/' . $this->getFeedName() . '.xml', 'rss');
        $feed->addAuthor(array(
            'name'  => 'Name of this feed',
            'email' => 'git@somedomain',
            'uri'   => 'http://some.viewgit.repo/',
        ));
        $feed->setDateModified(time());
        $feed->setDescription('git commits');

        $logs = $this->_parseLogs();

        foreach ($logs as $log) {
            $date  = strtotime($log['date']);
            $entry = $feed->createEntry();
            $entry->setTitle($log['commit']);
            $entry->setLink($this->getBaseLink() . $log['commit']);
            $entry->setDateModified($date);
            $entry->setDateCreated($date);
            $entry->setDescription($log['subject']);
            $entry->setContent($log['subject'] . "\n\n" . $log['notes']);
            $feed->addEntry($entry);
        }

        $output = $feed->export('rss');
        file_put_contents($this->getFeedDir() . '/' . $this->getFeedName() . '.xml', $output);
    }

    public function execute(\GearmanJob $job)
    {
        $this->generateFeed();
    }

    protected function _parseLogs()
    {
        $repoPath = $this->getRepo();

        $command = 'git --git-dir=' . $repoPath . '/.git --work-tree=' . $repoPath . ' log --max-count=15 --format=\'Commit: %H%nAuthor: %an%nDate: %cD%nSubject: %s%nNotes: %N%n\' -p';
        $log     = shell_exec($command);
        $lines   = preg_split("/[\n?|\r]/", $log);
        $logs    = array();
        $index   = 0;
        $current = false;
        foreach ($lines as $line) {
            if (preg_match('/^(Commit|Author|Date|Subject|Notes): (.*)$/', $line, $matches)) {
                $current = strtolower($matches[1]);
                $value   = $matches[2];
                if ('commit' == $current) {
                    $index++;
                    $logs[$index] = array();
                }
                $logs[$index][$current] = $value;
            } elseif (false !== $current) {
                $logs[$index][$current] .= "\n" . $line;
            }
        }
        return $logs;
    }
}
```

The above object could use a few more customization vectors — ways to inject the
RSS feed name, URL, etc., and some threshold for the description limit so it can
truncate past a certain number of lines. However, it gets the job done — it
creates an RSS feed with entries based on each commit.

### The Gearman Worker

Now, for the worker. Since I'm using some Zend Framework classes, and relying on
autoloading, I need to setup some autoloading. I also need to instantiate these
classes, configure the instances, and attach them to the Gearman worker.

```php
#!/usr/bin/env php
<?php
ini_set('memory_limit', -1);

$autoloader = function($class) {
    $file = str_replace(array('\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    return include_once $file;
};
spl_autoload_register($autoloader);

$mergeSubtree = new ZF\Git\MergeSubtree();
$mergeSubtree->setWorkingDir(__DIR__);

$log2rss      = new ZF\Git\Log2RSS();
$log2rss->setRepo('/home/gitolite/working/zf-master')
        ->setFeedName('zf');

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction('post-receive', array($mergeSubtree, 'execute'));
$worker->addFunction('rss', array($log2rss, 'execute'));
while ($worker->work()) {
    if (GEARMAN_SUCCESS != $worker->returnCode()) {
        echo "Worker failed: " . $worker->error() . "\n";
    }
}
```

To tie it all together, I'm using [supervisord](http://supervisord.org/) to
manage this script, so that I don't have to worry about it dying on me; it will
always be available when Gearman needs it. I won't go into the setup here; it's
incredibly straight-forward. *(Many thanks to Sean Coates for
[his 2009 PHP Advent article](http://phpadvent.org/2009/daemonize-your-php-by-sean-coates)
on using supervisord with PHP, and to [Mike Naberezny](http://mikenaberezny.com/)
for introducing me to supervisord many years ago.)*

Conclusions
-----------

Gearman is a great tool for parallelizing tasks, as well as creating
asynchronous processes. Coupled with supervisord and the scripting language of
your choice, you can achieve some incredible results with very little effort.

This is also a nice example of cherry-picking ZF components for use in simple
tasks — I'm using `Zend_Log` to do reporting on the status of jobs, and
`Zend_Feed_Writer` to generate the RSS feed. These are two components that work
very well standalone, and which are also ideally suited for long-running
environments, where you don't need to worry about how long the task takes.

I highly encourage you to investigate using tools for asynchronous processing —
there are a variety of messaging systems, queues, and more that you can
leverage, and which can help you offload resource intensive tasks from your main
application.

*For those of you curious about the subtree merge workflow I'm developing, I'll be writing additional posts this month on that subject.*
