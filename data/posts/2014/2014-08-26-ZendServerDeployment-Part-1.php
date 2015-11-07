<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-08-11-zend-server-deployment-part-1');
$entry->setTitle('Deployment with Zend Server (Part 1 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-08-26 15:15', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-09-18 08:30', new \DateTimezone('America/Chicago')));
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
    I manage a number of websites running on Zend Server, Zend's PHP 
    application platform. I've started accumulating a number of patterns and 
    tricks that make the deployments more successful, and which also allow 
    me to do more advanced things such as setting up recurring jobs for the 
    application, clearing page caches, and more.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Yes, YOU can afford Zend Server</h2>

<p>
    "But, wait, Zend Server is uber-expensive!" I hear some folks saying.
</p>

<p>
    Well, yes and no.
</p>

<p>
    With the release of Zend Server 7, Zend now offers a "Development 
    Edition" that contains all the features I've covered here, and which 
    runs $195. This makes it affordable for small shops and freelancers, 
    but potentially out of the reach of individuals.
</p>

<p>
    But there's another option, which I'm using, which is even more 
    intriguing: <a href="http://www.zend.com/en/solutions/cloud-solutions/aws-marketplace">Zend Server on the Amazon Web Services (AWS) Marketplace</a>. 
    On AWS, you can try out Zend Server free for 30 days. After that, 
    you get charged a fee on top of your normal AWS EC2 usage. Depending 
    on the EC2 instance you choose, this can run as low as ~$24/month 
    (this is on the t1.micro, and that's the total per month for both 
    AWS and Zend Server usage). That's cheaper than most VPS hosting or 
    PaaS providers, and gives you a full license for Zend Server.
</p>

<p>
    Considering Zend Server is available on almost every PaaS and IaaS 
    offering available, this is a great way to try it out, as well as to 
    setup staging and testing servers cheaply; you can then choose the
    provider you want based on its other features. For those of you running 
    low traffic or small, personal or hobbyist sites, it's an 
    inexpensive alternative to VPS hosting.
</p>

<p>
    So... onwards with my first tip.
</p>

<h2>Tip 1: zf-deploy</h2>

<p>
    My first trick is to use 
    <a href="https://github.com/zfcampus/zf-deploy">zf-deploy</a>. This is a tool 
    <a href="https://twitter.com/ezimuel">Enrico</a> and I wrote when prepping 
    <a href="https://apigility.org">Apigility</a> for its initial stable release. 
    It allows you to create deployment packages from your application, 
    including zip, tarball, and ZPKs (Zend Server deployment packages). 
    We designed it to simplify packaging <a href="http://framework.zend.com">Zend Framework 2</a> and Apigility applications, but with a 
    small amount of work, it could likely be used for a greater variety 
    of PHP applications.
</p>

<p>
    zf-deploy takes the current state of your working directory, and 
    clones it to a working path. It then runs Composer (though you can 
    disable this), and strips out anything configured in your 
    <kbd>.gitignore</kbd> file (again, you can disable this). From there, it 
    creates your package.
</p>

<p>
    One optional piece is that, when creating a ZPK, you can tell it 
    which <kbd>deployment.xml</kbd> you want to use and/or specify a directory 
    containing the <kbd>deployment.xml</kbd> and any install scripts you want to 
    include in the package. This latter is incredibly useful, as you can 
    use this to shape your deployment.
</p>

<p>
    As an example, on my own website, I have a CLI job that will fetch 
    my latest <a href="https://github.com">GitHub</a> activity. I can invoke that 
    in my <kbd>post_stage.php</kbd> script:
</p>

<div class="example"><pre><code class="language-php">
if (! chdir(getenv('ZS_APPLICATION_BASE_DIR'))) {
  throw new Exception('Unable to change to application directory');
}

$php = '/usr/local/zend/bin/php';

$command = $php . ' public/index.php githubfeed fetch';
echo "\nExecuting `$command`\n";
system($command);
</code></pre></div>

<p>
    One task I always do is make sure my application data directory is 
    writable by the web server. This next line builds on the above, in 
    that it assumes you've changed to your application directory first:
</p>

<div class="example"><pre><code class="language-php">
$command = 'chmod -R a+rwX ./data';
echo "\nExecuting `$command`\n";
system($command);
</code></pre></div>

<p>
    Yes, PHP has a built-in for <kbd>chmod</kbd>, but it doesn't act recursively.
</p>

<p>
    For ZF2 and Apigility applications, zf-deploy also allows you to 
    specify a directory that contains the <kbd>*local.php</kbd> config 
    scripts for your <kbd>config/autoload/</kbd> directory, allowing you to 
    merge in configuration specific for the deployment environment. 
    This is a fantastic capability, as I can keep any private 
    configuration separate from my main repository.
</p>

<p>
    Deployment now becomes:
</p>

<div class="example"><pre><code class="language-bash">
$ vendor/bin/zfdeploy.php mwop.net.zpk --configs=../mwop.net-config --zpk=zpk
</code></pre></div>

<p>
    and I now have a ZPK ready to push to Zend Server.
</p>

<p>
    In sum: zf-deploy simplifies ZPK creation, and allows you to add 
    deployment scripts that let you perform other tasks on the server.
</p>

<h2>Next time...</h2>

<p>
    Next tip: creating scheduled Job Queue jobs, à la cronjobs.
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
    <li><a href="/blog/2014-09-11-zend-server-deployment-part-6.html">Tip 6: Page caching</a></li>
    <li><a href="/blog/2014-09-16-zend-server-deployment-part-7.html">Tip 7: zs-client</a></li>
    <li><a href="/blog/2014-09-18-zend-server-deployment-part-8.html">Tip 8: Automate</a></li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
