---
id: 267-Getting-started-writing-ZF2-modules
author: matthew
title: 'Getting started writing ZF2 modules'
draft: false
public: true
created: '2011-11-07T13:19:00-05:00'
updated: '2011-11-12T13:02:42-05:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
During [ZendCon](http://zendcon.com/) this year, we
[released 2.0.0beta1](http://framework.zend.com/zf2/blog/entry/Zend-Framework-2-0-0beta1-Released)
of [Zend Framework](http://framework.zend.com). The key story in the release is
the creation of a new MVC layer, and to sweeten the story, the addition of a
modular application architecture.

"Modular? What's that mean?" For ZF2, "modular" means that your application is
built of one or more "modules". In a lexicon agreed upon during our IRC
meetings, a module is a collection of code and other files that solves a
specific atomic problem of the application or website.

As an example, consider a typical corporate website in a technical arena. You
might have:

- A home page
- Product and other marketing pages
- Some forums
- A corporate blog
- A knowledge base/FAQ area
- Contact forms

These can be divided into discrete modules:

- A "pages" modules for the home page, product, and marketing pages
- A "forum" module
- A "blog" module
- An "faq" or "kb" module
- A "contact" module

Furthermore, if these are developed well and discretely, they can be *re-used* between different applications!

So, let's dive into ZF2 modules!

<!--- EXTENDED -->

What is a module?
-----------------

In ZF2, a module is simply a namespaced directory, with a single `Module` class
under it; no more, and no less, is required.

So, as an example:

```
modules/
    FooBlog/
        Module.php
    FooPages/
        Module.php
```

The above shows two modules, `FooBlog` and `FooPages`. The `Module.php` file
under each contains a single `Module` class, namespaced per the module:
`FooBlog\Module` and `FooPages\Module`, respectively.

This is the one and only requirement of modules; you can structure them however
you want from here. However, we *do* have a *recommended* directory structure:

```
modules/
    SpinDoctor/
        Module.php
        configs/
            module.config.php
        public/
            images/
            css/
                spin-doctor.css
            js/
                spin-doctor.js
        src/
            SpinDoctor/
                Controller/
                    SpinDoctorController.php
                    DiscJockeyController.php
                Form/
                    Request.php
        tests/
            bootstrap.php
            phpunit.xml
            SpinDoctor/
                Controller/
                    SpinDoctorControllerTest.php
                    DiscJockeyControllerTest.php
```

The important bits from above:

- Configuration goes in a `configs` directory.
- Public assets, such as javascript, CSS, and images, go in a `public` directory.
- PHP source code goes in a `src` directory; code under that directory should follow [PSR-0 standard structure](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).
- Unit tests should go in a `tests` directory, which should also contain your PHPUnit configuration and bootstrapping.

Again, the above is simply a *recommendation*. Modules in that structure clearly dileneate the purpose of each subtree, allowing developers to easily introspect them.

The Module class
----------------

Now that we've discussed the minimum requirements for creating a module and its
structure, let's discuss the minimum requirement: the `Module` class.

The module class, as noted previously, should exist in the module's namespace. Usually this will be equivalent to the module's directory name. Beyond that, however, there are no real requirements, other than the constructor should not require any arguments.

```php
namespace FooBlog;

class Module
{
}
```

So, what do module classes do, then?

The module manager (class `Zend\Module\Manager`) fulfills three key purposes:

- It aggregates the enabled modules (allowing you to loop over the classes manually).
- It aggregates configuration from each module.
- It triggers module initialization, if any.

I'm going to skip the first item and move directly to the configuration aspect.

Most applications require some sort of configuration. In an MVC application,
this may include routing information, and likely some dependency injection
configuration. In both cases, you likely don't want to configure anything until
you have the full configuration available — which means all modules must be loaded.

The module manager does this for you. It loops over all modules it knows about,
and then merges their configuration into a single configuration object. To do
this, it checks each Module class for a `getConfig()` method.

The `getConfig()` method simply needs to return an `array` or `Traversable`
object. This data structure should have "environments" at the top level — the
"production", "staging", "testing", and "development" keys that you're used to
with ZF1 and `Zend_Config`. Once returned, the module manager merges it with its
master configuration so you can grab it again later.

Typically, you should provide the following in your configuration:

- Dependency Injection configuration
- Routing configuration
- If you have module-specific configuration that falls outside those, the
  module-specific configuration. We recommend namespacing these keys after the
  module name: `foo_blog.apikey = "..."`

The easiest way to provide configuration? Define it as an array, and return it
from a PHP file — usually your `configs/module.config.php` file. Then your `getConfig()` method can be quite simple:

```php
public function getConfig()
{
    return include __DIR__ . '/configs/module.config.php';
}
```

In the original bullet points covering the purpose of the module manager, the
third bullet point was about module initialization. Quite often you may need to
provide additional initialization once the full configuration is known and the
application is bootstrapped — meaning the router and locator are primed and
ready. Some examples of things you might do:

- Setup event listeners. Often, these require configured objects, and thus need access to the locator.
- Configure plugins. Often, you may need to inject plugins with objects managed by the locator. As an example, the `url()` view helper needs a configured router in order to work.

The way to do these tasks is to subscribe to the bootstrap object's "bootstrap" event:

```php
$events = StaticEventManager::getInstance();
$events->attach('bootstrap', 'bootstrap', array($this, 'doMoarInit'));
```

That event gets the application and module manager objects as parameters, which
gives you access to everything you might possibly need.

The question is: where do I do this? The answer: the module manager will call a
Module class's `init()` method if found. So, with that in hand, you'll have the
following:

```php
namespace FooBlog;

use Zend\EventManager\StaticEventManager,
    Zend\Module\Manager as ModuleManager

class Module
{
    public function init(ModuleManager $manager)
    {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'doMoarInit'));
    }
    
    public function doMoarInit($e)
    {
        $application = $e->getParam('application');
        $modules     = $e->getParam('modules');
        
        $locator = $application->getLocator();
        $router  = $application->getRouter();
        $config  = $modules->getMergedConfig();
        
        // do something with the above!
    }
}
```

As you can see, when the bootstrap event is triggered, you have access to the
`Zend\Mvc\Application` instance as well as the `Zend\Module\Manager` instance,
giving you access to your configured locator and router, as well as merged
configuration from all modules! Basically, you have everything you could
possibly want to access right at your fingertips.

What else might you want to do during `init()`? One very, very important thing:
setup autoloading for the PHP classes in your module!

ZF2 offers several different autoloaders to provide different strategies geared
towards ease of development to production speed. For beta1, they were refactored
slightly to make them even more useful. The primary change was to the
`AutoloaderFactory`, to allow it to keep single instances of each autoloader it
handles, and thus allow specifying additional configuration for each. As such,
this means that if you use the `AutoloaderFactory`, you'll only ever have one
instance of a `ClassMapAutoloader` or `StandardAutoloader` — and this means each
module can simply add to their configuration.

As such, here's a typical autoloading boilerplate:

```php
namespace FooBlog;

use Zend\EventManager\StaticEventManager,
    Zend\Loader\AutoloaderFactory,
    Zend\Module\Manager as ModuleManager

class Module
{
    public function init(ModuleManager $manager)
    {
        $this->initializeAutoloader();
        // ...
    }
    
    public function initializeAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                include __DIR__ .  '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' .  __NAMESPACE__,
                ),
            ),
        ));
    }
```

During development, you can have `autoload_classmap.php` return an empty array,
but then during production, you can generate it based on the classes in your
module. By having the `StandardAutoloader` in place, you have a backup solution
until the classmap is updated.

Now that you know how your module can provide configuration, and how it can tie
into bootstrapping, I can finally cover the original point: the module manager
aggregates enabled modules. This allows modules to "opt-in" to additional
features of an application. As an example, you could make modules "ACL aware",
and have a "security" module grab module-specific ACLs:

```php
   public function initializeAcls($e)
   {
       $this->acl = new Acl;
       $modules   = $e->getParam('modules');
       foreach ($modules->getLoadedModules() as $module) {
           if (!method_exists($module, 'getAcl')) {
               continue;
           }
           $this->processModuleAcl($module->getAcl());
       }
   }
```

This is an immensely powerful technique, and I'm sure we'll see a lot of
creative uses for it in the future!

Composing modules into your application
---------------------------------------

So, writing modules should be easy, right? Right?!?!?

The other trick, then, is telling the module manager about your modules. There's
a reason I've used phrases like, "enabled modules" "modules it [the module
manager] knows about," and such: the module manager is opt-in. You have to
*tell* it what modules it will load.

Some may say, "Why? Isn't that against rapid application development?" Well, yes
and no. Consider this: what if you discover a security issue in a module? You
could remove it entirely from the repository, sure. Or you could simply update
the module manager configuration so it doesn't load it, and then start testing
and patching it in place; when done, all you need to do is re-enable it.

Loading modules is a two-stage process. First, the system needs to know where
and how to locate module classes. Second, it needs to actually load them. We
have two components surrounding this:

- `Zend\Loader\ModuleAutoloader`
- `Zend\Module\Manager`

The `ModuleAutoloader` takes a list of paths, or associations of module names to
paths, and uses that information to resolve `Module` classes. Often, modules
will live under a single directory, and configuration is as simple as this:

```php
$loader = new Zend\Loader\ModuleAutoloader(array(
    __DIR__ . '/../modules',
));
$loader->register();
```

You can specify multiple paths, or explicit module:directory pairs:

```php
$loader = new Zend\Loader\ModuleAutoloader(array(
    __DIR__ . '/../vendors',
    __DIR__ . '/../modules',
    'User' => __DIR__ . '/../vendors/EdpUser-0.1.0',
));
$loader->register();
```

In the above, the last will look for a `User\Module` class in the file
`vendors/EdpUser-0.1.0/Module.php`, but expect that modules found in the other
two directories specified will always have a 1:1 correlation between the
directory name and module namespace.

Once you have your `ModuleAutoloader` in place, you can invoke the module
manager, and inform it of what modules it should load. Let's say that we have
the following modules:

```
modules/
    Application/
        Module.php
    Security/
        Module.php
vendors/
    FooBlog/
        Module.php
    SpinDoctor/
        Module.php
```

and we wanted to load the `Application`, `Security`, and `FooBlog` modules.
Let's also assume we've configured the `ModuleAutoloader` correctly already. We
can then do this:

```php
$manager = new Zend\Module\Manager(array(
    'Application',
    'Security',
    'FooBlog',
));
$manager->loadModules();
```

We're done! If you were to do some profiling and introspection at this point,
you'd see that the "SpinDoctor" module will not be represented — only those
modules we've configured.

To make the story easy and reduce boilerplate, the
[ZendSkeletonApplication](https://github.com/zendframework/ZendSkeletonApplication)
repository provides a basic bootstrap for you in `public/index.php`. This file
consumes `configs/application.config.php`, in which you specify two keys,
`module_paths` and `modules`:

```php
return array(
    'module_paths' => array(
        realpath(__DIR__ . '/../modules'),
        realpath(__DIR__ . '/../vendors'),
    ),
    'modules' => array(
        'Application',
        'Security',
        'FooBlog',
    ),
);
```

It doesn't get much simpler at this point.

Tips and Tricks
---------------

One trick I've learned deals with how and when modules are loaded. In the
previous section, I introduced the module manager and how it's notified of what
modules we're composing in this application. One interesting thing is that
modules are processed in the order in which they are provided in your
configuration. This means that the configuration is merged in that order as
well.

The trick then, is this: if you want to override configuration settings, don't
do it in the modules; create a special module that loads last to do it!

So, consider this module class:

```php
namespace Local;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/configs/module.config.php';
    }
}
```

We then create a configuration file in `configs/module.config.php`, and specify
any configuration overrides we want there!

```php
return array(
    'production' => array(
        'di' => 'alias' => array(
            'view' => 'My\Custom\Renderer',
        ),
    ),
);
```

Then, in our `configs/application.config.php`, we simply enable this module as
the last in our list:

```php
return array(
    // ...
    'modules' => array(
        'Application',
        'Security',
        'FooBlog',
        'Local',
    ),
);
```

Done!

Fin
---

Modules in ZF2 are incredibly flexible and powerful. I didn't even cover some of
the features — such as the ability to use phar files (or any format phar
supports) as modules, or the ability to cache module configuration, etc.
Hopefully, however, I've outlined their simplicity for you, so you can start
harnessing their power for yourself!

### Disclaimer

ZF2 is in beta stage at this time, and Zend Framework is not guaranteeing BC
between beta releases. If you choose to test or build on ZF2, be aware that you
may need to make changes between releases. That said, please *do* test, and
provide your feedback!

### Updates

- **2011-11-07 14:30 CST**: Updated config `FooBlog.apikey` to read `foo_blog.apikey`, per ZF2 config naming standards
