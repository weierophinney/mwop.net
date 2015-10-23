---
id: 248-Introducing-the-ZF2-Plugin-Broker
author: matthew
title: 'Introducing the ZF2 Plugin Broker'
draft: false
public: true
created: '2010-11-09T15:08:36-05:00'
updated: '2010-11-11T15:46:00-05:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
In Zend Framework 2.0, we're refactoring in a number of areas in order to
increase the consistency of the framework. One area we identified early is how
plugins are loaded.

The word "plugins" in Zend Framework applies to a number of items:

- Helpers (view helpers, action helpers)
- Application resources
- Filters and validators (particularly when applied to `Zend_Filter_Input` and `Zend_Form`)
- Adapters

In practically every case, we use a "short name" to name the plugin, in order to
allow loading it dynamically. This allows more concise code, as well as the
ability to configure the code in order to allow specifying alternate
implementations.

<!--- EXTENDED -->

Analysis
--------

Slightly before 1.0.0, we created the "PluginLoader", a class used to resolve
plugin names to their full class names. While this solution has worked
reasonably well, it's by no means perfect — far from it, in fact:

- It only handles class resolution, not actual class instantiation or
  persistence, which led to:
- Each component using it typically handled class instantiation and registration
  differently.
- Some components simply decided not to use the solution, either because it
  wasn't comprehensive enough, or because they needed to handle edge cases;
  which leads to:
- Case sensitivity issues. If the plugin name did not follow the original
  class casing, a variety of issues could occur; on case sensitive file
  systems, the plugin would not be found, and on case insensitive file
  systems, the plugin file would be found, but not the class — leading to
  inconsistency of errors. How a component handled plugin case sensitivity has
  also led to inconsistency in APIs.
- Stack resolution issues. Plugins are loaded in a stack as "prefix path"
  pairs… with each prefix potentially storing a stack of paths in which to
  look. Understanding which prefix and path will resolve can be difficult —
  particularly in the MVC where paths may be added automatically. This leads to
  a critical issue as well:
- Performance issues. The prefix/path solution requires system stat calls. In
  fact, in many cases, the same plugins will be loaded multiple times over the
  course of a single request, but because different objects are responsible, the
  same lookups and stat calls will be made multiple times. Stat calls are
  expensive; in fact, we've discovered that plugin loading is potentially the
  single most expensive operation across the framework!

Some examples of issues:

- Resources in `Zend_Application` are expected to be case insensitive. This has
  led to odd class names such as "Frontcontroller", "Cachemanager", etc.
- Many developers camelCase the "doctype" view helper name ("docType") — leading
  to errors.
- Since the default module allows registering either using the application
  prefix _or_ the `Zend_View_Helper` prefix, there are often conflicts as to
  which helper will be loaded.

The end result of these issues is an inconsistent approach to plugins in Zend
Framework that leads to critical performance degradation.

Introducing the PluginBroker
----------------------------

In analyzing the situation, we determined that the following responsibilities
should be, and can be, shared across components:

- Plugin class resolution
- Plugin class instantiation
- Plugin registry

Basically, we saw a number of design patterns, including Lazy Loading, Factory,
Builder, and Registry. We separated these into a number of interfaces in the
`Zend\Loader` namespace:

- ShortNameLocater
- Broker
- LazyLoadingBroker

The first interface, `ShortNameLocater`, describes the act of resolving a plugin
name to a class. Code will typically simply consume the interface, which
consists quite simply of methods to load (resolve) a class from a plugin name,
and check if a given plugin name has already been resolved.

The second, `Broker`, describes a class that does the following:

- Composes a `ShortNameLocater`
- Instantiates and Registers plugins

The last, `LazyLoadingBroker`, extends `Broker` and adds the capability to
pre-specify instantiation options as well as lists of plugins to load. Use cases
for this include `Zend\Application`, where you may want to configure a list of
resources to load, with optional instantiation options.

### Plugin Class Resolution

We are including two implementations of ShortNameLocater. The first replaces the
original `PluginLoader`, and is called `PrefixPathLoader`. Internally it has
been refactored to utilize `SplStack` and `SplFileInfo`, both of which are more
performant and work better cross-platform.

The second implementation, which is the standard now used in ZF2, is called
`PluginClassLoader`. It implements a very simple plugin/class hash mechanism,
allowing us to leverage the autoloader for lookups and return results quickly.
It also simplifies the story surrounding overriding plugins: you simply register
a different class for a given plugin name, which makes it very easy to search
for such cases in your code.

