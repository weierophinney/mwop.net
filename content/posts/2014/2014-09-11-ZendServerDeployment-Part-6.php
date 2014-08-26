<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-11-zend-server-deployment-part-6');
$entry->setTitle('Deployment with Zend Server (Part 6 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-11 08:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-09-11 08:30', new \DateTimezone('America/Chicago')));
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
    This is the sixth in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-09-09-zend-server-deployment-part-5.html">The previous post in the series</a>
    detailed setting job script status codes.
</p>

<p>
    Today, I'm sharing some tips around setting up page caching, and jobs
    for clearing the Zend Server page cache.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 6: Page caching</h2>

<p>
    Zend Server offers page caching. This can be defined per-application or 
    globally. I've personally never had luck with per-application caching, but 
    primarily because I often define server aliases; application-specific rules 
    are based on the primary server name.
</p>

<p>
    I define my rules first by setting up my rules using regular expressions. 
    For instance, for my current site, I have this for the host:
</p>

<div class="example"><pre><code language="regexp">
(www\.)?mwop.net
</code></pre></div>

<p>
    This allows me to match with or without the <kbd>www.</kbd> prefix.
</p>

<div><img src="http://uploads.mwop.net/2014-09-11-ZendServer-PageCacheRule.png"></div>

<p>
    After that, I define regular expressions for the paths, and ensure that 
    matches take into account the <kbd>REQUEST_URI</kbd> (failure to do this will cache 
    the same page for any page matching the regex!).
</p>

<div><img src="http://uploads.mwop.net/2014-09-11-ZendServer-PageCacheRule-ByUri.png"></div>

<p>
    When I deploy, or when I run specific jobs, I typically want to clear my 
    cache. To do that, I have a Job Queue job, and in that script, I use the 
    <kbd>page_cache_remove_cached_contents_by_uri()</kbd> function defined by the page 
    cache extension in Zend Server. Because cache hits are per URL, I need to 
    define a list of hosts as well as paths I want to clear.
</p>

<p>
    My current cache clearing script looks like this:
</p>

<div class="example"><pre><code language="php">
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$hosts = [
    'mwop.net',
    'www.mwop.net',
];

$rules = [
    'mwop_home'   => '/',
    'mwop_resume' => '/resume',
];

foreach ($hosts as $host) {
    foreach ($rules as $rule => $path) {
        page_cache_remove_cached_contents_by_uri(
            $rule,
            'http://' . $host . $path
        );
    }
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
</code></pre></div>

<p>
    If I wanted to get more granular, I could alter the script to accept rules 
    and URLs to clear via arguments provided by Job Queue; see the
    <a href="http://files.zend.com/help/Zend-Server/zend-server.htm#zendserverapi/zend_job_queue-php_api.htm#function-createHttpJob">Job Queue</a>
    documentation for information on passing arguments.
</p>

<p>
    I queue this script in my <kbd>post_activate.php</kbd> deployment script, but without a schedule:
</p>

<div class="example"><pre><code language="php">
$queue->createHttpJob($server . '/jobs/clear-cache.php', [], [
    'name' => 'clear-cache',
    'persistent' => false,
]);
</code></pre></div>

<p>
    This will schedule it to run immediately once activation is complete. I will also queue
    it from other jobs if what they do should result in flushing the page cache; I use the
    exact same code when I do so.
</p>

<h2>Next time...</h2>

<p>
    The next tip in the series discusses using the <a 
    href="https://github.com/zend-patterns/ZendServerSDK">Zend Server SDK</a>
    for deploying your application from the command line.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
