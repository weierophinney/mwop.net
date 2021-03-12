---
id: 2021-03-12-laminas-ci
author: matthew
title: 'Laminas CI Automation'
draft: false
public: true
created: '2021-03-12T10:14:00-05:00'
updated: '2021-03-12T10:14:00-05:00'
tags:
    - php
    - laminas
    - github
---

The Laminas Project has close to 200 repositories between the main project, Laminas API Tools, and Mezzio.
It's a lot to maintain, and keeping on top of incoming patches can be a gargantuan task, much less creating releases.

That's why this past year, we've spent a bunch of time on streamlining our processes; we want to be able to review, merge, and release changes quickly and confidently.
To that end, we have developed a number of [GitHub Actions](https://github.com/features/actions) to make these processes as easy as possible for our maintainers.

<!--- EXTENDED -->

### Automated Releases

The first was a brainchild of [Marco Pivetta (aka Ocramius)](https://github.com/Ocramius).
He wanted a way to make releases as simple as possible.

Before this, we had a rather convoluted process:

- If a pull request was against our "master" branch:
  - Merge to "master"
  - Merge to "develop" (which generally resulted in merge conflicts, due to differences in the `CHANGELOG.md` file between branches)
  - Create a branch off of "master" to set the release version
  - Bump the release version in the `CHANGELOG.md`
  - Merge the release branch into "master"
  - Merge the release branch into "develop" (again, merge conflicts)
  - Tag the release, copying the relevant `CHANGELOG.md` entry into the tag description
  - Push the release
  - Create a release on GitHub from the tag, and, again, copy the `CHANGELOG.md` entry into the description
- If a pull request was against our "develop" branch:
  - Merge to "develop"
  - Merge "develop" to "master"
  - Create a branch off of "master" to set the release version
  - Bump the release version in the `CHANGELOG.md`
  - Merge the release branch into "master"
  - Merge the release branch into "develop"
  - Bump the release version in the `CHANGELOG.md` file in the "develop" branch to the next minor version
  - Tag the release from "master", copying the relevant `CHANGELOG.md` entry into the tag description
  - Push the release
  - Create a release on GitHub from the tag, and, again, copy the `CHANGELOG.md` entry into the description

A lot of the work around tagging and creating the GitHub release are handled by my [keep-a-changelog tooling](https://phly.github.io/keep-a-changelog/), but it was still work, and there was a lot of boilerplate and busywork involved.

Marco's big idea: what if we assigned issues and pull requests to GitHub milestones, and, when the milestone was closed, the release was created automatically?

This led to the creation of our [automatic-releases GitHub action](https://github.com/laminas/automatic-releases).

To work with it, you need to create _release branches_ in your repository, named after semantic versions, and of the form `{MAJOR}.{MINOR}.x` (that's a literal ".x" at the end).
(This has a nice side benefit of removing the "master" verbiage from our branches as well.)
You then create milestones named for the next releases you want to create: `1.2.3`, `1.3.0`, `2.0.0`.
From there, you add a [small workflow](https://github.com/laminas/automatic-releases/tree/1.11.x/examples/.github/workflows) to your application, along with a few secrets (a GPG signing key, a Git author and email for tagging the release, and potentially a privileged GitHub token to allow creating a merge-up request; more on that later).

As you triage, assign your issues and pull requests to milestones.
When all issues and pull requests related to a milestone are complete, you close the milestone, and the workflow takes it from there.

What the workflow does:

- It pulls the milestone description.
- It pulls the list of issues and pull requests, along with the people who created them, to create a list of release notes detailing the issues/pull requests closed.
- If you have a `CHANGELOG.md` in the [Keep A Changelog format](https://keepachangelog.com), it will update the entry for the release to append the milestone description and release notes pulled in the previous steps, as well as set the release date, pushing the changes back to the branch.
- It creates a tag using the signing key and git author/email, setting the description to the the changelog entry, or the information from the first two steps, pushing the tag on completion.
- It creates a release on GitHub, using the same notes provided in the tag description.
- If a newer release branch exists (e.g., if you were release 1.2.3, and the 1.3.x branch exists), it creates a "merge-up" request to that branch, with the difference between the two branches.
- If no newer release branch exists, it creates a new minor release branch (e.g., if you are releasing 1.2.3, it would create the 1.3.x branch).
- It switches the default branch to the next release branch.
- If you have a `CHANGELOG.md` file, and just created a new release branch, it adds an entry for the next minor version to it.
- It creates milestones for the next patch, minor, and major releases, if they do not already exist.

Essentially, the action **automates the busy work so that maintainers can release easily, and release often**.

One thing we discovered along the way: that merge-up request was still causing issues, as there would be differences in the `CHANGELOG.md` file.
So we came up with a solution for that, because it was already supported in the workflow: we now keep information we used to keep in the `CHANGELOG.md` file _in the milestone description_, using the same format.
Since the milestone description gets pulled by the action and used in the tag and release notes, it has the same effect (putting the information semantically where it belongs), while removing the merge conflicts (as the file can be eliminated).

This new workflow has been hugely successful.
We're finding that we're spending time mostly on triage (determining whether or not we'll address it, and, if so, what branch a fix or feature should target), and more time providing feedback.
Once we are able to accept a patch, the time between merge and release is only as long as it takes to close the milestone and the action to run (which we've pared down to less than a minute!).

> #### Use anywhere!
>
> While this action uses PHP to accomplish its work, it can be used in **any** repository!
> We are even using it to create releases for our other GitHub Actions, which are primarily written in JavaScript and Bash.

### Continuous Integration

In order to merge a pull request, we need to ensure that it passes our QA checks, which generally include unit tests, coding standards verifications, and static analysis.
We have also wanted to grow our QA checks to include things like documentation linting and documentation link checks.

We have traditionally used Travis-CI for this, but starting late last year, Travis made some policy changes around OSS usage, and we found we were running out of hours mid-month.
As such, we needed a new solution, and we decided it was time to figure out what it was we really wanted from CI.

What we came up with:

- We absolutely loathe having to update the matrix every time there's a new PHP version to support.
- We also dislike having to update the matrix when we add a new QA tool; it's an easy step to forget.
- On top of that, running multiple types of checks on a given matrix item means we end up playing whack-a-mole: we fix one QA item, only to find the next check fails; we fix that only to find the next is failing; etc.
  We'd like them to run discretely, if possible, with a minimum of conditionals needed to set them up.
- We would really like as many jobs to run in parallel as possible, instead of being limited both at the repo and organization level (Travis limited us to 5 consecutive jobs across our entire organization at any given time, which could lead to a loooong queue at times.)
- We would like a minimum amount of configuration, with zero configuration being the goal when achievable.

To help figure this out, I worked with both Marco and [Cees-Jan Kiewiet (aka Wyrihaximus)](https:github.com/Wyriximus) to identify what possibilities existed, and what approaches might work.
[Luís Cobucci](https://github.com/lcobucci) also provided some useful information around creating and publishing Docker images to back GitHub Actions.

The result is two GitHub Actions:

- [laminas-ci-matrix-action](https://github.com/laminas/laminas-ci-matrix-action)
- [laminas-continuous-integration-action](https://github.com/laminas/laminas-continuous-integration-action)

These are then both referenced in a [single workflow with two steps](https://gist.github.com/weierophinney/9decd19f76b7d9745c6559074053fa65).

The first action, laminas-ci-matrix-action, analyzes your commit to discover known QA tools that might need to be run:

- PHPUnit (via existence of one or both of `phpunit.xml` or `phpunit.xml.dist`)
- phpcs (via existence of one or both of `phpcs.xml` or `phpcs.xml.dist`)
- Psalm (via existence of one or both of `psalm.xml` or `psalm.xml.dist`)
- yamllint and markdownlint (via existence of a `mkdocs.yml` and/or `docs?/book/**/*.md` files)

It also checks your `composer.json` to determine what PHP versions to run against, and what dependency set to install (which always includes lowest and latest supported, and, if a `composer.lock` is present, locked).
From there, it builds a matrix.

- One PHPUnit job per dependency set per PHP version.
- One job on the current "stable PHP" (currently 7.4) for each other QA check discovered.

You have the ability to provide extensions to install, `php.ini` values to use, QA checks to exclude, and additional checks to run via configuration.

When the action detects it is triggered by a pull request, it also performs a diff with the target branch, and only runs checks based on the files changed; for instance, if only documentation files were changed, it will only do markdown linting.

The action then creates a JSON-formatted "output" string of all the checks to run, which later steps or jobs can then use. Such as the laminas-continuous-integation-action!

The laminas-continuous-integration-action does the actual lifting. It accepts a JSON-formatted "job" that contains:

- The PHP version to use.
- Any additional extensions to install.
- Any `php.ini` settings to use.
- Which dependency set to use (lowest, locked, or latest)
- A command to run (the actual QA check)

When it runs, it checks out the specified repository reference, sets the default PHP version, installs any required extensions, adds `php.ini` configuration, and then runs Composer to install dependencies, based on the dependency set.
From there, it runs the command specified as a non-privileged user, thus performing the QA check.

The action allows you to provide pre/post run scripts in the repository as well, which can be handy for things like seeding databases or caches.

Between these two actions and the simplified workflow, we have been able to create a CI solution that will grow with our needs, without requiring periodic updates.
We have in fact expanded their capabilities a few times already, without requiring changes in the repositories that were already using them.
I've actually added new QA tooling to several repositories since adding the workflow, and had the pleasant surprise of seeing the action pick it up and start executing jobs with it!

On top of this, the GHA infrastructure is such that workflows can run in parallel, and most jobs run either all in parallel, or massively in parallel.
The end result is that jobs that CI runs that often took 3-5 minutes on Travis are now completed on GHA in under a minute.
This kind of fast feedback makes iterating on patches far easier, and allows our maintainers to focus on what a patch is accomplishing, instead of whether or not it meets QA guidelines.

### Takeaways

As a project lead, many think my job would be pushing new features.
However, with as many repositories as we have, and the amount of specialized knowledge many of them require, **my job is to enable contributions**.
I've found the tooling we've been developing to be a huge boon in helping maintainers, including myself, shepherd in new contributions and get them released to the world quickly.
As this has often been a critique of Zend Framework in the past and Laminas currently, I have high hopes that we can turn that story around and get quality code out to PHP users everywhere.

Speaking of: we are **always** looking for more maintainers for our packages.
If you are interested, [nominate yourself](https://github.com/laminas/technical-steering-committee/issues/new?assignees=&labels=Nomination&template=Maintainer_Nomination.md&title=%5BNOMINATION%5D%5BMAINTAINER%5D%3A+%7Bname+of+person+being+nominated%7D)!
