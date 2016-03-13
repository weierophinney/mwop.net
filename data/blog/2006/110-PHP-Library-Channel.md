---
id: 110-PHP-Library-Channel
author: matthew
title: 'PHP Library Channel'
draft: false
public: true
created: '2006-05-16T00:01:00-04:00'
updated: '2006-05-16T08:46:33-04:00'
tags:
    - php
---
I've been working on [Cgiapp](http://cgiapp.sourceforge.net/) in the past few months, in particular to introduce one possibility for a Front Controller class.  To test out ideas, I've decided to port areas of my personal site to Cgiapp2 using the Front Controller. Being the programmer I am, I quickly ran into some areas where I needed some reusable code — principally for authentication and input handling.

I've been exposed to a ton of good code via [PEAR](http://pear.php.net/), [Solar](http://www.solarphp.com/), [eZ components](http://ez.no/products/ez_components), and [Zend Framework](http://framework.zend.com/). However, I have several criteria I need met:

- I want PHP5 code. I'm coding in PHP5, I should be able to use PHP5 libraries, not PHP4 libraries that work in PHP5 but don't take advantage of any of its features.
- I prefer few dependencies, particularly lock-in with existing frameworks. If I want to swap out a storage container from one library and use one from another, I should be free to do so without having to write wrappers so they'll fit with the framework I've chosen. Flexibility is key.
- Stable API. I don't want to have to change my code every few weeks or months until the code is stable.
- I should be able to understand the internals quickly.

So what did I choose? To reinvent the wheel, of course!

To that end, I've opened a new PEAR channel that I'm calling [PHLY, the PHp LibrarY](http://weierophinney.net/phly/), named after my blog. The name implies soaring, freedom, and perhaps a little silliness.

It is designed with the following intentions:

- Loosely coupled; dependencies should be few, and no base class should be necessary.
- Extendible; all classes should be easily extendible. This may be via observers, interfaces, adapters, etc. The base class should solve 80% of usage, and allow extensions to the class to fill in the remainder.
- Designed for PHP5 and up; all classes should make use of PHP5's features.
- Documented; all classes should minimally have excellent API-level documentation, with use cases in the class docblock.
- Tested; all classes should have unit tests accompanying them.
- Open source and commercial friendly; all classes should use a commercial-friendly open source license. The BSD license is one such example.

Please feel free to use this code however you will. Comments, feedback, and submissions are always welcome.
