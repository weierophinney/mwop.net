---
id: 214-Zend-Framework-1.8-PREVIEW-Release
author: matthew
title: 'Zend Framework 1.8 PREVIEW Release'
draft: false
public: true
created: '2009-04-07T22:27:53-04:00'
updated: '2009-04-13T18:30:48-04:00'
tags:
    - php
    - 'zend framework'
---
By the time you read this, the [Zend Framework](http://framework.zend.com/) team
will have released a **preview** release of 1.8.0. While the final release is
scheduled for later this month, this release represents the hard work of many
contributors and shows off a variety of powerful new components.

If you're a Zend Framework user, you should give the preview release a spin, to
see what it can do:

- [1.8 Preview Release (zip)](http://framework.zend.com/releases/ZendFramework-1.8.0a1/ZendFramework-1.8.0a1.zip)
- [1.8 Preview Release (tarball)](http://framework.zend.com/releases/ZendFramework-1.8.0a1/ZendFramework-1.8.0a1.tar.gz)

<!--- EXTENDED -->

One common criticism of Zend Framework is that it doesn't fulfill the
traditional definition of a framework. One definition cited has been from
[TheFreeDictionary](http://www.thefreedictionary.com/framework), and includes
the following two potential matches:

> A structure for supporting or enclosing something else, especially a skeletal
> support used as the basis for something being constructed.

> A set of assumptions, concepts, values, and practices that constitutes a way
> of viewing reality.

The argument is that ZF does not provide the "assumptions" or opinions on how an
application should be built. However, this makes sense only if you buy into the
idea that a framework should always follow the "convention over configuration"
rule — which we soundly reject with Zend Framework. Our opinion has always been
that developers know best how their application should be built, and that ZF
code should support the myriad uses to which they will put it.

That said, with the addition of [Zend_Application](http://framework.zend.com/manual/en/zend.application.html) and [Zend_Tool](http://framework.zend.com/manual/en/zend.tool.framework.html), Zend Framework now provides a comprehensive framework for its users that is opinionated *and* provides the flexibility for developers to impose whatever structure they need.

`Zend_Tool` provides a tooling framework for Zend Framework. It allows you to
create your own tooling providers that can then be utilized by tooling clients,
which utilize an RPC style architecture. We now ship a Console or command line
interface (CLI) client that allows you to perform a variety of tasks — such as
setting up your initial project structure, adding new resources to a project,
adding action methods and view scripts to controllers, and more. As an example,
you can now do this:

```bash
$ zf create project foo
```

and generate the skeleton for a new project in a directory named "foo", with the following structure:

```
|-- application
|   |-- Bootstrap.php
|   |-- configs
|   |   `-- application.ini
|   |-- controllers
|   |   |-- ErrorController.php
|   |   `-- IndexController.php
|   |-- models
|   `-- views
|       |-- helpers
|       `-- scripts
|           |-- error
|           |   `-- error.phtml
|           `-- index
|               `-- index.phtml
|-- library
|-- public
|   |-- .htaccess
|   `-- index.php
`-- tests
    |-- application
    |   `-- bootstrap.php
    |-- library
    |   `-- bootstrap.php
    `-- phpunit.xml
```

In the future, we will be adding more support to this. A big kudos to [Ralph
Schindler](http://ralphschindler.com/) for doing the heavy lifting on this
project.

`Zend_Application` provides both bootstrapping of your PHP environment as well
as your application environment. When using `Zend_Application`, you will create
an application bootstrap class that can either use resource plugin classes or
define initialization routines internally; regardless, it allows you to define
resource dependencies and bootstrap the various facets of your application. Even
better, it introduces modules as first-class citizens of your applications. With
the introduction of `Zend_Loader_Autoloader_Resource` and
`Zend_Application_Module_Autoloader`, you can now use autoloading to resolve the
various resource classes in your modules — such as models, forms, and plugins.
This tremendously simplifies the story for utilizing resources from other
modules, as well as using resources within the same module. A big thank you goes
out to [Ben Scholzen](http://www.dasprids.de/) for getting the ball rolling on
`Zend_Application` and his significant contributions to the component.

There are many other stories in this release:

- Amazon EC2 and S3 support (contributed by [Jon Whitcraft](http://www.bombdiggity.net/) and Justin Plock/[Stas Malyshev](http://php100.wordpress.com/), respectively)
- `Zend_Navigation`, a comprehensive solution to generating and organizing navigation elements for use with breadcrumbs, navigation menus, sitemaps, and more (contributed by Robin Skoglund and Geoffrey Tran, from [Zym](http://www.zym-project.com/))
- Numerous additions to `Zend_Validate` and `Zend_Filter` support (primarily by [Thomas Weidner](http://www.thomasweidner.com/flatpress/))
- Improvements to `Zend_Search_Lucene` support including searching multiple indexes and keyword field search via query strings (contributed by Alexander Veremyev)
- Improvements to `Zend_Pdf`, including page scaling, shifting, and skewing (contributed by Alexander Veremyev)
- and more…

A hearty thanks to all who have contributed so far in this release. Start
testing it, and let us know what we can improve for the final 1.8 release later
this month!
