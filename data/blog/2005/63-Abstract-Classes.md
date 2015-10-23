---
id: 63-Abstract-Classes
author: matthew
title: 'Abstract Classes'
draft: false
public: true
created: '2005-04-17T02:24:15-04:00'
updated: '2005-04-20T15:48:31-04:00'
tags:
    - php
---
I just had to add a note over on PHP.net regarding abstract classes and methods:
[Object Abstraction](http://php.net/language.oop5.abstract).

I'm working on Cgiapp2, which is a PHP5-only implementation of Cgiapp that is
built to utilize PHP5's new object model as well as exceptions. One thing I
decided to do, initially, was to make it an abstract class, and to mark the
overridable methods as abstract as well.

In testing, I started getting some strange errors. Basically, it was saying in
my class extension that an abstract method existed, and thus the class should be
marked as abstract, and, finally, that this means it wouldn't run.

What was so odd is that the method didn't exist in the extension at all.

So, I overrode the method in the extension… and voila! Everything worked fine.

The lesson to take away from this is quite simple: if the method does not need
to be present in the overriding class, don't mark it as abstract. Only mark a
method as abstract if:

1. The method is required in the class implementation, and
2. The extending class should be responsible for implementing said method

Now I need to update my source tree…. :-(
