---
id: 2015-12-14-secure-phar-automation
author: matthew
title: 'Secure PHAR Automation'
draft: false
public: true
created: '2015-12-14T11:45:00-05:00'
updated: '2015-12-15T08:20:00-05:00'
tags:
    - composer
    - php
    - programming
    - security
---
For a variety of reasons, I've been working on a utility that is best
distributed via [PHAR](http://php.net/phar) file. As has been [noted by
others](https://archive.is/hDwaA) (archive.is link, due to lack of availability
of original site), PHAR distribution, while useful, is not without security
concerns, and I decided to investigate how to securely create, distribute, and
update PHAR utilities as part of this exercise.

This is an account of my journey, as well as concrete steps you can take to
secure your own PHAR downloads.

<!--- EXTENDED -->

## The Roadmap

The steps outlined by Pádraic Brady in the afore-linked post were essentially:

- Distribute the PHAR over TLS-secured HTTPS.
- Sign your PHAR with a private key.
- Manage self updates securely (i.e., the updates must be over TLS, and updated
  PHAR files should be signed using the same private key).

As such, I figured the plan should be:

- Use [GitHub Pages](https://pages.github.com) for distribution. This gives me
  essentially free hosting, and free TLS.
- Create an OpenSSL key, use it to sign the package, and provide the public key
  for download.
- Have functionality built-in to the PHAR for updating and rolling back.
- Automate creation of the PHAR, as well as pushing it and the version
  information to the site.

Seems simple enough, right?

It would have been, had I been able to find examples of each of the steps. In
the end, I spent an afternoon testing different strategies, and finally came up
with what follows.

## Create an OpenSSL Key

The first step is to create an OpenSSL private key. This will be used to sign
the packages.

```bash
$ openssl genrsa -des3 -out phar-private.pem 4096
```

The above will prompt you for a passphrase, which is used to encrypt the key.

For purposes of automation, however, you may not (in fact, will not, as you'll
see later) be able to enter the passphrase. As such, you'll need to strip it.

```bash
$ cp phar-private.pem phar-private.pem.passphrase-protected
$ openssl rsa -in phar-private.pem -out phar-private-nopassphrase.pem
$ cp phar-private-nopassphrase.pem phar-private.pem
```

The second step will prompt you for the passphrase. The resultant key will have
it stripped. You can keep the passphrase-protected version if you wish; however,
it's functionally equivalent to the new key. If you keep it, place it somewhere
safe.

From here, create a `.travis/` subdirectory in your project, put the private key in 
it, and then add that file to your project `.gitignore`:

```bash
$ mkdir .travis
$ mv phar-private.pem .travis/
$ echo ".travis/phar-private.pem" >> .gitignore
```

We're telling git to ignore the key, as we don't want to push it unencrypted to
our repository!

## Use Box to create the PHAR

Now that we have a key, we can think about creating our PHAR file.

While PHP provides a [ton of functionality around PHARs](http://php.net/phar),
the problem is that the manual is not terribly detailed, and this particular
section has often gone out-of-date. On top of that, to build even a relatively
simple PHAR that has an executable stub takes a ton of knowledge.

So, let others do the work for you. Specifically, the [Box Project](https://box-project.github.io/box2/).
The team behind the Box Project has done the hard work for you; all you need
to do is create a configuration file that details what files are used, what
compression to use, what signing mechanism to use (if any) and where the key is
located, etc.

The link above details retrieving box, but the basics are:

```bash
$ curl -LSs https://box-project.github.io/box2/installer.php | php
```

which will leave an executable `box.phar` in the working directory. I usually
put this in my `$PATH` and alias `box` to it.

Box, like many other PHP utilities, allows you to create either a configuration
file, or a "dist" configuration file. I like to do the latter, as it then lets
me copy it locally to provide modifications/customizations. Here's a basic
`box.json.dist` file containing the options for creating an OpenSSL-signed,
gzipped, executable package:

```javascript
{
  "algorithm": "OPENSSL",
  "chmod": "0755",
  "compression": "GZ",
  "directories": [
    "src"
  ],
  "files": [
    "LICENSE.md"
  ],
  "finder": [
    {
      "name": "*.php",
      "exclude": [
        "tests",
        "test"
      ],
      "in": "vendor"
    }
  ],
  "git-version": "package_version",
  "intercept": true,
  "key": ".travis/phar-private.pem",
  "main": "bin/command.php",
  "output": "command.phar",
  "stub": true
}
```

Some notes on the various options:

- "algorithm" and "key" go hand-in-hand. The algorithm indicates what package
  signing algorithm to use, and the "key" is the path on the filesystem to the
  key; relative paths are relative to the `box.json.dist` file.
- "compression" indicates the compression algorithm to use; in this case, I used
  gzip.
- "git-version" is a string to look for in files; technically, it looks for
  `@package_version@`, and not just the string. When discovered, it replaces
  that string with the sha1 of the most recent commit.
- "directories" is used to specify directories where all files should be
  included; "files" is used to specify individual files to include. Usually I
  use "files" for anything not in "directories", like the LICENSE file.
- "finder" can be used in a similar fashion to "directories" and "files", but
  gives you the ability to provide filtering rules; it uses the [Symfony Finder
  component](http://symfony.com/doc/current/components/finder.html), which means
  that the rules you build will follow that component's configuration syntax. In
  the example above, I'm telling it I want to include the `vendor/` directory,
  but to only include PHP files, and to exclude files in the directories "test"
  or "tests". Doing so greatly reduces the size of the generated PHAR.
- "main" is the name of the file containing the script to execute when the PHAR
  is invoked. Obviously, the name will vary based on your application.
- "stub", when `true`, indicates thatthe default stub based on the "main" script
  should be used; you can specify another stub file as well, if desired. I've
  used Box several times, and found that this needs to be boolean `true` when
  I'm having it encapsulate a command-line script.
- "output" is the name of the file to generate.
- "chmod" indicates the file mode mask to set on the generated file.

Obviously, configure this according to your needs, and add or remove
configuration as befits your project. The [schema
file](https://github.com/box-project/box2/blob/2.0/res/schema.json) is your
friend when determining what should be included; much of the information is also
available via `box.phar help build`.

Once you have created the file, you can attempt a build:

```bash
$ box build -vv
```

(`-vv` indicates "verbosity"; I do this so I can see errors if they occur.)

> ### Make the build leaner!
> 
> The command may take a while. In fact, if you have a lot of dependencies, it
> *will* take a while. I found it was best to strip any development-only
> dependencies prior to running a build:
> 
> ```bash
> $ composer install --no-dev
> ```
> 
> I find this results in a far faster build, with a much smaller file size.

When the build is successful, you'll end up with two files:

- `command.phar`
- `command.phar.pubkey`

The name will be based on what you specified for "output" in the configuration.
The second file is the OpenSSL public key derived from your private key. Users
will need to have *both* files available, as PHP's PHAR functionality will
verify the archive against the public key on every invocation. This is what
ensures a secure distribution!

Now that you have the files, add them to your `.gitignore`:

```bash
$ echo "command.phar" >> .gitignore
$ echo "command.phar.pubkey" >> .gitignore
```

We don't want the PHAR in the master branch; this is the branch used to create
the PHAR itself. By excluding it, we can do some automation later.

## Generate a version file

Now that we have the PHAR, we need to provide a way of indicating what *version*
we have. The simplest way to do that is to take its sha1sum and write it to a
file:

```bash
$ sha1sum command.phar > command.phar.version
```

We'll use this later to automate self-updates. Just like the PHAR file itself
and its public key, we'll exclude it from the branch:

```bash
$ echo "command.phar.version" >> .gitignore
```

## Create the gh-pages branch

At this stage, we have a PHAR file that is signed with a private OpenSSL key, a
public OpenSSL key for verifying the signature, and a version file we can use
later for triggering updates. It's time to publish those.

As noted above, we want to publish to a TLS-enabled site. GitHub provides that
infrastructure for us via GitHub Pages. These are available via any public
repository that publishes a `gh-pages` branch. The obvious conclusion is: let's
add that branch to our current repository!

That said, the new branch really shouldn't have the build tools and code.

First, let's make sure you've committed everything you need on the master
branch:

```bash
$ git add .gitignore box.json.dist
$ git commit -m 'Build tools for PHAR file'
```

Now we can create what's called an "orphan" branch &mdash; a branch with no
history and no parents:

```bash
$ git checkout --orphan gh-pages
$ git rm -rf .
```

We perform the `git rm -rf .` command in order to remove any files previously
committed. One nice side effect is that untracked files &mdash; such as our
generated PHAR, public key, and version file &mdash; are untouched by this
operation!

So, let's add them:

```bash
$ git add composer.phar*
```

It would be good to create a landing page as well, with links for downloading
the PHAR file and the public key (don't worry about the version file for now).
Create an `index.html` file in the root of the project, and add that as well, as
well as any CSS or JavaScript files you need for it. I personally used
[Bootstrap](https://getbootstrap.com) from a CDN for this, which gives a
reasonable default look for the package.

Once all your files are added, execute:

```bash
$ git commit -m 'Initial gh-pages files'
```

and then push to your GitHub repository.

Within a minute or so, you should be able to browse to `https://<your username
or org>.github.io/<your repo>/`.

## Write self-update/rollback commands

Now that we have a process for creating a PHAR, how will users update or
rollback?

Fortunately, Pádraic has a solution for that as well: [PHAR
Updater](https://github.com/padraic/phar-updater). This handy library provides
functionality for replacing the PHAR, and has built-in support to verify the
signature of the replacement with the public key. For it to work, the PHAR,
public key, and version file must all be accessible over TLS/SSL.

First, add the utility to your package:

```bash
$ composer require padraic/phar-updater
```

Regardless of how you write your console commands in PHP, your self-update
command will execute something like the following:

```php
use Humbug\SelfUpdate\Updater;

$updater = new Updater();
$updater->getStrategy()->setPharUrl($urlToGithubPagesPharFile);
$updater->getStrategy()->setVersionUrl($urlToGithubPagesVersionFile);
try {
    $result = $updater->update();
    if (! $result) {
        // No update needed!
        exit 0;
    }
    $new = $updater->getNewVersion();
    $old = $updater->getOldVersion();
    printf('Updated from %s to %s', $old, $new);
    exit 0;
} catch (\Exception $e) {
    // Report an error!
    exit 1;
}
```

It's really that simple! The defaults assume an OpenSSL-signed PHAR file, and
that the public key is present locally in a file named `<name of phar>.pubkey`.
If the version is different, it updates; if not, it doesn't. Exceptions
typically occur for things like:

- File permissions.
- Inability to reach the remote PHAR or version files.
- Inability to validate the downloaded PHAR.
- Inability to perform TLS negotation.

Regarding this latter, padraic/phar-updater includes
padraic/humbug_get_contents, which is supposed to iron out TLS issues on PHP
versions &lt; 5.6. I found in practice, however, that when performing the update,
if I didn't use a PHP 5.6 version, it consistently failed, indicating TLS
negotiation issues. Supposedly you can fix these by downloading
http://curl.haxx.se/ca/cacert.pem and setting
`openssl.cafile=/path/to/cacert.pem` in your `php.ini` file.

So, self-update is taken care of; what about rollback?

When a self-update is performed using padraic/phar-updater, it writes the
original PHAR to `command-old.phar` in the same directory as the original PHAR
file. If that file is available, you can write a rollback routine like the
following:

```php
use Humbug\SelfUpdate\Updater;

$updater = new Updater();
try {
    $result = $updater->rollback();
    if (! $result) {
        // report failure!
        exit 1;
    }
    exit 0;
} catch (\Exception $e) {
    // Report an error!
    exit 1;
}
```

Again, quite easy!

Checkout your project's `master` branch, add the above commands, and re-build
your PHAR&hellip;

Wait, shouldn't we automate that last step? It'd be really nice if we could push
to master, and have the new PHAR show up automatically on our gh-pages branch!

## Enable Travis-CI for the repository

How do we automate? Via continuous integration and deployment, of course!

For this step, I'm choosing [Travis-CI](https://travis-ci.org). It's free for
open source projects, but also has a paid, private tier if you need it. Its
dockerized builds trigger typically within seconds of pushing your code, and the
environment is built and your tests run in often under 30 seconds. It's a great
choice for this.

As a CI service, it provides a number of stages that trigger, often
conditionally. We're going to use one of these, `after_success`, to build the
PHAR, update the version file, and push them to our gh-pages branch.

First, though, we need to enable Travis-CI for our repository. You will need to
do the following:

- Register for a Travis-CI account if you haven't already. The homepage will
  guide you there.
- Once you have an account and have logged in, go to your profile page (clicking
  your icon in the top right takes you there).
- Find your repository in the list, and toggle the switch to enable it. (You may
  need to sync your repositories if your project is new within the last day;
  there's typically a "Sync" button next to where your name appears above the
  repository list.)

From here, you should add a `.travis.yml` file to your project, if you haven't
already. Here's a template:

```yaml
sudo: false
language: php

cache:
  directories:
  - $HOME/.composer/cache
  - vendor

matrix:
  fast_finish: true
  include:
  - php: 5.5
  - php: 5.6
    env:
    - EXECUTE_DEPLOYMENT=true
  - php: 7
  - php: hhvm
  allow_failures:
  - php: hhvm

before_install:
- phpenv config-rm xdebug.ini
- composer self-update

install:
- travis_retry composer install --no-interaction
- composer info -i

script:
- ./vendor/bin/phpunit # If you have tests

notifications:
  email: true
```

Commit and push that file, and you should see your first build appear on
Travis-CI.

## Create an SSH deploy key

If we want Travis-CI to push to our gh-pages branch, we'll need to provide it
with a deployment key. 

First, create a new SSH key:

```bash
$ ssh-keygen -t rsa -b 4096 -C "<your email address>"
```

This will prompt you for where you want to put the new key files, and what they
should be named; I use descriptive names in these cases, such as the repository
name, and the selected encryption type: "component_installer_rsa" . Usually
`$HOME/.ssh/` is a good location to store them.

Next, we'll provide the public key to GitHub.  Open the public key generated
(usually `<key name>.pub` in a visual editor with clipboard support, and copy
the entire file. Then go to `https://github.com/<your username or org>/<your
repo>/settings/keys`. On that page, click the button `Add deploy key`, give your
key a name (I used `<repo name> for Travis-CI`), and paste in the key where
indicated. Finally, click the box enabling write permissions; we want to be able
to push commits with this key! Confirm and save it.

Finally, we need to copy the *private* key into the project. Don't worry; we're
not going to commit it yet; in fact, we're going to tell git to omit it:

```bash
$ cp $HOME/.ssh/<repo>_rsa .travis/build-key.pem
$ echo ".travis/build-key.pem" >> .gitignore
```

At this point, we now have two files in `.travis/`, neither of which git will
commit to the repository: `phar-private.pem` and `build-key.pem`. And, somehow,
Travis-CI needs to get access to them.

## Archive and encrypt the secrets

Travis-CI provides a number of facilities for encrypting secrets that you wish
to utilize during the build process. In our case, we need to provide [encrypted
files](https://docs.travis-ci.com/user/encrypting-files).

Interestingly, due to some issues with OpenSSL and the way the support is
implemented in Travis-CI, you can [only encrypt a *single*
file](https://github.com/travis-ci/travis.rb/issues/239). Thus, if you
have multiple files, you need [create an archive of them and encrypt
that](https://docs.travis-ci.com/user/encrypting-files#Encrypting-multiple-files).

```bash
$ cd .travis
$ tar cvf secrets.tar *.pem
$ cd ..
```

This will create the file `.travis/secrets.tar`.

Now, we need to encrypt the file. To do this, you will need to install the
`travis` gem:

```bash
$ gem install travis
```

and then login:

```bash
$ travis login
```

Once you've done that, you can encrypt the `secrets.tar` file:

```bash
$ travis encrypt-file .travis/secrets.tar .travis/secrets.tar.enc --add
```

This will create a new file, `.travis/secrets.tar.enc`, and add an entry to your
`.travis.yml`'s `before_install` section that will decrypt the file; this means
that your code and scripts on Travis-CI can then rely on `.travis/secrets.tar`
being available.

> ### Note for the Type-A personalities out there
>
> When you use the `--add` flag and `travis` rewrites your `.travis.yml` file,
> it strips out any whitespace you've added.

We'll add the `.travis/secrets.tar.enc` file to the repository, and omit
`.travis/secrets.tar`:

```bash
$ git add .travis/secrets.tar.enc
$ echo ".travis/secrets.tar" >> .gitignore
$ git add .gitignore
```

When a build is triggered on Travis-CI now, it will decrypt this file before any
of our build processes are triggered, allowing us access to those secrets!

## Write a deployment script

Now that we have our secrets securely available on Travis-CI, we can figure out
what deployment might look like:

- We'll want to remove development-only dependencies.
- We'll want to extract the secrets from the tarball.
- We'll want to start the SSH agent with our deployment key.
- We'll want to setup our Git identity. (In my experiments, I discovered that
  GitHub rejected pushes from valid deployment keys that did not include a
  full name and email.)
- We'll need to add a git remote using the SSH-enabled repository path.
- We'll want to fetch the Box Project PHAR file.
- We'll want to create the PHAR using Box.
- We'll need to generate a new version file from the re-generated PHAR.
- We'll need to check out the gh-pages branch, and add the PHAR and version
  file.
- We'll need to push the changes to GitHub.

I do all but the first step in a script, which I put in `bin/deploy.sh`:

```bash
#!/bin/bash
# Unpack secrets; -C ensures they unpack *in* the .travis directory
tar xvf .travis/secrets.tar -C .travis

# Setup SSH agent:
eval "$(ssh-agent -s)" #start the ssh agent
chmod 600 .travis/build-key.pem
ssh-add .travis/build-key.pem

# Setup git defaults:
git config --global user.email "<your email here>"
git config --global user.name "<your name here>"

# Add SSH-based remote to GitHub repo:
git remote add deploy git@github.com:weierophinney/component-installer.git
git fetch deploy

# Get box and build PHAR
curl -LSs https://box-project.github.io/box2/installer.php | php
php box.phar build -vv
# Without the following step, we cannot checkout the gh-pages branch due to
# file conflicts:
mv component-installer.phar component-installer.phar.tmp

# Checkout gh-pages and add PHAR file and version:
git checkout -b gh-pages deploy/gh-pages
mv component-installer.phar.tmp component-installer.phar
sha1sum component-installer.phar > component-installer.phar.version
git add component-installer.phar component-installer.phar.version

# Commit and push:
git commit -m 'Rebuilt phar'
git push deploy gh-pages:gh-pages
```

Make the deployment script executable, add it to your repository, and commit!

## Add the script to travis

Now we need to tell Travis-CI to execute this script. We want it to run:

- Only for one build environment. (No need to push multiple times for the same
  commit!)
- Only if the build is successful.
- Only for builds on the master branch.
- Only if the build is not for a pull request.

If you used the `.travis.yml` file I provided earlier as a template, you likely
noted the section where I define the env variable `$EXECUTE_DEPLOYMENT`. This is
what enforces the first point (only run for one build environment).

For the second point, we're going to define an `after_success` section in the
configuration; this ensures it does not trigger if our other tasks fail (such as
unit tests, CS checks, etc.).

For the third and fourth points, Travis-CI provides some environment variables
to help us:

- `$TRAVIS_BRANCH` indicates the branch. However, in the case of a pull request,
  this will be the base branch against which the pull request was made. As such,
  we also need:
- `$TRAVIS_PULL_REQUEST`. The value of this is the pull request ID when present;
  otherwise, it's the string "false".

Putting it all together results in the following additions to the `.travis.yml`
file:

```yaml
after_success:
- if [[ $EXECUTE_DEPLOYMENT == 'true' && $TRAVIS_BRANCH == 'master' && $TRAVIS_PULL_REQUEST == 'false' ]]; then composer install --no-dev ; fi
- if [[ $EXECUTE_DEPLOYMENT == 'true' && $TRAVIS_BRANCH == 'master' && $TRAVIS_PULL_REQUEST == 'false' ]]; then ./bin/deploy.sh ; fi
```

Each line only executes if we are on the designated environment (we chose 5.6
for this example), on the "master" branch, for non-pull-request pushes. The
first line removes the development dependencies from the tree, and the second
executes our deployment script.

> ### Note on after_success vs deploy
>
> Travis-CI has another event, `deploy`, which is often touted as the
> appropriate place to perform, well, deployments, which is essentially what
> we're doing in the above.
>
> What I found, however, is that the workflow didn't work well when you have
> cached or encrypted files.
>
> The deploy event, when triggered, stashes changes, does a clean checkout, and
> then tries to restore from the stash. What I observed was that my cached
> composer files (the `composer.lock` file and `vendor/` directory) created
> conflicts when applying the stash, which caused my deployment script to never
> trigger. Another time I observed that the decrypted version of my secrets
> disappeared with the new checkout.
>
> If any readers have any feedback on this, I'd love to hear it!

## Push and watch it work

Hopefully, if you've been following along this far, you'll see that on your
next push with a successful build, your gh-pages branch will get a new commit,
with an updated PHAR and version file!

The workflow for consumers will then be:

- Go to your gh-pages site and download the PHAR file and public key.
- Periodically execute `<name of phar file>.phar self-update` to update their
  installation (assuming you named the self-update command "self-update").
- If desired, they can later rollback to a previous version using `<name of phar
  file>.phar rolback` (assuming you named the rollback command "rollback").

All of this can be done securely, because you've setup a secure workflow:

- The PHAR file, public key, and version file are all secured via TLS.
- The PHAR file is signed using an OpenSSL private key, and can be verified
  using its public complement.

In your own workflow, you're only pushing *encrypted* secrets to the repository,
and the keys for those are known only to you and Travis-CI. (In fact, you only
"know" them through the `travis` gem!) If your deployment key is compromised,
you can revoke it from GitHub. If you feel the signing key has been compromised,
you can create a new one, and notify your users that they need to re-download
the PHAR and public key.

## Still under research

While the above workflow is tested and works, I have one item I'm still unhappy
with: I'd really like it if the deployment could be delayed until I know *all*
environments have completed successfully. If anybody could assist me with that,
I'd love to hear from you!

## Updates

Below is a list of updates made to this post since the time of writing:

- 2015-12-15: Changed references to `composer update` to read `composer
  install`, per a comment from Christophe Coevoet.
- 2015-12-15: Changed OpenSSL key generation example to use 4096 bits instead of
  2048, per a comment from sf_tristanb.
