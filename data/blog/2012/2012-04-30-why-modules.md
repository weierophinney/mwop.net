---
id: 2012-04-30-why-modules
author: matthew
title: 'Why Modules?'
draft: false
public: true
created: '2012-04-30T16:00:00-05:00'
updated: '2012-04-30T16:00:00-05:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
I've blogged [about getting started with ZF2 modules](/blog/267-Getting-started-writing-ZF2-modules.html),
as well as [about ZF2 modules you can already use](/blog/zf2-modules-you-can-use-today.html).
But after fielding some questions recently, I realized I should talk about
*why* modules are important for the ZF2 ecosystem.

<!--- EXTENDED -->

History
-------

In the autumn of 2006, [Andi](http://andigutmans.blogspot.com/) asked me to
spearhead a refactor of the Zend Framework MVC, prior to a stable release. The
idea was to address the growing number of issues and feature requests, get it
well-tested, and document it thoroughly before we were ready for a 1.0.0 stable
release.

Late in that refactoring, a few folks approached me saying they wanted support
for "modules". The idea would be to have self-contained directories containing
discrete MVC functionality — controllers, views, related models, etc.
Additionally, they wanted routing to take into account the module, so that we
could have controllers with the same "name", but resolving to separate,
discrete classes.

The "solution" I came up with basically worked, but was quite limited. You
could drop modules into a directory, which the front controller would scan in
order to be able to resolve URLs of the form `/:module/:controller/:action/*`.
(You could also explicitly define a module in the route configuration if
desired).

This mostly worked, until we introduced `Zend_Application`, at which point it
fell apart. Why? Because we couldn't quite get bootstrapping to work.
Bootstrapping the application was easy, but adding modules and their
bootstraps, and sharing dependencies between all of them, proved to be quite
difficult, and we never truly solved it.

Add to this the fact that the only way to get dependencies into controllers was
via `Zend_Registry` or the front controller singleton, and the end result were
modules that could never truly be shared or simply dropped into an application.

Modules in ZF2
--------------

One of the very first requirements for ZF2, therefor, was to solve the module
problem. The goals were fairly simple:

> Modules should be re-usable. Developers should be able to drop in third-party
> modules easily, and immediately utilize them with zero or small amounts of
> configuration. Developers should never have to directly alter module code,
> ever, to get them to work in their applications; customization should be
> easily achieved via configuration or substitution.

Why?

The goal of any good application framework or content system should be to make
development of websites as easy as possible. Good systems make it possible to
use as little or as much of the framework as needed, and to make extension of
the framework trivial. This latter point is perhaps the most important aspect:
the quality of any good application ecosystem can typically be judged by the
amount and quality of third-party plugins developed for it.

If your framework is making you write boilerplate code to handle authentication
for every site you write, or making you write code for common application
features such as blogs, comment systems, contact forms, etc., then something is
wrong. These sorts of tasks should be done at most a handful of times, and
*shared* with the community.

The end-goal is to be able to pull in a handful or more of plugins that do
these tasks for you, configure them to suit your needs, and then focus on
building out the functionality that is truly unique to your website.

Building Blocks
---------------

I'll give a concrete example. In parallel with ZF2 development, I've been
rebuilding this very site. I've needed the following pieces:

- A handful of static pages (home page, résumé, etc.)
- A contact form
- A blog
- Authentication in order to "password protect" a few pages
- A few view helpers (github status, disqus display, etc)

How much of this functionality is unique to my site, other than the content?
Pretty much none of it. Ideally, I should be able to find some modules, drop
them in, and create some custom view scripts.

Which is what I did. That said, I developed several of the modules, but in some
cases, such as authentication, I was able to grab modules from elsewhere. The
beauty, though, is that in the future, I or others can re-use what I've
created, and quite easily.

This kind of building-block development makes *your* job easier as a developer
— and allows you to focus on the bits and pieces that make your site unique. As
such, I truly feel that ***modules are the most important new feature of
ZF2***.

Fin
---

If you're developing on top of ZF2 today, I have one piece of advice: create
and consume modules. Share your modules. Help make ZF2 a productive, fun,
collaborative ecosystem that allows developers to get things done and create
fantastic new applications.
