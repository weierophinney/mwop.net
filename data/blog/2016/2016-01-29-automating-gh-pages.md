---
id: 2016-01-29-automating-gh-pages
author: matthew
title: 'Automating GitHub Pages Builds with MkDocs'
draft: false
public: true
created: '2016-01-29T11:55:00-05:00'
updated: '2016-01-29T11:55:00-05:00'
tags:
    - automation
    - github
    - mkdocs
    - php
    - programming
---
One of the final tasks in prepping for the [Expressive 1.0 release](/blog/2016-01-28-expressive-stable.html)
was setting up the documentation site. We'd decided to use GitHub Pages for
this, and we wanted to automate builds so that as we push to the master branch,
documentation is deployed.

The process turned out both simple and bewilderingly difficult. This post is
intended to help others in the same situation.

<!--- EXTENDED -->

## Requirements

In looking at the problem, we realized we had a number of requirements we
needed to consider for any solution we developed.

### Technologies

First, we chose [MkDocs](http://www.mkdocs.org) for our documentation. MkDocs
uses plain old Markdown, which we're already quite comfortable with due to being
on GitHub, StackOverflow, Slack, and so many other services that use it.
With MkDocs, you create a file, `mkdocs.yml`, in which you specify the table of
contents, linking titles to the documents themselves. Once you run it, it
generates static HTML files.

MkDocs allows you to specify a template, and ships with several of its own; the
most well-known is the one used on [ReadTheDocs](http://rtfd.org). One reason we
chose MkDocs is because it has a good-sized ecosystem, which means quite a few
themes to choose from; this gave us a tremendous boost in rolling out something
that both looked good and was usable.

This meant, however, that we had the following dependencies in order to build
our documentation:

- MkDocs itself.
- One or more python extensions; in particular, we chose an extension that
  fixes issues with how the default Markdown renderer renders fenced code
  blocks that are part of bullet points or blockquotes.
- The custom theme we were developing.

As such, this meant our build automation was going to require grabbing these
items, ideally caching them between builds.

### Build only when necessary

The other aspect is that there's no reason to build the documentation for every
build we do on the CI server. We only want to build:

- on the master branch,
- when it's not a pull request,
- if the build is a success,
- and only once per build.

On any given build, we're actually running at least four jobs, one each for PHP
5.5, 5.6, 7, and HHVM. We don't want to build and deploy the documentation for
each!

### Reusability

While we were doing this initially for Expressive, we also want to do the same
for each of the ZF components. So any solution we built needed to be reusable
with minimum fuss. If we have an update to the theme, we don't want to have to
update each and every one of the component repositories! Similarly, if there are
any changes to the deployment script, we don't want to have to roll it out to
all the repositories.

### Pushing to gh-pages

Finally, any build automation we did would be required to push to the gh-pages
branch of the repository on successful build. This would require having a token
or user credentials on the CI server.

## Creating the automation

With the requirements in place, we could start work on the solution. Since we
already use [Travis-CI](https://travis-ci.org) for our builds, we decided to
re-use it for building documentation. Of course, the challenge then was creating
appropriate configuration to meet our requirements.

### GitHub credentials

In order to push from Travis, we need to have adequate credentials. There are a
couple of ways to do this:

- Use a [personal access token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/).
- Supply your private SSH key.

In both cases, you need to add information to your Travis environment.
The problem, however, is that if anybody has access to these values, they can
essentially commit using your credentials &mdash; which you *definitely* do not
want to have happen! As such, you need to encrypt the value so that only Travis
knows about it.

I covered encrypting your SSH key in my blog post [on secure PHAR automation](/blog/2015-12-14-secure-phar-automation.html),
and, in that particular case, I had several files needing encryption, which led
to a fairly complex setup. If you have no other secrets to encrypt, go with the
personal access token. For one, it simplifies security; if you find the token
has been compromised, you can simply delete it from GitHub, without needing to
go to the extra work of creating a new SSH key and propagating it. It also
simplifies setup, as you can encrypt a single value, and simply configure it.

To encrypt the token, use the [Travis CLI tool](https://github.com/travis-ci/travis.rb#readme),
and then paste the value into your `.travis.yml`. In the following, I assign it
to the env variable `GH_TOKEN`, which is a common convention:

```bash
$ travis encrypt -r <org>/<repo> GH_TOKEN=<token value>
```

Obviously, substitute your organization and repository names, as well as your
token. This will output something like this:

```
Please add the following to your .travis.yml file:

  secure: "......="

Pro Tip: You can add it automatically by running with --add.
```

Note: I never use the `--add` switch, as the `travis` utility changes all the
whitespacing in the file.

Copy and paste the value into the `env.global` section of your `.travis.yml`
(creating it if you haven't already):

```yaml
env:
  global:
    - secure: "..."
```

Travis will automatically decrypt the value and export it to your environment.
In its logs, you'll see `GH_TOKEN=secure`.

### When to build?

We know that Travis-CI has a number of events it triggers as part of a typical
build workflow:

- `before_install`
- `install`
- `script`
- `after_script`
- `deploy` (and `before_deploy` and `after_deploy`)

The `deploy` event may seem like the correct one, but it requires a very
specific workflow, which we won't be using. But it turns out there's another
event you can use: `after_success`! It runs after `script`, and before either
`deploy` or `after_script` are triggered.

That said, I discovered something problematic: Travis' caching happens
immediately following `script`, and before `after_success`, `deploy`, or
`after_script`. This meant that any assets we installed as part of documentation
deployment &mdash; MkDocs, the theme, etc. &mdash; would not be cached.

So I reached out to Travis' support team, and they told me about another cool
trick: the `$TRAVIS_TEST_RESULT` variable indicates the current exit status from
the `script` section; we could test this on the last line of the `script`
section to conditionally install assets!

As a result, we ended up with a line under `script` to install the assets, and
another under `after_success` to perform the actual build. They could likely be
combined, but I chose not to: I don't want the results of building the
documentation to result in a failed build. I hope one day that caching will
happen at the end of the build instead, so we can put them both under
`after_success`.

### Environment variables

This leads to environment variables. In order to determine if a documentation
build is necessary, we can use an environment variable that is only set for the
environment in which we want to build. Since most projects I do are PHP, we had
to choose which build in the matrix to use. Our projects test on PHP 5.5, 5.6,
7.0, and HHVM. Since most of our users are on PHP 5 versions, we decided to do
documentation builds on the latest stable 5 build: 5.6.

We also only want to build if we're on the master branch, and *not* as part of a
pull request; the branch reports as master if a pull request was issued against
that branch, which is why the criteria is so specific.

Finally, I know that, eventually, I'll have MkDocs installed. Due to the fact
that we're using Docker builds on Travis, I also know that this means I'll be
installing MkDocs using `pip install --user` versus via apt-get, since we don't
have root access. This means that MkDocs will be in `$HOME/.local/bin`, so I'll
need to update my `$PATH` for the environment in which I build.

Fortunately, you can do env variable declarations that are the product of
calculations in your `.travis.yml`. This meant that I ended up with the
following build matrix:

```yaml
matrix:
  fast_finish: true
  include:
    - php: 5.5
      env:
        - EXECUTE_CS_CHECK=true
    - php: 5.6
      env:
        - EXECUTE_TEST_COVERALLS=true
        - DEPLOY_DOCS="$(if [[ $TRAVIS_BRANCH == 'master' && $TRAVIS_PULL_REQUEST == 'false' ]]; then echo -n 'true' ; else echo -n 'false' ; fi)"
        - PATH="$HOME/.local/bin:$PATH"
    - php: 7
    - php: hhvm
  allow_failures:
    - php: hhvm
```

This creates a new `$DEPLOY_DOCS` environment variable with values of either
"true" or "false", which I can then test later. My `$PATH` is also updated.

The above are build-specific variables. However, I also needed a few variables
that could be accessed by scipts:

- I want to be able to provide the base site URL to my mkdocs configuration. We
  don't include this in the `mkdocs.yml` by default, so that you can build
  locally. For our production pages, though, we need it to ensure the search
  functionality works correctly, as we'll be in a sub-path.
- In order to commit via git, git requires the user's name and email. My
  experience has also been that these need to match the user who generated the
  personal access token.
- Because we want the functionality re-usable, I'll also need to provide the
  location of the git repository.

As such, I made the following additions to my `env.global` section:

```yaml
env:
  global:
    - SITE_URL: https://organization.github.io/repository
    - GH_USER_NAME: "My Full Name"
    - GH_USER_EMAIL: me@domain.tld
    - GH_REF: github.com/organization/repository.git
    - secure: "..."
```

`GH_REF` is the reference to the github repository being used. You'll note the
lack of a scheme to the URL; this is because the script for pushing the commits
to the gh-pages branch will create the full URL using the `GH_TOKEN`:

```bash
git remote add upstream https://${GH_TOKEN}@${GH_REF}
```

Now that the environment is all setup, we can approach installation of the
theme, and building the docs.

### How to install?

In order to build the docs with our custom theme, we need the custom theme
locally. Additionally, we likely want to download the theme only when there are
changes; we should cache it between requests. Additionally, if assets are not
cached, we should *not* download them unless the build has been successful.

This, frankly, was one of the harder parts to figure out, and I ended up needing
some pointers from the support team at Travis to figure it out. (Thanks for the
great pointers, you fine folks at Travis!)

As noted earlier, caching occurs immediately following execution of the `script`
section. This rules out the `after_success` script for this task, as any assets
downloaded then will never be cached. But how do we know when the build is
successful?

As noted earlier, the environment variable `TRAVIS_TEST_RESULT` holds the exit
value for the build. If any part of the script returns a non-zero value, then
the value will be non-zero from that point forward. As such, if we place a
script at the end of the `script` section that tests this value, we can
conditionally trigger an action!

I chose to create a script in our theme repository that has all the logic for
our documentation toolchain installation. This allows us to modify the
installation script as needed, without needing to update the various components
that will be adding the automation. The script currently looks something like
this:

```bash
#!/usr/bin/env bash
pip install --user mkdocs
pip install --user pymdown-extensions
if [[ ! -d zf-mkdoc-theme/theme ]];then
    mkdir -p zf-mkdoc-theme ;
    curl -s -L https://github.com/zendframework/zf-mkdoc-theme/releases/latest | egrep -o '/zendframework/zf-mkdoc-theme/archive/[0-9]*\.[0-9]*\.[0-9]*\.tar\.gz' | head -n1 | wget -O zf-mkdoc-theme.tgz --base=https://github.com/ -i - ;
    (
        cd zf-mkdoc-theme ;
        tar xzf ../zf-mkdoc-theme.tgz --strip-components=1 ;
    );
fi

exit 0
```

The above runs our `pip install` commands to install MkDocs and the extensions
we use, and, if the theme directory is missing, identifies and downloads the
latest tarball of the theme and extracts it.

> One gotcha I encountered: When you enable caching of a directory, Travis
> creates the directory even if no cache entries were found for it; as such, we
> need to test for a path *under* the directory.

Now, how do we get the installation script? With the following line in our
`script` section:

```yaml
script:
  - <build tasks>
  - if [[ $DEPLOY_DOCS == "true" && "$TRAVIS_TEST_RESULT" == "0" ]]; then wget -O theme-installer.sh "https://raw.githubusercontent.com/zendframework/zf-mkdoc-theme/master/theme-installer.sh" ; chmod 755 theme-installer.sh ; ./theme-installer.sh ; fi
```

The above grabs the script and executes it, but only if we're in the environment
designated for documentation deployment, and only if the build has been
successful to this point. This should *always* be the last line of the `script`
section.

### How to build?

Now that we know we have the build tools, what about building the documentation
itself?

For this, I wrote a deployment script, which we include in our theme repository.
We include it in the theme for *reusability* which was one of our requirements.
This ensures that as build and deployment change, we don't need to update all
the repositories that are building documentation; we can make the changes in the
theme repository, tag a new release, and on the next build, each will pick up
the changes.

The deployment script performs several tasks:

- It creates the build directory, and initializes it as a git repository with
  the upstream set to the repository's gh-pages branch, using the `GH_TOKEN`
  and `GH_HREF`.
- It sets the git configuration to use the configured GitHub user name and
  email.
- It runs the build (which is itself another script).
- It adds the changed files, commits them, and pushes them to the remote.

In the end, the deployment script looks like this:

```bash
#!/usr/bin/env bash
set -o errexit -o nounset

SCRIPT_PATH="$(cd "$(dirname "$0")" && pwd -P)"

# Get curent commit revision
rev=$(git rev-parse --short HEAD)

# Initialize gh-pages checkout
mkdir -p doc/html
(
    cd doc/html
    git init
    git config user.name "${GH_USER_NAME}"
    git config user.email "${GH_USER_EMAIL}"
    git remote add upstream "https://${GH_TOKEN}@${GH_REF}"
    git fetch upstream
    git reset upstream/gh-pages
)

# Build the documentation
${SCRIPT_PATH}/build.sh

# Commit and push the documentation to gh-pages
(
    cd doc/html
    touch .
    git add -A .
    git commit -m "Rebuild pages at ${rev}"
    git push -q upstream HEAD:gh-pages
)
```

> #### Notes on the script
>
> - We build our documentation in `doc/html/`, which is excluded from the
>   repository via `.gitignore`, allowing us to safely clone to that location.
> - `git add -A .` will remove any files previously tracked that are now
>   deleted, and add any new paths found. This makes automating far simpler,
>   as we don't need to worry about additions, removals, or renames.
> 
> Additionally, you'll note that the `git push` command includes the `-q`
> switch. This is **very** important: if you don't include it, the command
> output includes the push URL, which includes the GitHub token! Again, you
> don't want that value leaked, so take the steps to ensure it isn't!

The build script performs a few tasks, which might vary based on your own needs:

- It adds some configuration to the `mkdocs.yml`, including:
  - Setting the `site_url` value, based on our environment variable.
  - Adds configuration for several extensions. In particular, we don't use
    Pygments (instead, we opt for using [prism.js](http://prismjs.com)),
    and we use pymdownx.superfences, which corrects issues with fenced code
    blocks that are nested in lists or blockquotes.
  - Specifies the `theme_dir`.
- It runs `mkdocs build --clean`
- It runs a few utilities we've written for doing things like swapping out the
  landing page, and adding markup to make images responsive.

Regarding the `mkdocs.yml` changes, the reason we don't include these by default
is two-fold:

- It allows developers to run `mkdocs` locally without requiring that the theme
  or extensions be present.
- It allows us to preview documentation on [ReadTheDocs](http://rtfd.org); while
  the automation we're setting up largely obviates the need for that service,
  it's still useful for previewing documentation targeting the `develop` branch.

The build script looks like this:

```bash
#!/usr/bin/env bash
SCRIPT_PATH="$(cd "$(dirname "$0")" && pwd -P)"

# Update the mkdocs.yml
cp mkdocs.yml mkdocs.yml.orig
echo "site_url: ${SITE_URL}"
echo "markdown_extensions:" >> mkdocs.yml
echo "    - markdown.extensions.codehilite:" >> mkdocs.yml
echo "        use_pygments: False" >> mkdocs.yml
echo "    - pymdownx.superfences" >> mkdocs.yml
echo "theme_dir: zf-mkdoc-theme/theme" >> mkdocs.yml

mkdocs build --clean
mv mkdocs.yml.orig mkdocs.yml

# Make images responsive
echo "Making images responsive"
php ${SCRIPT_PATH}/img_responsive.php

# Replace landing page content
echo "Replacing landing page content"
php ${SCRIPT_PATH}/swap_index.php
```

You could combine the build and deploy scripts if desired. I did not, as it
allows me to clone the theme directory into my component checkout and build the
documentation as it will appear:

```bash
$ echo "zf-mkdoc-theme/" >> .git/info/exclude
$ git clone zendframework/zf-mkdoc-theme
$ ./zf-mkdoc-theme/build.sh
$ php -S 0:8000 -t doc/html/
```

Now that we have the scripts in place in our theme, we need to tell Travis to
execute them. We do that in an `after_success` script:

```yaml
after_success:
  - if [[ $DEPLOY_DOCS == "true" ]]; then echo "Preparing to build and deploy documentation" ; ./zf-mkdoc-theme/deploy.sh ; echo "Completed deploying documentation" ; fi
```

The above will only execute if the build is successful, which means we only need
to check if we're in the target environment. We'll assume that the documentation
build tools and theme are present, and simply execute the deployment script.

## Caching

One of the requirements is caching, and in the above, we've made some decisions
about when to execute certain tasks based on the assumption that we'll be
caching. How do we actually do that, though?

Travis allows caching assets via configuration. You can specify directories or
files, with entries being relative to the checkout unless they are fully
qualified paths. We want to cache:

- The results of installing MkDocs.
- The theme directory.

We'll add the following configuration:

```yaml
cache:
  directories:
    - $HOME/.local
    - zf-mkdoc-theme
```

> In the ZF components, we also cache the vendor directory and the global
> Composer cache, which helps speed up builds tremendously.

With that in place, we've now met all of our requirements!

## Putting it all together

In the end, the result was our new [zf-mkdoc-theme repository](https://github.com/zendframework/zf-mkdoc-theme).
It contains:

- The theme installer script invoked within our `script` section,
  `theme-installer.sh`.
- The various build scripts and utilities (`deploy.sh`, `build.sh`, etc.).
- The MkDocs theme (under the `theme/` subdirectory).

We can now consume this from any of our components, by ensuring the following
are in our `.travis.yml`:

```yaml
sudo: false

language: php

cache:
  directories:
    - $HOME/.local
    - zf-mkdoc-theme

env:
  global:
    - SITE_URL: https://organization.github.io/repository
    - GH_USER_NAME: "Name of Committer"
    - GH_USER_EMAIL: me@domain.tld
    - GH_REF: github.com/zendframework/zend-expressive.git
    - secure: "..."

matrix:
  fast_finish: true
  include:
    - php: 5.6
      env:
        - EXECUTE_TEST_COVERALLS=true
        - DEPLOY_DOCS="$(if [[ $TRAVIS_BRANCH == 'master' && $TRAVIS_PULL_REQUEST == 'false' ]]; then echo -n 'true' ; else echo -n 'false' ; fi)"
        - PATH="$HOME/.local/bin:$PATH"

script:
  - build something
  - if [[ $DEPLOY_DOCS == "true" && "$TRAVIS_TEST_RESULT" == "0" ]]; then wget -O theme-installer.sh "https://raw.githubusercontent.com/zendframework/zf-mkdoc-theme/master/theme-installer.sh" ; chmod 755 theme-installer.sh ; ./theme-installer.sh ; fi

after_success:
  - if [[ $DEPLOY_DOCS == "true" ]]; then echo "Preparing to build and deploy documentation" ; ./zf-mkdoc-theme/deploy.sh ; echo "Completed deploying documentation" ; fi
```

With the above in place, any pushes to the master branch that succeed on the PHP
5.6 job will then result in updating and deploying our documentation!

## Final Notes

This was a fun experiment, and I've been quite happy with [the results](https://zendframework.github.io/zend-expressive/).
I'm also looking forward to deploying this out to other components and libraries
I maintain or assist in, as I love the idea of having up-to-date documentation
with a style unique to the project.

The zf-mkdoc-theme referenced throughout this post is on github, and you can use
it as a guideline for your ow projects:

- [https://github.com/zendframework/zf-mkdoc-theme](https://github.com/zendframework/zf-mkdoc-theme)

I hope others are inspired to do the same, and find the tips in this post
useful!
