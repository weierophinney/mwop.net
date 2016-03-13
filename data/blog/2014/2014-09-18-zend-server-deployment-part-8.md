---
id: 2014-09-18-zend-server-deployment-part-8
author: matthew
title: 'Deployment with Zend Server (Part 8 of 8)'
draft: false
public: true
created: '2014-09-18T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the final in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-09-16-zend-server-deployment-part-7.html)
detailed using the [Zend Server SDK](https://github.com/zend-patterns/ZendServerSDK)
to deploy your Zend Server deployment packages (ZPKs) from the command line.

Today, I'll detail how I automate deployment with
[zf-deploy](https://github.com/zfcampus/zf-deploy) and zs-client (the Zend
Server SDK), and wrap up the series with some closing thoughts.

<!--- EXTENDED -->

Tip 8: Automate
---------------

Over the course of the series:

- I've defined Job Queue scripts for scheduled tasks I want to run.
- I've defined deployment scripts to automate deployment tasks on the server,
  including scheduling the above Job Queue scripts, as well as to prep the
  environment for my application.
- I'm using zf-deploy to create ZPK packages to push to the server, which
  contain the above scripts, as well as my deployment configuration.
- I'm using the Zend Server SDK to deploy our ZPK.

But it's a bunch of manual steps. What if I could automate it?

There are a ton of tools for this sort of thing. I could write a shell script.
I could use [Phing](http://www.phing.info).

I personally like to use [make](http://www.gnu.org/software/make/) for this
(yeah, I'm a dinosaur). As an example:

```makefile
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
```

The above ensures my ZPKs have versioned names, allowing me to keep the last
few in the working directory for reference; the clean target will remove them
for me when I'm ready. Using make also gives me some granularity; if I want to
build the ZPK only, so I can inspect it, I can use make zpk.

Of course, if there's any other pre- or post-processing I want to do as part of
my build process, I can do that as well. (In my actual script, I do some
pre-processing tasks.)

The main takeaway, though, is: automate the steps. This makes it trivial for
you to deploy when you want to, and the more trivial you make deployment, the
more likely you are to push new changes with confidence.

Closing Thoughts
----------------

I've been quite happy with my experiments using Zend Server, and have become
quite confident with the various jobs and deployment scripts and jobs I've
written. They make deployment trivial, which is something I'm quite happy with.
I'm even happier having my site on AWS, as it gives me some options for scaling
should I need them later.

With the tricks and tips in this series, hopefully you'll find yourself
successfully deploying *your* applications to Zend Server!

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