A simple `PluginClassLoader` extension might look like this:

```php
namespace Zend\Paginator;

use Zend\Loader\PluginClassLoader;

class AdapterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters 
     */
    protected $plugins = array(
        'array'           => 'Zend\Paginator\Adapter\ArrayAdapter',
        'db_select'       => 'Zend\Paginator\Adapter\DbSelect',
        'db_table_select' => 'Zend\Paginator\Adapter\DbTableSelect',
        'iterator'        => 'Zend\Paginator\Adapter\Iterator',
        'null'            => 'Zend\Paginator\Adapter\Null',
    );
}
```

This approach makes it simple to provide presets of expected plugins on a
per-component basis. To overload a definition (or create a new one), register
it:

```php
$loader->registerPlugin('array', 'Foo\Paginator\CustomArrayAdapter');
```

Because you may want to override certain plugin names globally in your
application, we also provide some static access via the `addStaticMap()` method.

```php
Zend\Paginator\AdapterLoader::addStaticMap(array(
    'array' => 'Foo\Paginator\CustomArrayAdapter',
));
```

Precedence is as follows:

- Explicitly registered maps (`registerPlugin()`, maps passed to constructor)
  always win, followed by
- Statically registered maps (`addStaticMap()`), followed by
- Maps defined in the class

Registering plugins, whether statically done or per-instance, overwrites that
instance's map entries — which means lookups are fast.

### Plugin Instantiation and Registration

The next piece of the puzzle after plugin class resolution is how to instantiate
and register plugin classes. As mentioned in the analysis, in ZF1, this is done
in an ad hoc fashion per-component. The `Broker` interface standardizes the
process. This interface defines the following:

```php
namespace Zend\Loader;

interface Broker
{
    public function load($plugin, array $options = null);
    public function getPlugins();
    public function isLoaded($name);
    public function register($name, $plugin);
    public function unregister($name);
    public function setClassLoader(ShortNameLocater $loader);
    public function getClassLoader();
}
```

The following benefits are gained:

- You can specify what arguments to pass to the constructor.
- You can register explicit instances of a plugin, as well as dynamically load
  them.
- If a plugin has been previously loaded by (or registered explicitly with) the
  current broker instance, it will be immediately returned.
- You can get a list of all loaded plugins (useful for determining application
  dependencies).
- You can specify what plugin class resolver you wish to use.

The `LazyLoadingBroker` implementation extends `Broker`, and adds the following methods:

```php
namespace Zend\Loader;

interface LazyLoadingBroker
{
    public function registerSpec($name, array $spec = null);
    public function registerSpecs($specs);
    public function unregisterSpec($name);
    public function getRegisteredPlugins();
    public function hasPlugin($name);
}
```

The idea behind `LazyLoadingBroker` is that you may want to specify what options
should be used when loading a particular plugin, but don't want to load it just
yet (or may not load it at all). Additionally, you may want to get a list of
plugins registered in this way — for instance, to iterate over them in order to
operate on each. The classic examples are application resources, and form
filters, validators, and decorators.

For now, I'm going to focus on the `PluginBroker` class, which is a generic
implementation of the `Broker` interface. It is designed to meet the needs of most
components that utilize plugins of some sort. By default, it will lazy-load an
empty `PluginClassLoader`, but allows you to specify the default. Additionally, it
provides a hook for validating registered plugins, to ensure consistency within
the component in which you are loading plugins.

This latter is the key to ensuring that the objects returned by the broker are
consistent in type. At the most basic, you can register any valid callback as a
validator via the `setValidator()` method; the easiest way is using a closure:

```php
$broker->setValidator(function($plugin) {
    if (!$plugin instanceof Plugin) {
        throw \RuntimeException('Invalid plugin');
    }
    return true;
});
```

Internally, however, The `register()` method calls a protected
`validatePlugin()` method, which will invoke the registered validator callback,
if any. This provides a nice extension point, which we utilize within the
framework.

As an example, the companion to the `Zend\Paginator\AdapterLoader` class above
is as follows:

```php
namespace Zend\Paginator;

use Zend\Loader\PluginBroker;

class AdapterBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Paginator\AdapterLoader';

    /**
     * Determine if we have a valid adapter
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws Exception
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Adapter) {
            throw new Exception\RuntimeException('Pagination adapters must implement Zend\Paginator\Adapter');
        }
        return true;
    }
}
```

