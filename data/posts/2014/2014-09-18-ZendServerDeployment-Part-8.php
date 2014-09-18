<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-09-18-zend-server-deployment-part-8');
$entry->setTitle('Deployment with Zend Server (Part 8 of 8)');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-09-18 08:30', new \DateTimezone('America/Chicago')));
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
    This is the final in a series of eight posts detailing tips on deploying
    to Zend Server. <a href="/blog/2014-09-16-zend-server-deployment-part-7.html">The previous post in the series</a>
    detailed using the <a href="https://github.com/zend-patterns/ZendServerSDK">
    Zend Server SDK</a> to deploy your Zend Server deployment packages (ZPKs) 
    from the command line.
</p>

<p>
    Today, I'll detail how I automate deployment with <a href="https://github.com/zfcampus/zf-deploy">zf-deploy</a>
    and zs-client (the Zend Server SDK), and wrap up the series with some closing thoughts.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Tip 8: Automate</h2>

<p>
    Over the course of the series:
<p>

<ul>
    <li>I've defined Job Queue scripts for scheduled tasks I want to run.</li>
    <li>I've defined deployment scripts to automate deployment tasks on the 
        server, including scheduling the above Job Queue scripts, as well as to 
        prep the environment for my application.</li>
    <li>I'm using zf-deploy to create ZPK packages to push to the server, which 
        contain the above scripts, as well as my deployment configuration.</li>
    <li>I'm using the Zend Server SDK to deploy our ZPK.</li>
</ul>

<p>
    But it's a bunch of manual steps. What if I could automate it?
</p>

<p>
    There are a ton of tools for this sort of thing. I could write a shell 
    script. I could use <a href="http://www.phing.info">Phing</a>.
</p>

<p>
    I personally like to use <a 
    href="http://www.gnu.org/software/make/">make</a> for this (yeah, I'm a 
    dinosaur). As an example:
</p>

<div class="example"><pre><code class="language-bash">
PHP ?= $(shell which php)
VERSION ?= $(shell date -u +"%Y.%m.%d.%H.%M")
CONFIGS ?= $(CURDIR)/../site-settings
ZSCLIENT ?= zs-client.phar
ZSTARGET ?= mwop

COMPOSER = $(CURDIR)/composer.phar

.PHONY : all composer zpk deploy clean

all : deploy

composer :
	@echo "Ensuring composer is up-to-date..."
	-$(COMPOSER) self-update
	@echo "[DONE] Ensuring composer is up-to-date..."

zpk : composer
	@echo "Creating zpk..."
	-$(CURDIR)/vendor/bin/zfdeploy.php build mwop-$(VERSION).zpk --configs=$(CONFIGS) --zpkdata=$(CURDIR)/zpk --version=$(VERSION)
	@echo "[DONE] Creating zpk."

deploy : zpk
	@echo "Deploying ZPK..."
	-$(ZSCLIENT) applicationUpdate --appId=20 --appPackage=mwop-$(VERSION).zpk --target=$(ZSTARGET)
	@echo "[DONE] Deploying ZPK."

clean :
	@echo "Cleaning up..."
	-rm -Rf $(CURDIR)/*.zpk
	@echo "[DONE] Cleaning up."
</code></pre></div>

<p>
    The above ensures my ZPKs have versioned names, allowing me to keep the 
    last few in the working directory for reference; the <kbd>clean</kbd> target will 
    remove them for me when I'm ready. Using make also gives me some 
    granularity; if I want to build the zpk only, so I can inspect it, I can 
    use <kbd>make zpk</kbd>.
</p>

<p>
    Of course, if there's any other pre- or post-processing I want to do as 
    part of my build process, I can do that as well. (In my actual script, I do 
    some pre-processing tasks.)
</p>

<p>
    The main takeaway, though, is: automate the steps. This makes it trivial 
    for you to deploy when you want to, and the more trivial you make deployment, 
    the more likely you are to push new changes with confidence.
</p>

<h2>Closing Thoughts</h2>

<p>
    I've been quite happy with my experiments using Zend Server, and have 
    become quite confident with the various jobs and deployment scripts and 
    jobs I've written. They make deployment trivial, which is something I'm 
    quite happy with. I'm even happier having my site on AWS, as it gives me 
    some options for scaling should I need them later.
</p>

<p>
    With the tricks and tips in this series, hopefully you'll find yourself 
    successfully deploying <em>your</em> applications to Zend Server!
</p>

<h2>Other articles in the series</h2>

<ul>
    <li><a href="/blog/2014-08-11-zend-server-deployment-part-1.html">Tip 1: zf-deploy</a></li>
    <li><a href="/blog/2014-08-28-zend-server-deployment-part-2.html">Tip 2: Recurring Jobs</a></li>
    <li><a href="/blog/2014-09-02-zend-server-deployment-part-3.html">Tip 3: chmod</a></li>
    <li><a href="/blog/2014-09-04-zend-server-deployment-part-4.html">Tip 4: Secure your job scripts</a></li>
    <li><a href="/blog/2014-09-09-zend-server-deployment-part-5.html">Tip 5: Set your job status</a></li>
    <li><a href="/blog/2014-09-11-zend-server-deployment-part-6.html">Tip 6: Page caching</a></li>
    <li><a href="/blog/2014-09-16-zend-server-deployment-part-7.html">Tip 7: zs-client</a></li>
</ul
EOT;
$entry->setExtended($extended);

return $entry;
