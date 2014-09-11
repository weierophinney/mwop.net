<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-09-zend-server-deployment-part-5');
$entry->setTitle('Deployment with Zend Server (Part 5 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-09 08:30', new \DateTimezone('America/Chicago')));
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
    This is the fifth in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-09-04-zend-server-deployment-part-4.html">The previous post in the series</a>
    detailed how to secure your Job Queue job scripts.
</p>

<p>
    Today, I'm sharing some best practices around writing job scripts, particularly
    around how to indicate execution status.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 5: Set your job status</h2>

<p>
    You should always set your job script status, and exit with an appropriate 
    return status. This ensures that Job Queue knows for sure if the job completed 
    successfully, which can help you better identify failed jobs in the UI. I use 
    the following:
</p>

<div class="example"><pre><code class="language-php">
// for failure:
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
exit(1);

// for success:
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
</code></pre></div>

<p>
    I also have started returning relevant messages. Since Job Queue aggregates 
    these in the UI panel, that allows you to examine the output, which often 
    helps in debugging.
</p>

<div class="example"><pre><code class="language-php">
exec($command, $output, $return);
header('Content-Type: text/plain');
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    echo implode("\n", $output);
    exit(1);
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
echo implode("\n", $output);
exit(0);
</code></pre></div>

<p>
    Here's sample output:
</p>

<div><img src="http://uploads.mwop.net/2014-09-09-ZendServer-JobStatus.png"></div>

<p>
    (The <code>[0;34m]</code>-style codes are colorization codes; terminals capable of
    color would display the output in color, but Zend Server, of course, is seeing plain text.)
</p>

<p>
    In sum: return appropriate job status via the 
    <kbd>ZendJobQueue::setCurrentJobStatus()</kbd> static method and the <kbd>exit()</kbd> code, 
    and send output to help diagnose issues later.
</p>

<h2>Next time...</h2>

<p>
    The next tip in the series discusses setting up page caching in Zend Server,
    as well as creating jobs to clear page caches.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
    <li><a href="/blog/2014-09-11-zend-server-deployment-part-6.html">Tip 6: Page caching</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
