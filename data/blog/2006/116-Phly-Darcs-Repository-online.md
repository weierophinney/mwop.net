---
id: 116-Phly-Darcs-Repository-online
author: matthew
title: 'Phly Darcs Repository online'
draft: false
public: true
created: '2006-06-05T21:49:49-04:00'
updated: '2006-06-05T23:05:09-04:00'
tags:
    - php
---
A [darcs repository browser](http://weierophinney.net/phly/darcs/) is now online for the [Phly channel](http://weierophinney.net/phly/).

If you're not familiar with [darcs](http://abridgegame.org/darcs/), it's a revision control system, similar to [GNU Arch](http://www.gnu.org/software/gnu-arch/) and [git](http://git.or.cz/); changes are kept as patch sets, and repositories are branched simply by checking them out. This makes darcs repositories very flexible, and incredibly easy to implement. Static binaries are available for most systems, which makes it easy to install on systems to which you have no administrator rights.

A perl CGI script is shipped with darcs, and provides a web-based repository viewer. It utilizes darcs' `--xml-output` switch to create XML, which is then transformed using XSLT. However, there are some issues with the script; it is somewhat difficult to customize, and makes many assumptions about your system (location of configuration files, repositories, etc.). To make it more flexible, I ported it to PHP, using [Cgiapp2](http://weierophinney.net/phly/index.php?package=Cgiapp2) and its XSLT template plugin and [Phly_Config](http://weierophinney.net/phly/index.php?package=Phly_Config).

I have released this PHP darcs repository browser as [Phly_Darcs](http://weierophinney.net/phly/index.php?package=Phly_Darcs), which contains both a Model and Controller, as well as example XSLT view templates. It is currently in beta as I'm still developing PHPUnit2 tests for some of the model functionality, as well as debating the ability to add write capabilities (to authenticated users only, of course).

**Update:** fixed links to internal pages to use absolute urls.
