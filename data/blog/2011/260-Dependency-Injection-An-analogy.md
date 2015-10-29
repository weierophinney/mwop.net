---
id: 260-Dependency-Injection-An-analogy
author: matthew
title: 'Dependency Injection: An analogy'
draft: false
public: true
created: '2011-03-21T17:52:15-04:00'
updated: '2011-03-25T02:25:13-04:00'
tags:
    - php
    - oop
---
I've been working on a proposal for including service locators and dependency
injection containers in Zend Framework 2.0, and one issue I've had is trying to
explain the basic concept to developers unfamiliar with the concepts — or with
pre-conceptions that diverge from the use cases I'm proposing.

In talking with my wife about it a week or two ago, I realized that I needed an
analogy she could understand; I was basically using her as my
[rubber duck](http://en.wikipedia.org/wiki/Rubber_duck_debugging). And it turned
out to be a great idea, as it gave me some good analogies.

<!--- EXTENDED -->

Dining Out
----------

The analogies go like this: you walk into a burger join, and you're hungry.

- Dependency Injection is like ordering off the menu — but specifying things
  like, "I'd like to substitute portabella mushrooms for the patties, please."
  The waiter then goes and brings your dish, which has portabella mushrooms
  instead of the hamburger patties listed on the menu.
- Service Location is like ordering with substitutions, and having the waiter
  completely ignore the substitutions; you get what's on the menu, nothing more,
  nothing less.

Now, when it comes to Zend Framework's version 1 releases, we've really got
neither. Our situation is more like a buffet or a kitchen — you grab a little of
this, a little of that, and assemble your own burger. It's a lot more work.

Frankly, I'm lazy, and like my dinner brought to me… and if I want any
substitutions, I'd like those, too.

Getting the Ingredients
-----------------------

A number of developers I've talked to seem to think DI is a bit too much "magic"
— they're worried they'll lose control over their application: they won't know
where dependencies are being set.

There are two things to keep in mind:

1. you, the developer, define the dependencies up front
2. if you don't pull the object from the container, you're in charge

Regarding the second point, it appears some developers think that with a DI
container in place, dependencies magically get injected in *every* object. But
that's simply not the case. If you use normal PHP:

```php
$o = new SomeClass();
```

you'll get a new instance, just like always, configured only with any parameters
you pass in to the constructor or methods you call on it. It's only when you
retrieve the object from the DI container that you dependency injection takes
place; if you do that, you can always examine the DI configuration (which can
either be programmatic or via a configuration file) to determine what
dependencies were configured.

Basically, it's like the difference between making your own hamburger patty out
of fresh ground sirloin, and ordering Animal Style from In-N-Out.

I'm done now
------------

What's your favorite way of thinking of these concepts?
