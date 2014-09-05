<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('240-Writing-Gearman-Workers-in-PHP');
$entry->setTitle('Writing Gearman Workers in PHP');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1273150800);
$entry->setUpdated(1273732239);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've been hearing about and reading about <a
        href="http://gearman.org/">Gearman</a> for a couple years now, but, due
    to the nature of <a href="http://framework.zend.com/">my work</a>, it's
    never really been something I needed to investigate; when you're writing
    backend code, scalability is something you leave to the end-users, right?
</p>

<p>
    Wrong! But perhaps an explanation is in order.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Background</h2>

<p>
    We're looking at migrating to <a href="http://git-scm.com/">Git</a> for our
    primary development on the framework. There are a lot of use cases we need
    to accommodate:
</p>

<ul>
	<li>We want to support atomic changesets (i.e., changesets that include all
        changes related to a single issue: usually, unit tests, code, and often
        documentation).</li>
	<li>At the same time, developers want the ability to pull in just the
        library code as a git <a href="http://limestone.uoregon.edu/ftp/pub/software/scm/git-core/docs/git-submodule.html">submodule</a>,
        or a single language of the manual, etc.</li>
    <li>Users want a read-only <a href="http://subversion.org/">Subversion</a>
        respository so that they can continue using <a
            href="http://svnbook.red-bean.com/en/1.0/ch07s03.html">svn:externals</a>.
        Just because we're migrating doesn't mean our users should need to.</li>
    <li>Of course, lots of folks like to keep on top of commits via <a
        href="http://en.wikipedia.org/wiki/RSS">RSS feeds</a></li>
    <li>And then there's masochists like myself who like having commit
        emails. (Is it a wonder I never hit inbox zero?)</li>
</ul>

<p>
    The first two items are hard to accomplish at the same time, as it turns
    out. If you make every distinct sub-tree you want discretely cloneable, and
    then build a repository consisting of a bunch of git submodules, you lose
    atomicity. (You end up having a commit for each submodule you touch, plus
    one for the master repository. Eww.)
</p>

<p>
    I found a way to do it, however, using <a
        href="http://progit.org/book/ch6-7.html">subtree merges</a>. However,
    since this post is about writing Gearman workers, I'll leave that for
    another day. The important thing, however, is that I discovered something
    else that was interesting.
</p>

<p>
    Git allows you to define "hooks", scripts that are run at various points of
    the git commit lifecycle. One hook that can run on the server is called
    "post-receive". What I discovered is that even though
    <code>post-receive</code> runs after a commit is accepted to the repository,
    if you perform operations on a git repository while the hook is still
    running, you can get some strange behavior. In my example, I was having the
    script trigger a "git pull" in a working tree. While the working tree
    received the changesets, it couldn't apply them cleanly, since the server
    actually hadn't finalized its state. The only way I could get a clean pull
    was if I pulled <em>after</em> the hook was complete. Which foiled my
    attempts at automation.
</p>

<p>
    And now we get to Gearman. I realized I could have my
    <code>post-receive</code> script queue a background task to Gearman. Since
    this is almost an instantaneous operation, it meant that my hook was
    completed before Gearman started a worker; if it wasn't, I could always do a
    <code>sleep()</code> within my worker to ensure it was.
</p>

<h2>Writing Gearman Tasks</h2>

<p>
    So, now I was able to do my task, I started thinking about what other things
    I could do, and suddenly Gearman looked like an excellent solution for the
    architecture. Basically, it prevents the end-user who is committing changes
    from having any lag based on the hook scripts, while simultaneously allowing
    me do perform the task automation we need.
</p>

<p>
    So I wrote two tasks as a proof-of-concept, using a mixture of straight PHP
    and Zend Framework; these are for the subtree merge I mentioned earlier (the
    actual work is done in a bash script, actually), and also one for RSS feeds.
</p>

<h3>The Gearman client: a post-receive hook</h3>

<p>
    First, let's look at my hook script, which uses a Gearman client. I'm using
    <a href="http://pecl.php.net//package/gearman">ext/gearman</a>, from <a
        href="http://pecl.php.net/">PECL</a>.  My <code>post-receive</code> hook
    script looks like this:
</p>

<div class="example"><pre><code lang="php">
#!/usr/bin/env php
&lt;?php
$workload = implode(':', $argv);
$client = new GearmanClient();
$client-&gt;addServer();
$client-&gt;doBackground('post-receive', $workload);
$client-&gt;doBackground('rss', $workload);
</code></pre></div>

