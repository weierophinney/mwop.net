---
id: 2012-11-01-openshift-zf2-composer
author: matthew
title: 'OpenShift, ZF2, and Composer'
draft: false
public: true
created: '2012-11-01T15:25:00-05:00'
updated: '2012-11-01T15:25:00-05:00'
tags:
    - zf2
    - cloud
    - composer
---
I was recently shopping around for inexpensive cloud hosting; I want to try out
a couple of ideas that may or may not have much traffic, but which aren't
suited for my VPS setup (the excellent [ServerGrove](http://servergrove.com/));
additionally, I'm unsure how long I will maintain these projects. My budget for
this is quite small as a result; I'm already paying for hosting, and am quite
happy with it, so this is really for experimental stuff.

I considered Amazon, Orchestra.io, and a few others, but was concerned about
the idea of a ~$50/month cost for something I'm uncertain about.

When I asked in [#zftalk.dev](irc://irc.freenode.net/zftalk.dev), someone
suggested [OpenShift](http://openshift.redhat.com/) as an idea, and
coincidentally, the very next day
[Zend announced a partnership with RedHat surrounding OpenShift](http://www.zend.com/en/company/news/press/379_red-hat-expands-openshift-ecosystem-with-zend-partnership-to-offer-professional-grade-environment-for-php-developers).
The stars were in alignment.

In the past month, in the few spare moments I've had (which included an
excellent OpenShift hackathon at ZendCon), I've created a quick application
that I've deployed and tested in OpenShift. These are my findings.

<!--- EXTENDED -->

ZF2
---

I didn't really have to do anything different to have
[zf2](http://framework.zend.com/) work; the standard `.htaccess` provided in
the skeleton application worked flawlessly the first time (I've worked with
some cloud environments where this is not the case).

The only frustration I had was the default directory structure OpenShift foists
upon us:

```
data/
libs/
misc/
php/
```

This is not terrible, by any stretch. However, it's attempting to dictate the
application structure, which I'm not terribly happy with — particularly as my
structure may vary based on the framework I'm using (or not!), and because I
may already have a project written that I simply want to deploy.

In particular, the `php` directory is galling — it's simply the document root.
Most frameworks I've used or seen call the equivalent directory `public`, or
`web`, or `html` — but never `php` (in large part because the only PHP file
under the document root in most frameworks is the `index.php` that acts as the
front controller). It would be nice if this were configurable.

This conflicts a bit with how a ZF2 app is structured. I ended up doing the
following:

- Removed `php` and symlinked my `public` directory to it.
- Removed `libs` and symlinked my `vendor` directory to it.
- Removed `misc` as I had no need to it.

Nothing too big, thankfully — but problematic from the perspective of, "I've
already developed this app, but now I have to make changes for it to work on a
specific cloud vendor."

Composer
--------

My next question was how to use [Composer](http://getcomposer.org/) during my
deployment process, and some some googling
[found some answers for me](https://openshift.redhat.com/community/content/support-for-git-clone-on-the-server-aka-support-php-composerphar).

Basically, I needed to create a `deploy` task that does two things:

- Unset the `GIT_DIR` environment variable. Evidently, the build process
  operates as part of a git hook, and since Composer often uses git
  repositories, this can lead to problems.
- Change directory to `OPENSHIFT_REPO_DIR`, which is where the application root
  (not document root!) lives.

Once I did those, I could run my normal composer installation. The `deploy`
task looks like this:

```bash
#!/bin/bash
# .openshift/action_hooks/deploy
( unset GIT_DIR ; cd $OPENSHIFT_REPO_DIR ; /usr/local/zend/bin/php composer.phar install )
```

This leads into my next topic.

Deployment
----------

First off, as you probably guessed from that last secton, there **are** hooks
for deployment — it doesn't have to be simply git. I like this, as I may have
additional things I want to do during deployment, such as retrieving and
installing site-specific configuration files, installing Composer-defined
dependencies (as already noted), etc.

Over all, this is pretty seamless, but it's not without issues. I've been told
that some of my issues are being worked on, so those I won't bring up here. The
ones that were a bit strange, and which caught me by surprise, though, were:

- Though the build process creates the site build from git, your **submodules
  are not updated recursively**. This tripped me up, as I was using
  [EdpMarkdown](https://github.com/EvanDotPro/EdpMarkdown), and had installed
  it as a submodule. I ended up having to import it, and its own submodule,
  directly into my project so that it would work.
- I installed the [MongoDB](http://www.mongodb.org/) cartridge. Ironically, it
  was not then enabled in Zend Server, and I had to go do this. This should be
  turnkey.
- `/usr/bin/php` is not the same as `/usr/local/zend/bin/php`. This makes no
  sense to me if I've installed Zend Server as my base gear. Considering
  they're different versions, this can be hugely misleading and lead to errors.
  I understand there are reasons to have both — so simply be aware that if you
  use the Zend Server gear, your tasks likely should use
  `/usr/local/zend/bin/php`.

The good parts?
---------------

- [You can alias an application to a DNS
  CNAME](https://openshift.redhat.com/community/faq/i-have-deployed-my-app-but-i-don%E2%80%99t-like-telling-people-to-visit-myapp-myusernamerhcloudcom-how-c)
  — meaning you can point your domain name to your OpenShift applications.
  Awesome!
- Simplicity of adding capabilities, such as Mongo, MySQL, Cron, and others. In
  most cases, this is simply a "click on the button" and it's installed and
  available.
- [Zend Server](http://www.zend.com/en/products/server). For most PHP
  extensions, I can turn them on or off with a few mouse clicks. If I want
  page-level caching, I don't have to do anything to my application; I can
  simply setup some rules in the Zend Server interface and get on with it, and
  enjoy tremendous boosts to performance. I used to enjoy taming and tuning
  servers; most days anymore, I just want them to work.
- [SSH](https://openshift.redhat.com/community/developers/remote-access) access
  to the server, with a number of commands to which I've been given `sudoer`
  access. If you're going to sandbox somebody, this is a fantastic way to do
  it. Oh, also: SSH tunnels to services like Mongo and MySQL just work (via the
  `rhc-port-forward` command).

Summary
-------

Over all, I'm quite pleased. While it took me a bit to find the various
incantations I needed, the service is quite flexible. For my needs, considering
I'm doing experimental stuff, the price can't be beat (the current developer
preview is free). Considering most stuff I do will fall into this or the basic
tier, and that most cartridges do not end up counting against your alotment of
gears, the pricing ($0.05/hour) is extremely competitive.
