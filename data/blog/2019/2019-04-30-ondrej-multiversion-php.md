---
id: 2019-04-30-ondrej-multiversion-php
author: matthew
title: 'Managing Multiple PHP versions via the ondrej/php PPA'
draft: false
public: true
created: '2019-04-30T17:10:00-05:00'
updated: '2019-04-30T17:10:00-05:00'
tags:
    - php
    - ubuntu
---

Last week, I did some system updates, and then decided to compile the most
recent PHP releases. I've used [phpbrew](https://phpbrew.github.io/phpbrew/) to
manage multiple PHP releases for a number of years, and having it install a new
version is fairly routine.

Except this time, it wasn't. Due to updates I installed, I was getting errors
first with compiling the GD extension, then with ext-intl:

- If you want Freetype support in ext-gd, you are expected to install the
  package libfreetype-dev. On Ubuntu, this now installs libfreetype6-dev, which
  no longer includes the `freetype-config` binary that PHP's `configure` script
  uses to determine what features it supports.

- Similarly, ext-intl depends on the package libicu-dev. Ubuntu's package now
  omits the `icu-config` binary used by PHP to determine feature support.

I searched for quite some time to find packages that would resolve these
problems. I could have found the source code and compiled it and linked to that,
but that would mean keeping that up-to-date on top of my PHP installs.

