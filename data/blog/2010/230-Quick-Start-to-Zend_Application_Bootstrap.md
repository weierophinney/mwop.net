---
id: 230-Quick-Start-to-Zend_Application_Bootstrap
author: matthew
title: 'Quick Start to Zend_Application_Bootstrap'
draft: false
public: true
created: '2010-01-08T12:57:20-05:00'
updated: '2010-04-02T07:44:57-04:00'
tags:
    - php
    - 'zend framework'
---
We added
[Zend_Application](http://framework.zend.com/manual/en/zend.application.html) to
Zend Framework starting in version 1.8.0. The intent behind the component was to
formalize the application bootstrapping process, and provide a simplified,
configuration-driven mechanism for it.

`Zend_Application` works in conjunction with `Zend_Application_Bootstrap`,
which, as you might guess from its name, is what really does the bulk of the
work for bootstrapping your application. It allows you to utilize plugin
bootstrap resources, or define local bootstrap resources as class methods. The
former allow for re-usability, and the latter for application-specific
initialization and configuration.

Additionally, `Zend_Application_Bootstrap` provides for dependency tracking
(i.e., if one resource depends on another, you can ensure that that other
resource will be executed first), and acts as a repository for initialized
resources. This means that once a resource has been bootstrapped, you can
retrieve it later from the bootstrap itself.

<!--- EXTENDED -->

How it works
------------

Now that you know what it does, let's jump into the basics.

If you use the `zf` command-line tool provided with Zend Framework to generate
your project (`zf create project`), you'll get a bootstrap and a default
configuration right out of the gate. This includes the following files in the
tree:

```
application/
|-- Bootstrap.php
|   `-- configs/
|   |   `-- application.ini
```

The `Bootstrap.php` file will contain the class `Bootstrap` which extends
`Zend_Application_Bootstrap_Bootstrap`; this class will be empty at first. The
`application.ini` file will contain the following:

```ini
[production]
phpSettings.display_startup_errors = 0
phpSettings.display_errors = 0
includePaths.library = APPLICATION_PATH "/../library"
bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
bootstrap.class = "Bootstrap"
appnamespace = "Application"
resources.frontController.controllerDirectory = APPLICATION_PATH "/controllers"
resources.frontController.params.displayExceptions = 0

[staging : production]

[testing : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1

[development : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
resources.frontController.params.displayExceptions = 1
```

`Zend_Application` runs in three stages. First, it initializes the PHP
environment, using INI settings from your configuration if provided, and setting
up the `include_path` and autoloading. Second, it initializes and executes the
bootstrap class. Finally, it then "runs" the application (by calling the
bootstrap's `run()` method).

Configuration Settings
----------------------

What we see in the above listing is a set of:

- PHP initialization settings (here, they indicate whether or not to display errors)
- `include_path` settings
- Settings that indicate the name and location of the bootstrap class
- Application resource settings

The `phpSettings` key accepts any `php.ini` keys as subkeys, and these key/value
pairs will be passed to `ini_set`. This can be useful when you need to either
ensure specific INI settings are made, particularly when you want them to vary
based on environment. (In the example above, `display_errors` is enabled in
testing and development, but disabled otherwise.)

When it comes to the `include_path` and autoloading, probably the most often
asked question is, "How do I add namespace prefixes for code other than ZF to
the autoloader?" This can be done easily in the configuration file using the
`autoloaderNamespaces` key, and appending namespace prefixes to it:

```ini
autoloaderNamespaces[] = "Phly_"
```

Regarding the bootstrap class and file location, typically the defaults will be
fine. However, if you want to specify a custom name — for instance, to provide a
class prefix — or perhaps if your default module is in a subdirectory, you can
notify `Zend_Application` of this via the `bootstrap.class` and `boostrap.path`
settings:

```ini
bootstrap.class = "Application_Bootstrap"
bootstrap.path = APPLICATION_PATH "/modules/application/Bootstrap.php"
```

Getting started with Bootstrap Resources
----------------------------------------

Now we finally get to the true fun: the bootstrap resources themselves.

*Yes, I'm aware I'm glossing over the "appnamespace" setting; I'l cover that at another time.*

Bootstrap resources may be one of two things:

- A protected method in the bootstrap class prefixed with `_init`; e.g., `protected function _initFoo()`
- A class implementing `Zend_Application_Resource_Resource`

In the former case, `_init*()` methods, each will be executed in each request.
In the latter, only those that you specify in your configuration will be
executed, allowing you to selectively choose which of the various shipped
resource plugins (or those you have written yourself!) will be used.

In the case of the default configuration, only the "frontcontroller" resource
plugin will be used, corresponding to
`Zend_Application_Resource_Frontcontroller`. As of the upcoming 1.10 release,
you can pick and choose from the following additional resource plugins as well:

- Cachemanager
- Db
- Dojo
- Layout
- Locale
- Log
- Mail
- Modules
- Multidb
- Navigation
- Router
- Session
- Translate
- View

Each has its own configuration options, [documented in the manual](http://framework.zend.com/manual/en/zend.application.available-resources.html).

Writing Resource Methods
------------------------

Writing your own resource methods is trivial: you simply create the method, and
do some work. You then have the option of returning a value; if you do, it will
be stored within the bootstrap so that you may retrieve it later. As an example:

```php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRegistry()
    {
        $registry = new Zend_Registry();
        return $registry;
    }
}
```

If we wanted to retrieve the registry later, we could do so using the bootstrap's `getResource()` method:

```php
$registry = $bootstrap->getResource('Registry');
```

Note that we pass the name of the method *minus* the `_init` prefix; this "short
name" is how the resource is referred to within the bootstrap, and how you will
refer to it later.

Now, let's say you have a resource that *depends* on your "Registry" resource;
for instance, let's say you want to create a `Zend_Currency` object, and pass it
to the registry. `Zend_Application_Bootstrap` was designed to handle this very
situation, and institutes some powerful dependency tracking (this is, in fact,
why the initialization methods are protected; it prevents them being called
directly). Simply call the `bootstrap()` method with the name of the resource to
initialize. Additionally, the `getResource()` method can then be used to
retrieve the value registered for that resource. As an example:

```php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initCurrency()
    {
        $this->bootstrap('Registry');
        $registry = $this->getResource('Registry');

        $currency = new Zend_Currency('$');
        $registry['Zend_Currency'] = $currency;
        return $currency;
    }

    protected function _initRegistry()
    {
        $registry = new Zend_Registry();
        return $registry;
    }
}
```

What will happen is this:

- `Zend_Application` will call `bootstrap()` with no arguments, which loops through the internal resource methods first, and then any configured resource plugins.
- The bootstrap will execute the `_initCurrency()` method
- It sees the `bootstrap()` call, and executes it
- The `bootstrap()` call executes the `_initRegistry()` method, storing a `Zend_Registry` instance (which was returned from the method) internally on completion
- Execution of `_initCurrency()` resumes, starting with the `getResource()` call; this returns the `Zend_Registry` instance stored under that key in the bootstrap.
- Execution of `_initCurrency()` completes, and the bootstrap stores the returned `Zend_Currency` instance.
- The `bootstrap()` method then attempts to call the `_initRegistry()` method, but notes that it has already been executed, and thus moves on to execute resource plugins.

As you can see by now, the bootstrap functionality is quite flexible and
powerful, and provides a number of benefits immediately out of the box.

Until next time…
----------------

At this point, you should have enough to get started writing your own bootstrap
initialization resources. In coming weeks, I'll blog about how to build reusable
resource plugins, as well as discuss how bootstrapping fits into modular
applications.
