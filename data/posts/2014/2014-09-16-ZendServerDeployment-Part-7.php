<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-16-zend-server-deployment-part-7');
$entry->setTitle('Deployment with Zend Server (Part 7 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-16 08:30', new \DateTimezone('America/Chicago')));
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
    This is the seventh in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-09-11-zend-server-deployment-part-6.html">The previous post in the series</a>
    detailed setting up and clearing page caching.
</p>

<p>
    Today, I'm sharing how to use the <a href="https://github.com/zend-patterns/ZendServerSDK">
    Zend Server SDK</a> to deploy your Zend Server deployment packages (ZPKs) 
    from the command line.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 7: zs-client</h2>

<p>
    Zend Server has an API, which allows you to interact with many admin tasks 
    without needing access to the UI, and in an automated fashion. The API is 
    extensive, and has a very complex argument signing process, which makes it 
    difficult to consume. However, this is largely solved via
    <a href="https://github.com/zend-patterns/ZendServerSDK">zs-client, the Zend Server SDK</a>.
</p>

<p>
    The first thing you need to do after downloading it is to create an 
    application target. This simplifies usage of the client for subsequent 
    requests, allowing you to specify <kbd>--target={target name}</kbd> instead of 
    having to provide the Zend Server URL, API username, and API token for each 
    call.
</p>

<p>
    This is done using the <kbd>addTarget</kbd> command:
</p>

<div class="example"><pre><code class="language-bash">
$ zs-client.phar addTarget \
> --target={unique target name} \
> --zsurl={URL to your Zend Server instance} \
> --zskey={API username} \
> --zssecret={API token} \
> --http="sslverifypeer=0"
</code></pre></div>

<p>
    The <kbd>zsurl</kbd> is the scheme, host, and port only; don't include the 
    path. You can find keys and tokens on the "Administration &gt; Web API" 
    page of your Zend Server UI, and can even generate new ones there.
</p>

<div><img src="http://uploads.mwop.net/2014-09-16-WebApiKeys.png"></div>

<p>
    Note the last line; Zend Server uses self-signed SSL certificates, which 
    can raise issues with cURL in particular -- which the SDK uses under the 
    hood. Passing <kbd>--http="sslverifypeer=0"</kbd> fixes that situation.
</p>

<p>
    Once you've created your target, you need to determine your application 
    identifier. Use the <kbd>applicationGetStatus</kbd> command to find it:
</p>

<div class="example"><pre><code class="language-bash">
$ zs-client.phar applicationGetStatus --target={unique target name}
</code></pre></div>

<p>
    Look through the list of deployed applications, and find the <kbd><id></kbd> of the application.
</p>

<p>
    From here, you can now deploy packages using the <kbd>applicationUpdate</kbd> command:
</p>

<div class="example"><pre><code class="language-bash">
$ zs-client.phar applicationUpdate \
> --appId={id} \
> --appPackage={your ZPK} \
> --target={unique target name}
</code></pre></div>

<p>
    In sum: the Zend Server SDK gives us the tools to automate our deployment.
</p>

<h2>Next time...</h2>

<p>
    The next tip in the series details automating deployments using zf-deploy and zs-client.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
    <li><a href="/blog/2014-09-11-zend-server-deployment-part-6.html">Tip 6: Page caching</a></li>
</ul

<p>
    I will update this post to link to each article as it releases.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
