---
id: 2016-01-28-expressive-stable
author: matthew
title: 'Expressive 1.0 in the Wild!'
draft: false
public: true
created: '2016-01-28T12:45:00-05:00'
updated: '2016-01-28T12:45:00-05:00'
tags:
    - http
    - middleware
    - php
    - programming
    - psr-7
---
A few hours ago, we pushed [Expressive 1.0](https://github.com/zendframework/zend-expressive/releases/tags/1.0.0).

This is a huge milestone for the ZF3 initiative; I've even called it the
cornerstone. It signals a huge shift in direction for the project, returning to
its roots as a *component* library. Expressive itself, however, also signals
the future of PHP applications we envision: composed of layered, single-purpose
PSR-7 middleware.

<!--- EXTENDED -->

I won't go into the details of the Expressive 1.0 release; you can read
about it [on the Zend Framework blog](http://framework.zend.com/blog/2016-01-28-expressive-1.0-stable.html).

What I'm excited about is that this marks the fruition of the PSR-7 effort for
me. I started work on PSR-7 due to the successes I'd had working with middleware
in node.js, and wanted to see a similar ecosystem in PHP.

Today, we have:

- [Relay](http://relayphp.com/)
- [Slim (v3)](http://www.slimframework.com/)
- [Penny](http://pennyphp.org/)
- [Expressive](https://zendframework.github.io/zend-expressive/)

and likely a number of others. The ecosystem has blossomed tremendously already;
just take a look at the [PSR-7 packages on Packagist](https://packagist.org/search/?search_query%5Bquery%5D=psr-7)!
Chances are, if you need to accomplish something via middleware, somebody has
already written it; if they haven't you can likely write it in a handful of
lines of code.

Expressive started out with me remarking off-handedly that I'd like to create a
project that is to [Stratigility](https://github.com/zendframework/zend-stratigility)
(the ZF PSR-7 middleware foundation) what [Express](http://expressjs.com/) is to
[Connect](https://github.com/senchalabs/connect) &mdash; in other words, a
microframework providing the most often-desired features when writing web
applications and APIs, but no more. What I saw with Connect and Express was that
developers were able to write single-purpose middleware, share it, and layer
middleware to create complex applications. The features Express layered on top
of Connect simplified the most common problems of routing middleware, while
Connect provided a robust, simple runtime.

[Enrico](http://www.zimuel.it) was particularly excited about the concept, and
got the ball rolling last summer, and it's been a whirlwind of activity ever
since. And then others started playing with the code, and contributing ideas,
validating the approach, and suggesting new directions. We now have a
microframework in place that rivals zend-mvc in flexibility, while retaining our
core principals of simplicity and minimalism.

How do I know the approach works?  This site [runs on Expressive already](https://github.com/weierophinney/mwop.net/blob/8a54313874706b4abd7e1a3082433ab495cabbeb/composer.json#L30).
And many of our users and contributors are already running on it. But the best
validation I've read was from one of our prolific Zend Framework contributors,
[Michaël Gallego](http://www.michaelgallego.fr/), on a recent thread:

> For me the only reason to use Zend\Mvc (and, therefore, the eco-system around
> it) is the facilities provided by the module eco-systems. But even in that
> case, I've found out that for that, the middleware philosophy makes it so much
> easier. You no longer need to install Zend\Authentication that would try to
> map into the mvc, spending a lot of time how it works… Want an
> authentication? Just analyze your need, and boom, ten lines letter, it's done.

That sort of comment and realization was exactly what I experienced working in
node.js almost two years ago. And now, today, it's a reality in PHP.
