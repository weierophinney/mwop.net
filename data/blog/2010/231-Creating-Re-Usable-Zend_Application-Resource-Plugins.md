---
id: 231-Creating-Re-Usable-Zend_Application-Resource-Plugins
author: matthew
title: 'Creating Re-Usable Zend_Application Resource Plugins'
draft: false
public: true
created: '2010-02-04T14:55:12-05:00'
updated: '2010-02-08T10:11:47-05:00'
tags:
    0: php
    2: 'zend framework'
---
In my [last article](/blog/230-Quick-Start-to-Zend_Application_Bootstrap.html),
I wrote about how to get started with `Zend_Application`, including some
information about how to write resource methods, as well as listing available
resource plugins. What happens when you need a re-usable resource for which
there is no existing plugin shipped? Why, write your own, of course!

All plugins in Zend Framework follow a [common pattern](http://framework.zend.com/manual/en/learning.plugins.intro.html). Basically, you group plugins under a common directory, with a common class prefix, and then notify the pluggable class of their location.

For this post, let's consider that you may want a resource plugin to do the following:

- Set the view doctype
- Set the default page title and title separator

<!--- EXTENDED -->

Getting Started
---------------

First, let's determine the class prefix we want to use. If we follow [Zend Framework Coding Standards](http://framework.zend.com/manual/en/coding-standard.overview.html), we can leverage autoloading, while simultaneously ensuring a common class prefix for our resources.

For the purposes of this exercise, we'll use the class prefix `Phly_Resource`, located in `Phly/Resource/` on our `include_path`.

We'll call our particular resource "Layouthelpers", with a full class name of `Phly_Resource_Layouthelpers`, and place it in `Phly/Resource/Layouthelpers.php`. It needs to implement `Zend_Application_Resource_Resource`, but it's often even easier to extend `Zend_Application_Resource_ResourceAbstract`. In both cases, you need to define an `init()` method. Let's set up our skeleton accordingly:

```php
<?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
    }
}
```

On Dependency Tracking
----------------------

In my previous article, I showed an example of dependency tracking in
`Zend_Application`. We will need it in this exercise as well, as both of our
tasks operate on the view object, which we will retrieve via the View resource.

When creating resource methods directly in your bootstrap, you can simply call
`$this->getResource($name)`. However, within a plugin resource class, you need
to first get access to the bootstrap object itself — which you can do with the
`getBootstrap()` method.

Let's ensure the View resource is initialized, and retrieve it.

```php
<?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        // ...
    }
}
```

Configuring the resource
------------------------

Now that we've got our view object, we can do some work. Since we want the
resource to be re-usable, we should likely allow some configuration options.
`Zend_Application_Resource_ResourceAbstract` provides some boilerplate
functionality for doing so.

First, we'll provide some default options via the `$_options` property.

```php
<?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         => 'XHTML1_STRICT',
        'title'           => 'Site Title',
        'title_separator' => ' :: ',
    );

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        // ...
    }
}
```

We can then grab options using the `getOptions()` method.

```php
<?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         => 'XHTML1_STRICT',
        'title'           => 'Site Title',
        'title_separator' => ' :: ',
    );

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        $options = $this->getOptions();
        // ...
    }
}
```

Now, in configuration files, developers can override the defaults:

```ini
[production]
; ...
resources.layouthelpers.doctype = "HTML5"
resources.layouthelpers.title = "My Snazzy New Website"
resources.layouthelpers.title_separator = " &emdash; "
```

Doing some work
---------------

Now that we have the bits and pieces of naming and configuration out of the way, let's do some work:

```php
<?php
// Phly/Resource/Layouthelpers.php
//
class Phly_Resource_Layouthelpers 
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $_options = array(
        'doctype'         => 'XHTML1_STRICT',
        'title'           => 'Site Title',
        'title_separator' => ' :: ',
    );

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('View');
        $view = $bootstrap->getResource('View');

        $options = $this->getOptions();
        
        $view->doctype($options['doctype']);
        $view->headTitle()->setSeparator($options['title_separator'])
                          ->append($options['title']);
    }
}
```

And that's it!

Telling the Bootstrap about us
------------------------------

Well, that's it for the plugin resource, that is. But how do we tell our
bootstrap class about it? Via our configuration file, using the "pluginPaths"
key. This is an array, with the keys being plugin class prefixes, and the values
the path that corresponds to that prefix.

```ini
[production]
; ...
pluginPaths.Phly_Resource = "Phly/Resource"
resources.layouthelpers.doctype = "HTML5"
resources.layouthelpers.title = "My Snazzy New Website"
resources.layouthelpers.title_separator = " &emdash; "
```

You can register as many plugin paths as you desire. As this key is processed
before any resources are processed, it can also be defined at any time in your
configuration.

Further Considerations
----------------------

The example in this post was admittedly trivial. One aspect not discussed was
creating a resource that would be reused throughout your application. As an
example, you might want to create a resource you'll use at different times in
your application. If you return a value in your `init()` method, the bootstrap
object will store this for later retrieval. A good example of this we saw
earlier: the View resource registers a `Zend_View` object with the bootstrap
simply by returning the instance from its resource plugin.

Conclusions
-----------

Hopefully this post and the post prior have helped shed some light on
`Zend_Application`, and in particular, how to write and bootstrap resources.

If you have further questions, you can find me on the [ZF mailing
lists](http://framework.zend.com/archives), on IRC via the Freenode servers, or
on [twitter](http://twitter.com/weierophinney). Good luck!
