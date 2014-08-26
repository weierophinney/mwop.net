<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-02-zend-server-deployment-part-3');
$entry->setTitle('Deployment with Zend Server (Part 3 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-02 08:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-09-09 08:30', new \DateTimezone('America/Chicago')));
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
    This is the third in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-08-28-zend-server-deployment-part-2.html">The previous post in the series</a>
    detailed creating recurring jobs via Zend Job Queue, à la cronjobs.
</p>

<p>
    Today, I'm sharing a very short deployment script tip learned by experience.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 3: chmod</h2>

<p>
    In the <a href="/blog/2014-08-11-zend-server-deployment-part-1.html">first tip</a>,
    I detailed writing deployment scripts. One of the snippets I shared was a <kbd>chmod</kbd>
    routine:
</p>

<div class="example"><pre><code class="language-php">
$command = 'chmod -R a+rwX ./data';
echo "\nExecuting `$command`\n";
system($command);
</code></pre></div>

<p>
    The code is fine; what I did not share is <em>where</em> in the deployment script
    you should invoke it. As I discovered from experience, this is key.
</p>

<p>
    Zend Server's deployment scripts run as the <kbd>zend</kbd> user. If they are 
    writing any data to the <kbd>data</kbd> directory, that data is owned by the <kbd>zend</kbd> 
    user and group -- and often will not be writable by the web server user.  If you 
    have scheduled jobs that need to write to the same files, they will fail... 
    unless you have done the <kbd>chmod</kbd> after your deployment tasks are done.
</p>

<p>
    So, that's today's tip: if you need any directory in your application to be
    writable by scheduled jobs, which will run as the web server user, make sure
    you do your <kbd>chmod</kbd> as the last step of your deployment script.
</p>

<h2>Next time...</h2>

<p>
    The next tip in the series is another short one, and will detail how to
    secure your Job Queue job scripts.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
<<<<<<< HEAD
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
=======
>>>>>>> Updated previous tips to link to this one
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
