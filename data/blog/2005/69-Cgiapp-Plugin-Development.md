---
id: 69-Cgiapp-Plugin-Development
author: matthew
title: 'Cgiapp Plugin Development'
draft: false
public: true
created: '2005-04-28T22:59:42-04:00'
updated: '2005-04-28T23:13:26-04:00'
tags:
    - php
---
I've been working on the Cgiapp roadmap, and particularly on the plugin
architecture. I'd been operating under the assumption that I'd have to make a
PHP5-specific release (Cgiapp2) to allow this feature. However, it turns out I'm
wrong.

<!--- EXTENDED -->

PHP has had [overload](http://php.net/overload) functionality since PHP4, and it
has been in the standard build since 4.3.0. It turns out that the only things I
had to do differently to get plugins working in PHP4 (which work via the
`__call()` magic overloading method) were to turn on overloading for the
function (via the overload() function, if available), and to look for a global
`$CGIAPP_PLUGINS` variable (rather than a class static).

In doing so, I started evaluating the need for Cgiapp2.

It turns out I can do a large amount of what I'd planned for my Cgiapp2 roadmap
in PHP4… which largely eliminates the need for Cgiapp2. However, there are a few
things which I can do more gracefully or better using PHP5 techniques — such as
testing for errors in the run mode, and overloading.

My decision is to create a separate class, `Cgiapp5`, which inherits from `Cgiapp`
and overrides methods as necessary. Currently, it unsets the `$CGIAPP_PLUGINS`
variable (as unnecessary), overrides the `run()` method (to use exception handling
instead of PHP's error handling), and overrides the `__call()` method (to use
the class static property instead of the global variable). The unit tests so far
show both versions as working and compatible.

What I like about this pattern of development is that I can add some powerful
features now for PHP4 users — who will be, for some time, I'm certain, the
largest base of users, until PHP5 gains momentum. But, simultaneously, I can
work with the more dynamic developments of PHP5 without sacrificing backwards
compatability.

The downside is that there's little incentive for developers to write for
Cgiapp5 instead of Cgiapp — and that's the future. However, at this point, I
want to aim for more developers than fewer.

Leave a comment and let me know what direction Cgiapp should take.
