---
id: why-conventions-matter
author: matthew
title: 'Why Conventions Matter'
draft: false
public: true
created: '2012-01-11T22:58:28-05:00'
updated: '2012-01-11T22:58:28-05:00'
tags:
    - php
    - perl
    - 'zend framework'
    - zf2
---
When I started teaching myself scripting languages, I started with Perl. One
Perl motto is ["TMTOWTDI"](http://en.wikipedia.org/wiki/TMTOWTDI) — "There's
More Than One Way To Do It," and pronounced "tim-toady." The idea is that
there's likely multiple ways to accomplish the very same thing, and the culture
of the language encourages finding novel ways to do things.

I've seen this principle used everywhere and in just about every programming
situation possible, applied to logical operations, naming conventions,
formatting, and even project structure. Everyone has an opinion on these
topics, and given free rein to implement as they see fit, it's rare that two
developers will come up with the same conventions.

TMTOWTDI is an incredibly freeing and egalitarian principle.

Over the years, however, my love for TMTOWTDI has diminished some. Freeing as
it is, is also a driving force behind having coding standards and conventions —
because when everyone does it their own way, projects become quickly hard to
maintain. Each person finds themselves reformatting code to their own
standards, simply so they can read it and follow its flow.

Additionally, TMTOWTDI can actually be a foe of simple, elegant solutions.

Why do I claim this?

<!--- EXTENDED -->

Recently, discussing module structure in Zend Framework 2, some folks were
arguing that our recommended directory structure invokes the
[YAGNI](http://en.wikipedia.org/wiki/YAGNI) principle: You Ain't Gonna Need It.
Our recommendation is this:

```
ModuleName/
    autoload_classmap.php
    Module.php
    config/
        module.config.php
        (other config files)
    public/
        css/
        images/
        js/
    src/
        ModuleName/
            (source files)
    test/
    view/
```

The argument is that since most modules implement a single namespace, and
because we recommend following
[PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
for autoloaders, it makes sense to simply have the source files directly under
the module directory.

```
ModuleName/
    autoload_classmap.php
    Module.php
    (other source files)
    config/
        module.config.php
        (other config files)
    public/
    test/
    view/
```

The argument myself and others made was that it makes sense to group the files
by responsibility. However, the module system ultimately *doesn't care* how you
organize the module — we've embraced TMTOWTDI, and our only requirement is that
for your module to be consumed, you must define a `ModuleName\Module` class,
and notify the module manager how to find it. Anything goes.

How does that bolster my argument about the importance of conventions? It
doesn't. What does is what following the recommended structure enabled me to
do.

One common concern identified with having a re-usable module system is that you
should be able to expose public assets easily: things like module-specific CSS
or JavaScript, or even images. The first question that arises when you consider
this is: where do I put them in my module? That's why the recommendation
includes a `public` directory. In fact, the recommendation goes a step further,
and suggests `css`, `images`, and `js` directories as well.

Now, your modules are typically *outside* the document root. This is a
rudimentary and fundamental security measure, and also simplifies deployment to
a degree — you don't need to worry about telling the web server about what it
*shouldn't* serve. But if the modules are outside the document root, how can I
expose their public assets?

There are a two possibilities that immediately jump to mind:

- Install scripts for modules, which copy the files into the document root.
- Symlink the files into the document root.

Both are valid, and easy to accomplish. Both raise the same question: where,
exactly? What if multiple modules have public assets named the same? how do I
refer to my assets withing things like view scripts?

This is where having a convention starts to make sense: having a convention
should answer these questions unambiguously.

My answer: public access should be at `/css/ModuleName/stylesheetname`, or
`/js/ModuleName/scriptname` or `/images/Modulename/imagename`. It's a
dirt-simple rule that fulfills the use cases.

However, I'm now stuck with having to develop either install scripts or
remembering to create symlinks — ugh. And that's where having conventions led
me to a simple, elegant solution.

I added one line to my Apache vhost definition:

```apacheconf
AliasMatch /(css|js|images)/([^/]+)/(.*) /path/to/module/$2/public/$1/$3
```

The translation:

> When I come across a path to CSS, JS, or image files that are in a
> subdirectory, alias it to the corresponding public asset of the matched
> module directory.

I dropped this into my vhost, restarted Apache, and now not only were the
assets I'd created already served, but any new ones I create are immediately
available as well. Having a convention actually simplified my choices and my
solutions.

Rapid application development at its finest.

My point is this: there will always be more than one way to do things when
you're programming, and you may not always agree with the decisions your team
has made, or the component library or framework you're using has made. However,
if you poke around a little and play within those confines, you may find that
those decisions make other decisions easier, or disappear altogether.