This broker uses the `AdapterLoader` as its default class loader, and hooks into
`validatePlugin()` to test if the plugin instance is an `Adapter` instance; if
not, it raises an exception.

Within a class utilizing plugins, you would then set accessors and mutators for
retrieving and setting the PluginBroker instance, and then simply consume the
broker. As an example, the following lines in `Paginator` load and register the
appropriate adapter:

```php
// Assume $adapter is an adapter name, and $data is an array or object to pass
// to the constructor
$broker  = self::getAdapterBroker();
$adapter = $broker->load($adapter, array($data));
return new self($adapter);
```

This reduces a ton of code in that particular component — the implementation
went from several dozen lines to less than a dozen, and is more flexible.

Using this approach has some pros and cons. On the pro side, we reduce the
amount of code, while simultaneously providing a more flexible, injectible
solution. On the con side, you will typically hint on the `Broker` interface —
meaning that plugins not conforming to expected adapters may potentially be
used. We consider this an edge case, however, and feel that if you are doing
that, you likely know the issues involved.

### The PluginSpecBroker

The `PluginBroker` is used in most cases. However, there are a number of cases
where the following workflow is present:

- Object defines plugin specifications for plugins it will use at some point in the future.
- At that point, it loops through those specifications, lazy-loading the classes and utilizing them.

Examples, again, are `Zend\Application` resources, as well as (current
incarnation) form elements, decorators, validators, and filters. Another example
is `Zend\Filter\InputFilter`, which is often configured well before being used.

For these purposes, we defined the interface `LazyLoadingBroker`, which I detailed
earlier. A concrete implementation of this is the `PluginSpecBroker`, which
extends `PluginBroker` and implements `LazyLoadingBroker`. This is used almost
exactly like `PluginBroker`, with a few minor differences in workflow.

As noted, you typically will pre-configure this broker, allowing you to define
it early, likely from a configuration file.

As an example, you might have the following configuration:

```ini
resources.frontcontroller.module_directory = APPLICATION_PATH "/modules"
resources.view.encoding = "iso-8859-1"
resources.view.doctype = "html5"
resources.layout.layout_path = APPLICATION_PATH "/layouts/scripts/"
resources.layout.layout = "layout"
```

Configuration might be passed as follows:

```php
// in the Zend\Application namespace:
$broker = new ResourcesBroker($config->resources);
```

Then, at a later point, your code loops over these plugins, retrieves them, and
acts on them:

```php
foreach ($broker->getRegisteredPlugins() as $resource) {
    // do something with $resource...
}
```

In our case, we'd loop over the "frontcontroller", "view", and "layout"
resources, and each would be given the appropriate configuration.

If you were to loop multiple times, you get immediate benefits: the plugins are
already present and instantiated!

Status
------

We completed the "autoloading and plugin loading" milestone of ZF2 in the past
few weeks. This involved refactoring all places using the old PluginLoader
solution to use the new PluginBroker instead.

There are a few outliers, however:

- `Zend\Cache` is currently being refactored, and will either incorporate the
  change during this work, or when complete.
- `Zend\Form` still needs to be updated. However, we are considering using
  `ValidatorChain` and `FilterChain` objects (which will likely mean modifying
  these to implement `LazyLoadingBroker`), and we will also likely change how
  rendering of forms and elements will occur — which may mean elimination of
  that plugin broker need. As such, the only broker that may need to be in place
  is for elements themselves.

`Zend\View` was refactored to use `PluginBroker` and `FilterChain`. In fact, a ton
of functionality was refactored in `Zend\View`, and there will be more to occur
during the MVC milestone.

Synopsis
--------

In closing the Autoloading/PluginLoading milestone of ZF2, we've accomplished an
important goal of improving consistency in the framework, while simultaneously
also improving performance of the framework. Early benchmarks show that using
the new autoloading system in conjunction with the plugin broker system as
outlined in this post, we gain anywhere between 7- and 20-fold increases in
performance. Let that sink in for a moment. The basic functionality remains the
same, with simply some minor API changes in how plugins are retrieved — but with
those changes, we can have a major improvement in framework performance. As far
as I'm concerned, this is a win-win situation.

You can check out ZF2 status by following our [GitHub repository](http://github.com/zendframework/zf2) or
[downloading the 2.0.0dev2 snapshot](http://framework.zend.com/announcements/2010-11-03-zf2dev2).
