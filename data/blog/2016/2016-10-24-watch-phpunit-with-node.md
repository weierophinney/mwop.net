---
id: 2016-10-24-watch-phpunit-with-node
author: matthew
title: 'Automating PHPUnit with Node'
draft: false
public: true
created: '2016-10-24T15:25:00-05:00'
updated: '2016-10-24T15:25:00-05:00'
tags:
    - gulp
    - node
    - npm
    - yarn
    - php
    - phpcs
    - phpunit
    - programming
---

I've been trying to automate everything this year. When working on OSS, this is
usually as simple as setting up [Travis CI](https://www.travis-ci.org); in
[some cases](/blog/2015-12-14-secure-phar-automation.html),
[even that](/blog/2016-01-29-automating-gh-pages.html) becomes
[a little more involved](/blog/2016-06-30-aws-codedeploy.html), but remains
possible.

But that's *continuous integration*. What about *continuous development*?

<!--- EXTENDED -->

## Continuous development?

With continous integration, every time I push to a branch associated with a pull
request or on the origin repository, a build is triggered. Which is great,
because I can verify and validate that my code runs fine on all the target
platforms. But I have to wait for the build to trigger and then run.

Ideally, I should also be testing locally; I likely don't want to push anything
upstream that will fail! So, I look in the contributing guidelines, and
determine how to run coding standards checks and unit tests, and do those
manually.

Manually? Ugh. Too easy to forget, and too easy to lose track and make a ton of
changes between runs, making breakage easier.

I'd like to *automate* running these as part of my development process. I want
*continuous development* cycles.

## Preparing your project

The first step is preparing your project. I like to run my tests and CS checks
using [Composer](https://getcomposer.org), as that allows me to change what I'm
using later, but also allows me to standardize invocation of the tools. I define
the following *scripts* in my `composer.json`:

```json
"scripts": {
  "check": [
    "@cs-check",
    "@test"
  ],
  "cs-check": "phpcs --colors",
  "cs-fix": "phpcbf --colors",
  "test": "phpunit --colors=always"
}
```

You may, of course, need to alter these to use the tools specific to your own
project. The main thing is that you have a "check" target, which runs all the
various QA tools.

You don't *need* to do this. But I definitely recommend it. If you can simplify
invocation for your users, and for your *tools*, automation is far easier.

## Using gulp

[Node](https://nodejs.org/) has some great tools for watching the filesystem and
reacting to it. Two of these are considered "build" or "workflow" tools:
[Grunt](http://gruntjs.com) and [Gulp](http://gulpjs.com).

I've opted for Gulp here, as the setup is far simpler; that said, it's not
difficult to do in Grunt, either.

First, you'll need [npm](https://www.npmjs.com/), which usually comes packaged
with node, or [yarn](https://yarnpkg.com/), a more recent addition to the node
ecosystem. Once you have these, you can continue.

Second, I installed a few dependencies:

- `gulp` is the actual taskrunner. It needs to be installed both *globally*, and
  *locally*. It includes the functionality for watching the filesystem.
- `gulp-shell` provides the ability to execute arbitrary command line tools.
- `gulp-notify` ties into your system's notifications abilities.

Navigate to your project directory, and install these as follows:

```bash
$ npm install -g gulp # this may require sudo, depending on your system
$ npm install --dev gulp gulp-shell gulp-notify
```

If you are using yarn:

```bash
$ yarn global add gulp # this may require sudo, depending on your system
$ yarn add --dev gulp gulp-shell gulp-notify
```

Third, create the following `gulpfile.js` in your project:

```javascript
/* jshint: node: true */
var gulp = require('gulp');
var notify = require('gulp-notify');
var shell = require('gulp-shell');
var project = require('path').posix.basename(__dirname);

gulp.task('default', ['watch']);
gulp.task('php_check', function () {
  gulp.src('')
    .pipe(shell('composer check'))
    .on('error', notify.onError({
      title: project + ' failures',
      message: project + ' CS checks and/or tests failed'
    }));
});
gulp.task('watch', function () {
  gulp.watch(
    ['phpunit.xml.dist', 'phpcs.xml', 'src/**/*.php', 'test/**/*.php'],
    ['php_check']
  );
});
```

What the above does is:

- Watch the filesystem for changes to any of:
  - `phpunit.xml.dist`, which would indicate a change to the test runner behavior.
  - `phpcs.xml`, which would indicate a change to the coding standards.
  - PHP files found in either the `src/` or `test/` directories.
- On changes, run `composer check`.
- On errors running `composer check`, create a system notification.

### Using the gulp automation

Once you've done the above, run the following within your project directory:

```bash
$ gulp
```

This will spawn a process that watches the filesystem; any time you save a
change to any of the files listed, it will run `composer check`, which in turn
runs your CS checks and unit tests. If either of these processes fails, it
spawns a system notification, which will draw your attention to the fact that
you've just done something wrong. (If no errors occur, no notification is
created.)

What this means is that I can spawn the process in a terminal that I hide, and
then start editing in my favorite editor or IDE, and get notifications
immediately when I break something.

## Making it reusable with node

The above is nice, but do you *really* want to add this to every project? While
it's a useful utility, different projects run things differently, and some may
or may not be amenable to adding tooling just to support specific development
workflows.

So, I decided to take this a step further, and see if I could automate for the
more generic use case. The result is an npm package,
[phly-php-qa-watch](https://www.npmjs.com/package/phly-php-qa-watch),
which ships with the binary `php-qa-watch`.

Install it as follows:

```bash
$ npm install phly-php-qa-watch -g  # via npm; may require sudo
$ yarn global add phly-php-qa-watch # via yarn; may require sudo
```

Once installed, you can run it in your project:

```php
$ php-qa-watch
```

If you need to specify an alternate checker, or a different list of files, flags
will provide this:

- `-c|--check-command` allows you to specify an alternate check command to use;
  it defaults to `composer-check`.
- `-w|--watch-files` allows you to specify a comma-separated list of files,
  directories, and globs to watch. It defaults to
  `phpunit.xml.dist,phpcs.xml,src/**/*.php,test/**/*.php`.

So, as an example, I could run:

```bash
$ php-qa-watch \
> -c "./vendor/bin/php-cs-fixer fix --dry-run && ./vendor/bin/phpunit" \
> -w ".php_cs,phpunit.xml.dist,phpunit.xml,lib/**/*.php,tests/**/*.php"
```

The above would run the locally installed `php-cs-fixer` in dry-run mode and
phpunit. Further, it would re-run if the `php-cs-fixer` configuration changes,
the local or project PHPUnit configuration changes, or if PHP files in either
the `lib/` or `tests/` directories change.

This finally hits the sweet-spot for me with automation: it's literally the
least effort I can muster in any given repository in order to automate my
testing tools.

## Automate all the things

It's terribly easy to get complacent and lazy when developing, particularly on
open source where you may be investing your off hours, and want to economize
your activity. This can lead to having patches rejected, or frustration at only
discovering a completely avoidable build error days later, as you left your
console immediately after issuing a pull request. For these reasons, I try and
automate whenever possible, not just for continuous integration, but for my own
development workflow.

> ### Note
>
> This post is derived from a talk I prepared recently on quality PHP packages,
> and details one facet of the "automation" section. I plan to blog on the
> topics covered, and release related tools, in the coming weeks.
