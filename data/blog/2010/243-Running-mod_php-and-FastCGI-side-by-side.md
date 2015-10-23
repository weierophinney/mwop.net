---
id: 243-Running-mod_php-and-FastCGI-side-by-side
author: matthew
title: 'Running mod_php and FastCGI side-by-side'
draft: false
public: true
created: '2010-08-09T10:45:00-04:00'
updated: '2010-08-15T09:40:29-04:00'
tags:
    - php
    - fastcgi
---
Because we're in full throes of [Zend Framework](http://framework.zend.com) 2.0
development, I find myself with a variety of PHP binaries floating around my
system from both the PHP 5.2 and 5.3 release series. We're at a point now where
I'm wanting to test migrating applications from ZF 1.X to 2.0 to se see what
works and what doesn't. But that means I need more than one PHP binary enabled
on my server…

I use [Zend Server](http://www.zend.com/products/server/) on my development box;
it's easy to install, and uses my native Ubuntu update manager to get updates.
On Ubuntu, it installs the Debian Apache2 packages, so I get the added bonus of
familiarity with the configuration structure.

I installed Zend Server some time ago, so I'm still on a PHP 5.2 `mod_php`
binary. I have several PHP 5.3 binaries compiled and installed locally for
running unit tests and sample scripts already — so the question was how to keep
my 5.2 `mod_php` running while simultaneously allowing the ability to run
selected vhosts in 5.3?

The answer can be summed up in one acronym: FastCGI.

<!--- EXTENDED -->

With a little help from [Ralph Schindler](http://ralphschindler.com), I got things setup.

Enabling FastCGI on Ubuntu's Apache
-----------------------------------

Interestingly, FastCGI is not enabled by default, nor is another module you'll
need, `mod_actions`. You can enable these very easily though:

```bash
$ cd /etc/apache2/mods-enabled
$ sudo ln -s ../mods-available/fastcgi.load .
$ sudo ln -s ../mods-available/fastcgi.conf .
$ sudo ln -s ../mods-available/actions.load .
$ sudo ln -s ../mods-available/actions.conf .
```

Create a FastCGI-enabled vhost
------------------------------

Next, you need to add a new vhost that will utilize FastCGI. I copied an
existing vhost I had in my `/etc/apache2/sites-enabled` tree, modified it to
give it a unique `ServerName` and `DocumentRoot`, and added the following lines:

```apacheconf
ScriptAlias /cgi-bin/ /path/to/zfproject/public/cgi-bin/
AddHandler php-fcgi .php
Action php-fcgi /cgi-bin/php-5.3.1
```

The name of the PHP script doesn't matter much; I used "php-5.3.1" so that I
could visually recognize what version of PHP I was using with that vhost.

Create a "cgi-bin" directory and CGI script
-------------------------------------------

Finally, I needed to actually create the "cgi-bin" directory and CGI script to
execute. This was relatively simple; I navigated to my project's `DocumentRoot`,
and created a new directory `cgi-bin` (`mkdir cgi-bin`).

I then entered that directory and created a new script, based on the name I
provided in my vhost. That script, `cgi-bin/php-5.3.1` then simply `exec`'s the
`php-cgi` binary from my PHP install.

### Note about CGI binaries

In PHP 5.3 and up, CGI binaries are built by default — and they're already
FastCGI enabled. In PHP 5.2, CGI versions are still built by default, but they
are not FastCGI-enabled unless you explicitly pass the `--enable-fastcgi`
configure flag. To determine if you did that when compiling, execute the
following:

```bash
$ php-cgi -i | grep fcgi
```

If you get no output, you need to recompile.

My script looks like this:

```bash
#!/bin/bash
exec /path/to/php/install/bin/php-cgi "$@"
```

Because this is a CGI binary, you can pass additional CLI arguments and
environment variables; try experimenting with setting your `include_path`,
application environment, etc.

Once you're done creating the script, make sure it's executable:

```bash
$ chmod a+x php-5.3.1
```

Fire it up!
-----------

Once I'd done the above, I restarted my Apache instance
(`sudo /etc/init.d/apache2 restart`). After ensuring there were no startup
errors, I navigated to my new vhost, and *voila!* it was running.

For those of you doing your first forays into PHP 5.3, this is an excellent way
to test code without needing a separate server running. It's also a great way to
test whether your application is 5.3-ready — create a 5.3-enabled vhost pointing
to your existing application and see if it runs.
