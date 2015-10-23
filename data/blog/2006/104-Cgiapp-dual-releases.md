---
id: 104-Cgiapp-dual-releases
author: matthew
title: 'Cgiapp dual releases'
draft: false
public: true
created: '2006-02-19T18:02:47-05:00'
updated: '2006-02-19T18:30:55-05:00'
tags:
    - php
---
Today, I have released two versions of Cgiapp into the wild, Cgiapp 1.8.0 and Cgiapp2 2.0.0rc1.

Cgiapp 1.8.0 is a performance release. I did a complete code audit of the class, and did a number of changes to improve performance and fix some previously erratic behaviours. Additionally, I tested under both PHP4 and PHP5 to make sure that behaviour is the same in both environments.

However, Cgiapp 1.8.0 markes the last feature release of Cgiapp. I am deprecating the branch in favor of Cgiapp2.

Cgiapp2 is a PHP5-only version of Cgiapp. Some of the changes:

- `Cgiapp2` is an abstract class, with the abstract method `setup()`. Now it is truly non-instantiable!
- Cgiapp2 makes extensive use of visibility operators. Key methods have been marked final, some methods are now protected, others static. See [the changelog](http://cgiapp.sourceforge.net/index.php/view/Changelog2) for more information.
- Cgiapp2 is now `E_STRICT` compliant.
- Cgiapp2 implements the CGI::Application 4.x series callback hook system. This is basically an observer pattern, allowing developers to register callbacks that execute at different locations in the runtime.
- Cgiapp2 adds some extensive error and exception handling classes, including observable errors and exceptions.
- I created a template interface. If implemented, a template engine can be plugged into the architecture at will — at the superclass, application class, and instance script level, allowing developers to mix-and-match template engines or choose whichever matches their taste, *without* having to rewrite application code. Three template plugins are included:
  - [Smarty](http://smarty.php.net/)
  - [Savant2](http://phpsavant.com/yawiki/)
  - [Savant3](http://phpsavant.com/yawiki/index.php?area=Savant3&page=HomePage)

[Cgiapp](http://sourceforge.net/project/showfiles.php?group_id=125419&package_id=137071) and [Cgiapp2](http://sourceforge.net/project/showfiles.php?group_id=125419&package_id=180286) are available at Sourceforge.

Keep reading for more information on the evolution of Cgiapp2.

<!--- EXTENDED -->

I've been loathe to make Cgiapp (1) PHP5-only, and (2) into a version 2 style PEAR package (e.g., Cgiapp2 versus Cgiapp). However, I realized recently that both were necessary.

I've been doing a lot of reading the past 6 months on design patterns, and also exposed to a bunch of advanced PHP5 code since my employment at Zend. In developing Cgiapp2, I decided to put both to use so I could help advance my coding skills.

At this point, I don't know how I ever thought I could successfully port Cgiapp without PHP5. Having the ability to define abstract classes, static properties and methods, and marking methods as final is truly useful and makes the class much more robust.

PHP5 is truly a great advance for PHP4. I had been somewhat ambivalent about it before; I liked the ability to pass objects without using the reference notation (e.g., `=&`), and had used SimpleXML a little, but overall didn't see much use to the new features. With my introduction to design patterns, I started seeing more uses for them, as some design patterns are difficult, if not impossible, to implement without them. The visibility operators are truly useful. Static properties and methods are also incredibly useful. Exceptions completely change the face of error handling for PHP. I've even found uses for the Reflection API, something I never thought I'd do.

If you're not using PHP5 yet, you're doing yourself a disservice. Upgrade today, and start coding projects that make use of the new OOP model.

Regarding the versioning numbers, I realized that if I'm going to have two stable versions of Cgiapp around, having them both named Cgiapp would make it difficult for developers to migrate — they wouldn't be able to have them easily accessible on the same machine at the same time. Once again, the PEAR group got it right, when it comes to backwards compatibility. Thus, Cgiapp 2.0.0 was renamed to Cgiapp2 2.0.0.

Enjoy the new releases!
