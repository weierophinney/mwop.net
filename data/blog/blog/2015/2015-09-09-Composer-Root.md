---
id: 2015-09-09-composer-root
author: matthew
title: 'Fixing Version Issues When Running Composer from a Branch'
draft: false
public: true
created: '2015-09-09T09:10:00-05:00'
updated: '2015-09-09T09:10:00-05:00'
tags:
    - php
    - programming
---
For the Zend Framework component repositories, we occasionally need to backport
changes to the [2.4 LTS](http://framework.zend.com/long-term-support) releases. This
requires checking out a branch based off the last LTS tag, applying patches (often
with edits to translate PHP 5.5 syntax to PHP 5.3), and running tests against
PHP 5.3 and 5.4.

Of course, to run the tests, you need the correct set of dependencies installed.
If you have any component dependencies, that means running a `composer update`
to ensure that you get the 2.4 versions of those components.

And that's where my story begins.

<!--- EXTENDED -->

## Incorrect dependencies

I was in exactly that situation yesterday with one of our components, zend-validator.
This component has a dependency on zend-uri, which, unfortunately, also has a
dependency on zend-validator. Normally this is not a problem, as the components have
specified the version `self.version` on any other ZF components.

The problem occurs when I'm in a branch composer does not know about. In that
situation, Composer does not know what version is required, and assumes the latest.
For the ZF components, it then wants to use `2.5.x@dev`, but cannot, because I'm
on PHP 5.3. It then drops back to 2.4 versions, but cannot find one that directly
matches the release branch name (in this case, `dev-release-2.4.`).

## Fix the dependencies

My first attempt at a fix was to change the zend-validator dependencies. At first,
I tried `^2.4.0`, but this is the same as saying `>=2.4.0,<3.0.0` — leaving me with
the same problems with regards to Composer prefering a 2.5 version.

I then changed it to `~2.4.0`, but the issue then became a problem with the
dependencies, which were still specifying `self.version`, and could not resolve
to a known version.

## Forcing the version

Fortunately, I found a solution on the [Composer troubleshooting page](https://getcomposer.org/doc/articles/troubleshooting.md#package-not-found-on-travis-ci-org).
The tip is specifically for Travis-CI, but works equally well locally. Essentially,
you force Composer to report the package as a specific version.

In my case, I did the following:

```bash
$ COMPOSER_ROOT_VERSION=2.4.7 composer install
```

And voilá! Any dependencies that utilized `self.version` now resolved to 2.4.8,
and my semantic version specifiers for the root package (`~2.4.0`) were properly
respected.