<p>
    The above should be pretty straight-forward: I create a
    <code>GearmanClient</code>, tell it to use the default server (localhost),
    and trigger two Gearman functions, "post-receive" and "rss," using the
    arguments my script received as a payload. I use the
    <code>doBackground()</code> method so that the tasks can execute
    asynchronously; the hook script doesn't need to be blocked on the execution
    of any given task, and can continue merrily on its way.
</p>

<h3>The tasks</h3>

<p>
    I wrote two classes, one for each Gearman job I wanted to create. I could
    have done these as <a href="http://php.net/functions.anonymous">lambdas</a>,
    plain old functions, etc. I chose objects so that I could test them, as well
    as consume them from other scripts if I want. These classes implement a
    <code>Command</code> interface, which simply defines an
    <code>execute()</code> method that accepts a <code>GearmanJob</code>
    instance.
</p>

<p>
    The first is the job that triggers my subtree merge:
</p>

<div class="example"><pre><code lang="php">
&lt;?php

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
        $this-&gt;_wd = $path;
    }

    public function getLogger()
    {
        if (null === $this-&gt;_logger) {
            $this-&gt;setLogger(new \Zend_Log(new \Zend_Log_Writer_Stream($this-&gt;_wd . '/merge_subtree_error.log')));
        }
        return $this-&gt;_logger;
    }

    public function setLogger(\Zend_Log $logger)
    {
        $this-&gt;_logger = $logger;
    }

    public function executeMerge()
    {
        chdir($_ENV['HOME'] . '/working/zf-master');
        $return = shell_exec($this-&gt;_wd . '/update-master.sh');
        return $return;
    }

    public function execute(\GearmanJob $job)
    {
        $this-&gt;getLogger()-&gt;info('Received merge request');
        $return = $this-&gt;executeMerge();
        if (strstr($return, 'Failed')) {
            $this-&gt;getLogger()-&gt;err('Failed pull: ' . $return);
            $job-&gt;sendFail();
            return;
        }
        $this-&gt;getLogger()-&gt;info('Merge complete');
    }
}
</code></pre></div>

<p>
    <em>(Note the backslashes in front of the ZF class names; since I'm using
        namespaces, I need to fully-qualify my classes.)</em>
</p>

<p>
    The above class is probably overkill. But it has some nice features,
    particularly for a Gearman environment: it logs anytime it sees failures in
    my merge script. This way I can go look through my logs anytime I start
    seeing discrepancies between my repositories.
</p>

<p>
    My next class is a bit more complex, and yet for many, probably more
    useful. It takes the most recent 15 <code>git log</code> entries, and
    creates an RSS feed:
</p>

<div class="example"><pre><code lang="php">
&lt;?php
namespace ZF\Git;

class Log2RSS implements Command
{
    protected $_repo;
    protected $_feedDir  = '/var/spool/gearman/feeds';
    protected $_feedName = 'rss';
    protected $_baseLink = 'http://some.viewgit.repo/?a=commit&amp;p=zf&amp;h=';

    public function setRepo($repo)
    {
        if (!is_dir($repo) || !is_dir($repo . '/.git')) {
            throw new \Exception('Invalid repository specified; not a Git repository');
        }
        $this-&gt;_repo = $repo;
        return $this;
    }

    public function getRepo()
    {
        if (null === $this-&gt;_repo) {
            throw new \Exception('No repository directory specified');
        }
        return $this-&gt;_repo;
    }

    public function setBaseLink($url)
    {
        $this-&gt;_baseLink = $url;
        return $this;
    }

    public function getBaseLink()
    {
        return $this-&gt;_baseLink;
    }

    public function setFeedDir($path)
    {
        if (!is_dir($path) || !is_writable($path)) {
            throw new \Exception('Invalid feed directory specified, or not writeable');
        }
        $this-&gt;_feedDir = $path;
        return $this;
    }

    public function getFeedDir()
    {
        return $this-&gt;_feedDir;
    }

    public function setFeedName($feedName)
    {
        $this-&gt;_feedName = (string) $feedName;
        return $this;
    }

    public function getFeedName()
    {
        return $this-&gt;_feedName;
    }

