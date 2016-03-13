---
id: 255-How-to-Contribute-to-ZF2
author: matthew
title: 'How to Contribute to ZF2'
draft: false
public: true
created: '2011-03-04T09:30:00-05:00'
updated: '2012-04-17T11:45:00-04:00'
tags:
    0: php
    2: 'zend framework'
---
ZF2 development is ramping up. We've been at it for some time now, but mostly
taking care of infrastructure: converting to namespaces, re-working our
exception strategy, improving our test suites, and improving our autoloading
and plugin loading strategies to be more performant and flexible. Today, we're
actively working on the MVC milestone, which we expect to be one of the last
major pieces necessary for developers to start developing on top of ZF2.

A question I receive often is: "How can I contribute to ZF2?"

Consider this your guide.

<!--- EXTENDED -->

Getting Setup
-------------

~~Just like ZF1, ZF2 requires the same [Contributors License Agreement
(CLA)](http://framework.zend.com/cla). This agreement helps protect end users
from litigation; basically, you're ensuring that you are the author of the code
you contribute, or that you have secured the rights to any code you contribute.
If anybody contests the origin, they can legally only approach you, not the end
users.~~

~~You can either FAX your signed CLA, or you can scan and email it to us; we
prefer the latter (email).~~

~~As part of the CLA submission process, d~~

*Note: a CLA is no longer necessary for ZF2 contribution!*

Don't forget to sign up for an account on our
[issue tracker](http://framework.zend.com/issues) if you haven't already; if you have,
make sure your email address is current and correct (you can do so via our
[Crowd instance](http://framework.zend.com/crowd)).

ZF2 development is not using the same Subversion repository as ZF1. Instead,
we've switched to [Git](http://git-scm.org/) for our version control needs; the
distributed nature of our ZF contributors lends itself well to a distributed
VCS, and Git was the VCS that our contributors were most familiar with.

While we are hosting our own Git repository, the kind folks at
[GitHub](http://github.com/) have set up a mirror of the repository that is
synced once or twice a day. As such, we encourage developers to utilize GitHub
to host their repositories.

You can find the ZF2 Git repository at
[http://github.com/zendframework/zf2](http://github.com/zendframework/zf2),
along with instructions on forking the repository.

Once you have forked and cloned the repository locally, please update your git
configuration to ensure your author email address matches that in our issue
tracker:

```bash
$ cd zf2
$ git config set user.email <email>
```

The above information is also [available in detail on our wiki](http://bit.ly/zf2gitguide).

Conventions
-----------

Now that you're ready to contribute, we have a few conventions.

First, each discrete bugfix or feature change should be done in a separate branch of your repository. This makes it simpler to evaluate and review changes.

My suggestions for naming these branches are as follows:

- `hotfix/<Issue ID>`, where `<Issue ID>` is the ID from the [ZF issue tracker](http://framework.zend.com/issues); e.g., "hotfix/ZF-10989". Use this format for bugfixes.
- `feature/<featurename>`, where `<featurename>` is a short yet descriptive name of the developed feature; e.g., "feature/translate_resource_es". Use this format for feature changes or new features.

In particular, using the issue tracker ID for the bugfixes helps a ton in evaluating if the fix is appropriate for the reported issue.

Next, for all code changes — be they bugfixes, feature changes, or new features — include unit tests. We will throw a pull request back your way immediately if it does not include tests.

Once you've completed your bugfix or feature request, issue us a pull request. Again, GitHub makes this dead-simple. Make sure when you create the pull request that you give a good, succinct title, and adequate detail in the message describing what you've done; this makes review and prioritization easier.

Where we are currently
----------------------

Last year, we drafted our [requirements](http://bit.ly/zf2reqs), as well as a list of [milestones](http://bit.ly/zf2milestones). They include:

- Autoloading and Plugin Loading (improvements and additions to autoloading and plugin loading, as well as making these consistent throughout the framework)
- Exceptions (updated exception strategy to make it more flexible and remove dependencies)
- Testing (consistent infrastructure, better use of modern PHPUnit features, etc.)
- MVC (more flexible strategies and improvements to architecture)
- Internationalization and Localization (performance and architectural optimizations)
- Documentation (consistent structure, including more examples and detailing all configuration options and methods)

To date, we've completed the following milestones:

- Autoloading and Plugin Loading
- Exceptions

We've [proposed a documentation structure](http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+Documentation+in+ZF2), but, obviously, have not completed the Documentation milestone (I expect this to be one of the last to be completed, though it should be accomplished in parallel with other milestones).

Additionally, we've worked extensively on our testing infrastructure, but have a few changes yet to make the structure uniform and cohesive.

We're currently working on the MVC milestone, and have a set of [MVC interface proposals](http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+MVC+Interfaces) complete and accepted; development of proposed implementation is in progress, and we will likely have further proposals in the coming weeks, as well as specific tasks the community can assist us with.

What you can work on now
------------------------

Much of the current work is being spear-headed by Zend's ZF team, for which I am Project Lead. However, there's plenty to work on:

- The community maintains a list of [component maintainers](http://framework.zend.com/wiki/display/ZFDEV2/Component+Maintainers). If you're interested in working on a component, contact the maintainer or any listed developers, and discuss the direction with them. If you can't reach anyone, or the component has no listed maintainers, offer to take over maintenance.
- Most *service components* currently need to be migrated to namespaces. These are listed on the same page linked above, and are an excellent place to start.
- If nothing else, just running individual component test suites and helping fix testing issues is always a huge help.
- Review the [proposed documentation standard](http://framework.zend.com/wiki/display/ZFDEV2/Proposal+for+Documentation+in+ZF2), and start updating the documentation.

Thank You!
----------

To those of you who take the plunge and start contributing, I extend an early thank you! The efforts of our contributors are what make the framework compelling for developers!

Update
------

- Fixed all links - thanks for the reports!
- **2012-04-17**: struck out CLA info; no longer required
