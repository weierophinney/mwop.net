---
id: 2016-01-29-automating-gh-pages
author: matthew
title: 'Automating GitHub Pages Builds with MkDocs'
draft: false
public: true
created: '2016-01-29T10:00:00-05:00'
updated: '2016-01-29T10:00:00-05:00'
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

With the requirements in place, we could start work on the solution.

### GitHub credentials

- Detail encrypting github token

### Environment variables

- env variables can be results of computation
- set env variables for build in matrix that will deploy docs

### When to build?

We know that Travis-CI has a number of events it triggers as part of a typical
build workflow:

- `before_install`
- `install`
- `script`
- `after_script`
- `deploy` (and `before_deploy` and `after_deploy`)

The `deploy` event may seem like the correct one, but it requires a very
specific workflow, which we wouldn't be using. But it turns out there's another
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
combined, but I hope one day that caching will happen at the end of the build
instead, so we can put them both under `after_success`.

### How to install?

### How to build?

## Putting it all together

