<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-04-zend-server-deployment-part-4');
$entry->setTitle('Deployment with Zend Server (Part 4 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-04 08:30', new \DateTimezone('America/Chicago')));
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
    This is the fourth in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-09-02-zend-server-deployment-part-3.html">The previous post in the series</a>
    detailed a trick I learned about when to execute a <kbd>chmod</kbd> statement
    during deployment.
</p>

<p>
    Today, I'm sharing a tip about securing your Job Queue job scripts.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 4: Secure your job scripts</h2>

<p>
    In the <a href="/blog/2014-08-28-zend-server-deployment-part-2.html">second tip</a>,
    I detailed <em>when</em> to register job scripts, but not how to write them.
    As it turns out, there's one very important facet to consider when writing 
    job scripts: security.
</p>

<p>
    One issue with Job Queue is that jobs are triggered... via the web. This 
    means that they are exposed via the web, which makes them potential attack 
    vectors. However, there's a simple trick to prevent access other than from Job 
    Queue; add this at the top of your job scripts:
</p>

<div class="example"><pre><code class="language-php">
if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}
</code></pre></div>

<p>
    While the jobs are invoked via HTTP, Zend Server has ways of tracking whether
    or not they are being executed in the context of Job Queue, and for which job.
    If the <code>ZendJobQueue::getCurrentJobId()</code> returns a falsy value,
    then it was not invoked via Job Queue, and you can exit immediately. I like
    to set a 403 status in these situations as well, but that's just a personal
    preference.
</p>

<h2>Next time...</h2>

<p>
    The next tip in the series is builds on this one, and gives some best
    practices to follow when writing your job scripts.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
    <li><a href="/blog/2014-09-11-zend-server-deployment-part-6.html">Tip 6: Page caching</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
