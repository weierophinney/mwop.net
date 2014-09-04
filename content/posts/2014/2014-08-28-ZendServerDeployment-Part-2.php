<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-08-28-zend-server-deployment-part-2');
$entry->setTitle('Deployment with Zend Server (Part 2 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-08-28 08:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-09-04 08:30', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'apigility',
  'php',
  'programming',
  'zend-framework',
  'zend-server',
));

$body =<<<'EOT'
<p>
    This is the second in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-08-11-zend-server-deployment-part-1.html">The previous post in the series</a>
    detailed getting started with <a href="http://www.zend.com/en/solutions/cloud-solutions/aws-marketplace">Zend
    Server on the AWS marketplace</a> and using <a href="https://github.com/zfcampus/zf-deploy">zf-deploy</a>
    to create ZPK packages to deploy to Zend Server.
</p>

<p>
    Today, I'm looking at how to created scheduled/recurring jobs using Zend Server's Job Queue;
    think of this as application-level cronjobs.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 2: Recurring Jobs</h2>

<p>
    I needed to define a few recurring jobs on the server. In the past, I've 
    used <kbd>cron</kbd> for this, but I've recently had a slight change of mind on 
    this: if I use <kbd>cron</kbd>, I have to assume I'm running on a unix-like system, 
    and have some sort of system access to the server. If I have multiple 
    servers running, that means ensuring they're setup on each server. It seems 
    better to be able to define these jobs at the applicaton level.
</p>

<p>
    Since Zend Server comes with Job Queue, I decided to try it out for 
    scheduling recurring jobs. This is not terribly intuitive, however. The UI 
    allows you to define scheduled jobs... but only gives options for every 
    minute, hour, day, week, and month, without allowing you to specify the 
    exact interval (e.g., every day at 20:00).

<p>
    The PHP API, however, makes this easy. I can create a job as follows:
</p>

<div class="example"><pre><code language="php">
$queue = new ZendJobQueue();
$queue->createHttpJob('/jobs/github-feed.php', [], [
  'name'       => 'github-feed',
  'persistent' => false,
  'schedule'   => '5,20,35,40 * * * *',
]);
</code></pre></div>

<p>
    Essentially, you provide a URL to the script to execute (Job Queue "runs" a 
    job by accessing a URL on the server), and provide a schedule in crontab 
    format. I like to give my jobs names as well, as it allows me to search for 
    them in the UI, and also enables linking between the rules and the logs in 
    the UI. Marking them as <em>not</em> persistent ensures that if the job is 
    successful, it will be removed from the events list.
</p>

<p>
    The question is, where do you define this? I decided to do this in my 
    <kbd>post_activate.php</kbd> deployment script. However, this raises two new 
    problems:
</p>

<ul>
    <li>Rules need not just a path to the script, but also the scheme and host. 
        You _can_ omit those, but only if the script can resolve them via 
        <kbd>$_SERVER</kbd>... which it cannot due during deployment.</li>
    <li>Each deployment adds the jobs you define... but this does not overwrite 
        or remove the jobs you added in previous deployments.</li>
</ul>

<p>
    I solved these as follows:
</p>

<div class="example"><pre><code language="php">
$server = 'http://mwop.net';

// Remove previously scheduled jobs:
$queue = new ZendJobQueue();
foreach ($queue->getSchedulingRules() as $job) {
    if (0 !== strpos($job['script'], $server)) {
        // not one we're interested in
        continue;
    }

    // Remove previously scheduled job
    $queue->deleteSchedulingRule($job['id']);
}

$queue->createHttpJob($server . '/jobs/github-feed.php', [], [
  'name'       => 'github-feed',
  'persistent' => false,
  'schedule'   => '5,20,35,40 * * * *',
]);
</code></pre></div>

<p>
    So, in summary:
</p>

<ul>
    <li>Define your rules with names.</li>
    <li>Define recurring rules using the <kbd>schedule</kbd> option.</li>
    <li>Define recurring rules in your deployment script, during <kbd>post_activate</kbd>.</li>
    <li>Remove previously defined rules in your deployment script, prior to defining them.</li>
</ul>

<h2>Next time...</h2>

<p>
    The next tip in the series is a short one, perfect for following the US 
    Labor Day weekend, and details something I learned the hard way from Tip 1 
    when setting up deployment tasks.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
