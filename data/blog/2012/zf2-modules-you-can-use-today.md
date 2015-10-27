---
id: zf2-modules-you-can-use-today
author: matthew
title: 'ZF2 Modules You Can Use Today'
draft: false
public: true
created: '2012-02-08T11:31:47-05:00'
updated: '2012-02-08T11:31:47-05:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
One key new architectural feature of Zend Framework 2 is its new module
infrastructure. The basic idea behind modules is to allow developers to both
create and consume re-usable application functionality — anything from
packaging common assets such as CSS and JavaScript to providing MVC application
classes.

<!--- EXTENDED -->

As an example, for my own site, I've created: a "Contact" module for rendering
and processing contact forms; a "SkeletonCSS" module for dropping in
[Skeleton](http://getskeleton.com) into my sites; a "CommonResource" module
with a very, very basic data mapper implementation, and a "Blog" module that
consumes it to deliver the very blog you're reading now.

But the *real* goal of the module infrastructure is for developers to *share*
their modules, so that other developers don't need to develop that very same
functionality for their own site. And to my delight, that's already happening!

So, here's a list of some of the ZF2 modules I've found in the wild.

- [SkeletonCss](http://bit.ly/yVBXkw). I mentioned this one already, but it's a
  version of SkeletonCss, an adaptive response CSS/JS framework, packaged for
  easy consumption as a ZF2 module. The git URI is
  "git://mwop.net/SkeletonCss.git".
- [DoctrineModule](https://github.com/doctrine/DoctrineModule). One of the most
  frequently asked questions I get is, "Will ZF2 use Doctrine?" My answer has
  been that we'll provide a bridge — but the cool thing is that the Doctrine
  project has already decided to do it. Spear-headed by an enthusiastic ZF2
  contributor/collaborator, [Kyle Spraggs](http://twitter.com/SpiffyJr), this
  module provides the base functionality for integrating Doctrine 2 into your
  ZF2 site (primarily access to the base/common functionality). Two other
  modules provide additional functionality:
  - [DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule) provides
    the ability to interact with the Doctrine 2 ORM, and
  - [DoctrineMongoODMModule](https://github.com/doctrine/DoctrineMongoODMModule)
    provides the ability to interact with and consume the Doctrine 2 Object
    Document Mapper for MongoDB.
- [EdpSuperluminal](https://github.com/EvanDotPro/EdpSuperluminal). This module
  can be used to cache all classes you use in your application to a single
  include file — giving you a performance boost.
- [ZfcUser](https://github.com/ZF-Commons/ZfcUser). This module was begun by
  [Evan Coury](http://evan.pro), the lead developer behind ZF2's module system;
  its purpose is to provide a drop-in solution for registering and
  authenticating users. The module itself provides functionality consuming
  `Zend\Db`; however, several other modules are also offered to provide other
  persistence layers, including:

  - [ZfcUserDoctrineORM](https://github.com/ZF-Commons/ZfcUserDoctrineORM) module, and
  - [ZfcUserDoctrineMongoODM](https://github.com/ZF-Commons/ZfcUserDoctrineORM);
  
  more are planned. The "Zfc" namespace is used because Evan realized he and
  several others were working on similar solutions, and felt that collaboration
  would lead to a better solution than each would develop individually; this in
  turn led to the creation of a "Zend Framework Commons" organization on
  GitHub, with the goal of providing high-quality modules solving common
  application problems.
- [AsseticBundle](https://github.com/widmogrod/zf2-assetic-module). This is a
  module providing integration with the excellent
  [Assetic](https://github.com/kriswallsmith/assetic) asset management
  framework.
- [TwitterBootstrap](https://github.com/widmogrod/zf2-twitter-bootstrap-module).
  Many developers are gravitating to the [Twitter Bootstrap](https://github.com/twitter/bootstrap)
  project for CSS layouts.  This module depends on the AsseticBundle already
  listed above, and provides both Twitter Bootstrap as well as integration with
  the current (ZF1) incarnation of `Zend\Form`.

I've seen a number of others as well, and know of more on their way (as an
example, a ZfcAcl module to complement the ZfcUser module). Writing modules is
incredibly easy, and a great way to both learn ZF2 and collaborate and share
with other developers.

Where are *your* modules?
