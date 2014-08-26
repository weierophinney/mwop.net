<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

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
$entry->setUpdated(new \DateTime('2014-09-16 08:30', new \DateTimezone('America/Chicago')));
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
    globally. I typically use global rules, as I most often define server 
    aliases; application-specific rules are based on the primary server name
    only, which makes it impossible to cache per-hostname.
</p>

<p>
    I define my rules first by setting up my rules using regular expressions. 
    For instance, for my current site, I have this for the host:
</p>

<div class="example"><pre><code class="language-markup">
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
    <kbd>page_cache_remove_cached_contents()</kbd> function defined by the page 
    cache extension in Zend Server.
</p>

<p>
    This function accepts one argument. The documentation says it's a URL, but in actuality
    you need to provide the pattern from the rule you want to match; it will then
    clear caches for any pages that match that rule. That means you have to provide the
    full match -- which will include the scheme, host, <em>port</em>, and path. Note the
    port -- that absolutely <em>must</em> be present for the match to work, even if it's
    the default port for the given scheme.
</p>

<p>
    What that means is that in my example above, the argument to 
    <kbd>page_cache_remove_cached_contents()</kbd> becomes <kbd>http://(www\.)?mwop\.net:80/resume</kbd>.
    If I allow both HTTP and HTTPS access, then I also will need to explicitly clear
    <kbd>https://(www\.)?mwop\.net:443/resume</kbd>. Note that the regexp escape
    characters are present, as are any conditional patterns.
</p>

<p>
    My current cache clearing script looks like this:
</p>

<div class="example"><pre><code class="language-php">
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$paths = [
    '/',
    '/resume',
];

foreach ($paths as $path) {
    page_cache_remove_cached_contents(
        'http://(www\.)?mwop\.net:80' . $path
    );
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

<div class="example"><pre><code class="language-php">
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

<h3>Note on cache clearing</h3>

<p>
    The Zend Server PHP API offers another function that would appear to be more
    relevant and specific: <kbd>page_cache_remove_cached_contents_by_uri()</kbd>. This
    particular function accepts a rule name, and the URI you wish to clear, and, as
    documented, seems like a nice way to clear the cache for a specific URI as a subset
    of a rule, without clearing caches for all pages matching the rule. However,
    as of version 7.0, this functionality does not work properly (in fact, I was unable
    to find any combination of rule and url that resulted in a cache clear). I recommend using
    <kbd>page_cache_remove_cached_contents()</kbd> only for now, or using full page
    caching within your framework.
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
    <li><a href="/blog/2014-09-16-zend-server-deployment-part-7.html">Tip 7: zs-client</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