    public function generateFeed()
    {
        $feed = new \Zend_Feed_Writer_Feed;
        $feed-&gt;setTitle('git commits');
        $feed-&gt;setLink('http://some.viewgit.repo/');
        $feed-&gt;setFeedLink('http://some.viewgit.repo/feeds/' . $this-&gt;getFeedName() . '.xml', 'rss');
        $feed-&gt;addAuthor(array(
            'name'  =&gt; 'Name of this feed',
            'email' =&gt; 'git@somedomain',
            'uri'   =&gt; 'http://some.viewgit.repo/',
        ));
        $feed-&gt;setDateModified(time());
        $feed-&gt;setDescription('git commits');

        $logs = $this-&gt;_parseLogs();

        foreach ($logs as $log) {
            $date  = strtotime($log['date']);
            $entry = $feed-&gt;createEntry();
            $entry-&gt;setTitle($log['commit']);
            $entry-&gt;setLink($this-&gt;getBaseLink() . $log['commit']);
            $entry-&gt;setDateModified($date);
            $entry-&gt;setDateCreated($date);
            $entry-&gt;setDescription($log['subject']);
            $entry-&gt;setContent($log['subject'] . \&quot;\n\n\&quot; . $log['notes']);
            $feed-&gt;addEntry($entry);
        }

        $output = $feed-&gt;export('rss');
        file_put_contents($this-&gt;getFeedDir() . '/' . $this-&gt;getFeedName() . '.xml', $output);
    }

    public function execute(\GearmanJob $job)
    {
        $this-&gt;generateFeed();
    }

    protected function _parseLogs()
    {
        $repoPath = $this-&gt;getRepo();

        $command = 'git --git-dir=' . $repoPath . '/.git --work-tree=' . $repoPath . ' log --max-count=15 --format=\'Commit: %H%nAuthor: %an%nDate: %cD%nSubject: %s%nNotes: %N%n\' -p';
        $log     = shell_exec($command);
        $lines   = preg_split('/[\r\n?|\n]/', $log);
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
                $logs[$index][$current] .= \&quot;\n\&quot; . $line;
            }
        }
        return $logs;
    }
}
</code></pre></div>

<p>
    The above object could use a few more customization vectors -- ways to
    inject the RSS feed name, URL, etc., and some threshold for the description
    limit so it can truncate past a certain number of lines. However, it gets
    the job done -- it creates an RSS feed with entries based on each commit.
</p>

<h3>The Gearman Worker</h3>

<p>
    Now, for the worker. Since I'm using some Zend Framework classes, and
    relying on autoloading, I need to setup some autoloading. I also need to
    instantiate these classes, configure the instances, and attach them to the
    Gearman worker.
</p>

<div class="example"><pre><code lang="php">
#!/usr/bin/env php
&lt;?php
ini_set('memory_limit', -1);

$autoloader = function($class) {
    $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
    return include_once $file;
};
spl_autoload_register($autoloader);

$mergeSubtree = new ZF\Git\MergeSubtree();
$mergeSubtree-&gt;setWorkingDir(__DIR__);

$log2rss      = new ZF\Git\Log2RSS();
$log2rss-&gt;setRepo('/home/gitolite/working/zf-master')
        -&gt;setFeedName('zf');

$worker = new GearmanWorker();
$worker-&gt;addServer();
$worker-&gt;addFunction('post-receive', array($mergeSubtree, 'execute'));
$worker-&gt;addFunction('rss', array($log2rss, 'execute'));
while ($worker-&gt;work()) {
    if (GEARMAN_SUCCESS != $worker-&gt;returnCode()) {
        echo \&quot;Worker failed: \&quot; . $worker-&gt;error() . \&quot;\n\&quot;;
    }
}
</code></pre></div>

<p>
    To tie it all together, I'm using <a
        href="http://supervisord.org/">supervisord</a> to manage this script, so
    that I don't have to worry about it dying on me; it will always be available
    when Gearman needs it. I won't go into the setup here; it's incredibly
    straight-forward. <em>(Many thanks to Sean Coates for <a
        href="http://phpadvent.org/2009/daemonize-your-php-by-sean-coates">his
        2009 PHP Advent article</a> on using supervisord with PHP, and to <a
        href="http://mikenaberezny.com/">Mike Naberezny</a> for introducing
    me to supervisord many years ago.)</em>
</p>

<h2>Conclusions</h2>

<p>
    Gearman is a great tool for parallelizing tasks, as well as creating
    asynchronous processes. Coupled with supervisord and the scripting language
    of your choice, you can achieve some incredible results with very little
    effort. 
</p>

<p>
    This is also a nice example of cherry-picking ZF components for use in
    simple tasks -- I'm using <code>Zend_Log</code> to do reporting on the
    status of jobs, and <code>Zend_Feed_Writer</code> to generate the RSS feed.
    These are two components that work very well standalone, and which are also
    ideally suited for long-running environments, where you don't need to worry
    about how long the task takes.
</p>

<p>
    I highly encourage you to investigate using tools for asynchronous
    processing -- there are a variety of messaging systems, queues, and more
    that you can leverage, and which can help you offload resource intensive
    tasks from your main application.
</p>

<p>
    <em>For those of you curious about the subtree merge workflow I'm
        developing, I'll be writing additional posts this month on that
        subject.</em>
</p>
EOT;
$entry->setExtended($extended);

return $entry;