I even looked in the [ondrej/php PPA](https://deb.sury.org/), as that repository
has multiple PHP versions already, including source packages.

And then I thought: why not try using those instead of phpbrew?

The rest of this post is how I made that work.

> I use Ubuntu for my operating system. The instructions I present here should
> work on any Debian-based system, but your mileage may vary. If you are using
> an RPM-based system, `yum` will be your friend, but I have no idea how to add
> repositories in that system, nor if `update-alternatives` is available. As
> such, these instructions may or may not help you.
>
> Which is okay. I mainly wrote them to help future me.

## Register the PPA

First, I had to add the PPA to the system:

```bash
$ sudo add-apt-repository ppa:ondrej/ppa
```

Then the usual:

```bash
$ sudo apt update
```

## Approach to installation

I first identified the extensions I typically install, matched them to
packages in the PPA, and made a list. I knew I'd be installing the same
extensions across all PHP versions I wanted, so I figured I could script it a
bit.

From there, I executed the following from the CLI:

```bash
$ for VERSION in 5.6 7.0 7.1 7.2 7.3;do
for> for EXTENSION in {listed all extensions here};do
for for> sudo apt install php${VERSION}-${EXTENSION}
for for> done
for> done
```

This grabbed and installed each PHP I needed along with all extensions I wanted.

## Switching between versions

To switch between versions, you have two options:

- Use the version-specific binaries: `/usr/bin/php5.6`, `/usr/bin/php7.0`, etc.

- Set a default via `update-alternatives`:
  ```bash
  $ sudo update-alternatives --set php /usr/bin/php5.6
  ```
  
  If you're not sure what you have installed, use:
  
  ```bash
  $ sudo update-alternatives --config php
  ```
  
  which will give you a listing, and an ability to select the one to use.

### Rootless alternatives

What if you'd rather not be root to switch the default version, though?
Fortunately, `update-alternatives` allows specifying alternate config and admin
directories.

Define the following alias in your shell's configuration:

```bash
alias update-my-alternatives='update-alternatives \
 --altdir ~/.local/etc/alternatives \
 --admindir ~/.local/var/lib/alternatives'
```

Additionally, make sure you add `$HOME/.local/bin` to your `$PATH`; since
defining `$PATH` varies based on the shell you use, I'll leave that for you to
accomplish.

If you open a new shell, the alias will now be available; alternately, source
the file in which you defined it to have it take effect immediately.

Once you've done that, you can run the following, based on the PHP versions
you've installed:

```bash
$ for VERSION in 5.6 7.0 7.1 7.2 7.3;do
for> update-my-alternatives --install $HOME/.local/bin/php php /usr/bin/php${VERSION} ${VERSION//./0}
for> done
```

This will create alternatives entries local to your own user, prioritizing them
by version; as a result, the default, auto-selected version will be the most
recently installed.

You can verify this by running `update-my-alternatives --config php`:

```text
There are 5 choices for the alternative php (providing $HOME/.local/bin/php).

  Selection    Path             Priority   Status
------------------------------------------------------------
* 0            /usr/bin/php7.3   703       auto mode
  1            /usr/bin/php5.6   506       manual mode
  2            /usr/bin/php7.0   700       manual mode
  3            /usr/bin/php7.1   701       manual mode
  4            /usr/bin/php7.2   702       manual mode
  5            /usr/bin/php7.3   703       manual mode

Press <enter> to keep the current choice[*], or type selection number:
```

To switch between versions using the alias:

- Switch to a specific, known version:
  ```bash
  $ update-my-alternatives --set php /usr/bin/php{VERSION}
  ```

- Switch back to the default version (version with highest priority):
  ```bash
  $ update-my-alternatives --auto php
  ```

- List available versions:
  ```bash
  $ update-my-alternatives --list php
  ```

- Interactively choose a version when you're not sure what's available:
  ```bash
  $ update-my-alternatives --config php
  ```

> The above was cobbled together from:
>
> - https://serverfault.com/a/811377
> - https://williamdemeo.github.io/linux/update-alternatives.html

## PECL

Compiling and installing your own extensions turns out to be a bit of a pain
when you have multiple PHP versions installed, mainly because there is exactly
one PECL binary installed.

First, you need to install a few packages, including the one containing PEAR
(PECL uses the PEAR installer), and the development packages for each PHP
version you use (as those contain the tools necessary to compile extensions,
including `phpize` and `php-config`):

```bash
$ sudo apt install php-pear
$ for VERSION in 5.6 7.0 7.1 7.2 7.3;do
for> sudo apt install php${VERSION}-dev
for> done
```

### Compiling extensions

From there, you need to:

1. Ensure the correct `phpize` and `php-config` are selected.
1. Install the extension.
1. Tell PECL to deregister the extension in its own registry.

Normally, you would accomplish the first point by doing the following:

```bash
$ sudo update-alternatives --set php /usr/bin/php7.3
$ sudo update-alternatives --set php-config /usr/bin/php-config7.3
$ sudo update-alternatives --set phpize /usr/bin/phpize7.3
```

> Note that the above is **not** using the `update-my-alternatives` alias
> detailed in the previous section. This is because extensions must be installed
> at the **system** level.
>
> That said, the above won't be necessary, as I detail below.

However, PECL now has a really nice configuration flag, `php_suffix`, that
allows specifying a string _to append to each of the php, phpize, and php-config
binary names_. So, for example, if I specify `pecl -d php_suffix=7.3`, the
string `7.3` will be appended to those, so that they become `php7.3`,
`phpize7.3`, and `php-config7.3`, respectively. This ensures that the correct
scripts are called during the build process, and that the extension is installed
to the correct location.

As for the last point in that numbered list, it's key to being able to install
an extension in multiple PHP versions; otherwise, each subsequent attempt, even
when using a different PHP version, will result in PECL indicating it's already
installed. The `-r` switch tells PECL to remove the package from its own
registry, _but not to remove any build artifacts_.

As a complete example:

```bash
$ sudo pecl -d install php_suffix=7.3 swoole && sudo pecl uninstall -r swoole
```

### Registering extensions

From there, you still have to register, and optionally configure, the extension.
To do this, drop a file named after the extension in
`/etc/php/${PHP_VERSION}/mods-available/${EXTENSION}.ini`, with the following
contents:

```dosini
; configuration for php ${EXTENSION} module
; priority=20
extension=${EXTENSION}.so
```

Now that this is in place, enable it:

```bash
$ sudo phpenmod -v ${PHP_VERSION} -s cli ${EXTENSION}
```

(To disable it, use `phpdismod` instead.)

## Thoughts

When thinking in terms of phpbrew:

- Like `phpbrew`, you can temporarily choose an alternative PHP binary
  by simply referring to its path. With `phpbrew`, this would be something like
  `~/.phpbrew/php/php-{PHP_VERSION}/bin/php`. With the ondrej PPA, it becomes
  `/usr/bin/php{PHP_MINOR_VERSION}`. (E.g. `~/.phpbrew/php/php-7.2.36/bin/php`
  would become just `/usr/bin/php7.2`.)

- There is no equivalent to `phpbrew use`. That feature would change the symlink
  only for the duration of the current terminal session. Opening a new terminal
  session would revert to the previous selection. With `update-alternatives`,
  it's all or nothing. I mainly used `phpbrew use` to ensure my default PHP did
  not change in case I forgot to call it again.

- Usage of `update-alternatives` is more like `phpbrew switch`, as it affects
  both the current and all later terminal sessions. Once switched, that selection
  is in use until you switch it again. This means I have to remember to switch
  to my default version. However, it's relatively easy to add a line to my shell
  profile to call `update-my-alternatives --auto php`.

Basically, if you can use the binary directly, but don't want to use that one as
your default, refer to it by absolute path. If you are using a command that will
use the current environment's PHP, use `update-my-alternatives` to switch PHP
versions first.

The other issue I see is that if I want to test against a _specific_ PHP
version, I'll still need to compile it myself &mdash; which leads me back to the
original problem that led me here in the first place. I'll cross that bridge
when I get to it. Until then, I have a workable solution &mdash; and finally a
single document I can refer to when I need to remember again at a later date,
instead of cobbling it together from multiple sources!
