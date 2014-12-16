<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('248-Introducing-the-ZF2-Plugin-Broker');
$entry->setTitle('Introducing the ZF2 Plugin Broker');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1289333316);
$entry->setUpdated(1289508360);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zf2',
));

$body =<<<'EOT'
<p>
In Zend Framework 2.0, we're refactoring in a number of areas in order to
increase the consistency of the framework. One area we identified early is how
plugins are loaded.
</p>

<p>
The word "plugins" in Zend Framework applies to a number of items:
</p>

<ul>
    <li> Helpers (view helpers, action helpers)</li>
    <li> Application resources</li>
    <li> Filters and validators (particularly when applied to Zend_Filter_Input and Zend_Form)</li>
    <li> Adapters</li>
</ul>

<p>
In practically every case, we use a "short name" to name the plugin, in order
to allow loading it dynamically. This allows more concise code, as well as the
ability to configure the code in order to allow specifying alternate
implementations. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2> Analysis </h2>

<p>
Slightly before 1.0.0, we created the "PluginLoader", a class used to resolve
plugin names to their full class names. While this solution has worked
reasonably well, it's by no means perfect -- far from it, in fact:
</p>

<ul>
    <li> It only handles class resolution, not actual class instantiation or persistence, which led to:</li>
    <li> Each component using it typically handled class instantiation and registration differently.</li>
    <li> Some components simply decided not to use the solution, either because it wasn't comprehensive enough, or because they needed to handle edge cases; which leads to:</li>
    <li> Case sensitivity issues. If the plugin name did not follow the original class casing, a variety of issues could occur; on case sensitive file systems, the plugin would not be found, and on case insensitive file systems, the plugin file would be found, but not the class -- leading to inconsistency of errors. How a component handled plugin case sensitivity has also led to inconsistency in APIs.</li>
    <li> Stack resolution issues. Plugins are loaded in a stack as "prefix path" pairs... with each prefix potentially storing a stack of paths in which to look. Understanding which prefix and path will resolve can be difficult -- particularly in the MVC where paths may be added automatically. This leads to a critical issue as well:</li>
    <li> Performance issues. The prefix/path solution requires system stat calls. In fact, in many cases, the same plugins will be loaded multiple times over the course of a single request, but because different objects are responsible, the same lookups and stat calls will be made multiple times. Stat calls are expensive; in fact, we've discovered that plugin loading is potentially the single most expensive operation across the framework!</li>
</ul>

<p>
Some examples of issues:
</p>

<ul>
    <li> Resources in Zend_Application are expected to be case insensitive. This has led to odd class names such as "Frontcontroller", "Cachemanager", etc.</li>
    <li> Many developers camelCase the "doctype" view helper name ("docType") -- leading to errors.</li>
    <li> Since the default module allows registering either using the application prefix _or_ the "Zend_View_Helper" prefix, there are often conflicts as to which helper will be loaded.</li>
</ul>

<p>
The end result of these issues is an inconsistent approach to plugins in Zend Framework that leads to critical performance degradation.
</p>

<h2> Introducing the PluginBroker </h2>

<p>
In analyzing the situation, we determined that the following responsibilities
should be, and can be, shared across components:
</p>

<ul>
    <li> Plugin class resolution</li>
    <li> Plugin class instantiation</li>
    <li> Plugin registry</li>
</ul>

<p>
Basically, we saw a number of design patterns, including Lazy Loading,
Factory, Builder, and Registry. We separated these into a number of interfaces
in the Zend\Loader namespace:
</p>

<ul>
    <li> ShortNameLocater</li>
    <li> Broker</li>
    <li> LazyLoadingBroker</li>
</ul>

<p>
The first interface, ShortNameLocater, describes the act of resolving a plugin name to a class. Code will typically simply consume the interface, which consists quite simply of methods to load (resolve) a class from a plugin name, and check if a given plugin name has already been resolved.
</p>

<p>
The second, Broker, describes a class that does the following:
</p>

<ul>
<li> Composes a ShortNameLocater</li>
<li> Instantiates and Registers plugins</li>
</ul>

<p>
The last, LazyLoadingBroker, extends Broker and adds the capability to pre-specify instantiation options as well as lists of plugins to load. Use cases for this include Zend\Application, where you may want to configure a list of resources to load, with optional instantiation options.
</p>

<h3> Plugin Class Resolution </h3>

<p>
We are including two implementations of ShortNameLocater. The first replaces
the original "PluginLoader", and is called "PrefixPathLoader". Internally it
has been refactored to utilize SplStack and SplFileInfo, both of which are more
performant and work better cross-platform. 
</p>

<p>
The second implementation, which is the standard now used in ZF2, is called
"PluginClassLoader". It implements a very simple plugin/class hash mechanism,
allowing us to leverage the autoloader for lookups and return results quickly.
It also simplifies the story surrounding overriding plugins: you simply
register a different class for a given plugin name, which makes it very easy to
search for such cases in your code.
</p>

<p>
A simple PluginClassLoader extension might look like this:
</p>

<div class="example"><pre><code class="language-php">
namespace Zend\Paginator;

use Zend\Loader\PluginClassLoader;

class AdapterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters 
     */
    protected $plugins = array(
        'array'           =&gt; 'Zend\Paginator\Adapter\ArrayAdapter',
        'db_select'       =&gt; 'Zend\Paginator\Adapter\DbSelect',
        'db_table_select' =&gt; 'Zend\Paginator\Adapter\DbTableSelect',
        'iterator'        =&gt; 'Zend\Paginator\Adapter\Iterator',
        'null'            =&gt; 'Zend\Paginator\Adapter\Null',
    );
}
</code></pre></div>

<p>
This approach makes it simple to provide presets of expected plugins on a
per-component basis. To overload a definition (or create a new one), register it:
</p>

<div class="example"><pre><code class="language-php">
$loader-&gt;registerPlugin('array', 'Foo\Paginator\CustomArrayAdapter');
</code></pre></div>

<p>
Because you may want to override certain plugin names globally in your
application, we also provide some static access via the <code>addStaticMap()</code>
method. 
</p>

<div class="example"><pre><code class="language-php">
Zend\Paginator\AdapterLoader::addStaticMap(array(
    'array' =&gt; 'Foo\Paginator\CustomArrayAdapter',
));
</code></pre></div>

<p>
Precedence is as follows:
</p>

<ul>
    <li> Explicitly registered maps (<code>registerPlugin()</code>, maps passed to constructor) always win, followed by</li>
    <li> Statically registered maps (<code>addStaticMap()</code>), followed by</li>
    <li> Maps defined in the class</li>
</ul>

<p>
Registering plugins, whether statically done or per-instance, overwrites that
instance's map entries -- which means lookups are fast.
</p>

<h3> Plugin Instantiation and Registration </h3>

<p>
The next piece of the puzzle after plugin class resolution is how to
instantiate and register plugin classes. As mentioned in the analysis, in ZF1,
this is done in an ad hoc fashion per-component. The Broker interface
standardizes the process. This interface defines the following:
</p>

<div class="example"><pre><code class="language-php">
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
</code></pre></div>

<p>
The following benefits are gained:
</p>

<ul>
    <li> You can specify what arguments to pass to the constructor.</li>
    <li> You can register explicit instances of a plugin, as well as dynamically load them.</li>
    <li> If a plugin has been previously loaded by (or registered explicitly with) the current broker instance, it will be immediately returned.</li>
    <li> You can get a list of all loaded plugins (useful for determining application dependencies).</li>
    <li> You can specify what plugin class resolver you wish to use.</li>
</ul>

<p>
The LazyLoadingBroker implementation extends Broker, and adds the following
methods:
</p>

<div class="example"><pre><code class="language-php">
namespace Zend\Loader;

interface LazyLoadingBroker
{
    public function registerSpec($name, array $spec = null);
    public function registerSpecs($specs);
    public function unregisterSpec($name);
    public function getRegisteredPlugins();
    public function hasPlugin($name);
}
</code></pre></div>

<p>
The idea behind LazyLoadingBroker is that you may want to specify what options
should be used when loading a particular plugin, but don't want to load it just
yet (or may not load it at all). Additionally, you may want to get a list of
plugins registered in this way -- for instance, to iterate over them in order
to operate on each. The classic examples are application resources, and form
filters, validators, and decorators.
</p>

<p>
For now, I'm going to focus on the PluginBroker class, which is a generic
implementation of the Broker interface. It is designed to meet the needs of
most components that utilize plugins of some sort. By default, it will
lazy-load an empty PluginClassLoader, but allows you to specify the default.
Additionally, it provides a hook for validating registered plugins, to ensure
consistency within the component in which you are loading plugins.
</p>

<p>
This latter is the key to ensuring that the objects returned by the broker are
consistent in type. At the most basic, you can register any valid callback as a
validator via the <code>setValidator()</code> method; the easiest way is using a closure:
</p>

<div class="example"><pre><code class="language-php">
$broker-&amp;gt;setValidator(function($plugin) {
    if (!$plugin instanceof Plugin) {
        throw \RuntimeException('Invalid plugin');
    }
    return true;
});
</code></pre></div>

<p>
Internally, however, The <code>register()</code> method calls a protected <code>validatePlugin()</code> method, which will invoke the registered validator callback, if any. This provides a nice extension point, which we utilize within the framework.
</p>

<p>
As an example, the companion to the Zend\Paginator\AdapterLoader class above is
as follows:
</p>

<div class="example"><pre><code class="language-php">
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
</code></pre></div>

<p>
This broker uses the AdapterLoader as its default class loader, and hooks into
<code>validatePlugin()</code> to test if the plugin instance is an Adapter instance; if
not, it raises an exception.
</p>

<p>
Within a class utilizing plugins, you would then set accessors and mutators for
retrieving and setting the PluginBroker instance, and then simply consume the
broker. As an example, the following lines in Paginator load and register the
appropriate adapter:
</p>

<div class="example"><pre><code class="language-php">
// Assume $adapter is an adapter name, and $data is an array or object to pass
// to the constructor
$broker  = self::getAdapterBroker();
$adapter = $broker-&gt;load($adapter, array($data));
return new self($adapter);
</code></pre></div>

<p>
This reduces a ton of code in that particular component -- the implementation
went from several dozen lines to less than a dozen, and is more flexible.
</p>

<p>
Using this approach has some pros and cons. On the pro side, we reduce the
amount of code, while simultaneously providing a more flexible, injectible
solution. On the con side, you will typically hint on the Broker interface --
meaning that plugins not conforming to expected adapters may potentially be
used. We consider this an edge case, however, and feel that if you are doing
that, you likely know the issues involved.
</p>

<h3> The PluginSpecBroker </h3>

<p>
The PluginBroker is used in most cases. However, there are a number of cases
where the following workflow is present:
</p>

<ul>
    <li> Object defines plugin specifications for plugins it will use at some point in the future.</li>
    <li> At that point, it loops through those specifications, lazy-loading the classes and utilizing them.</li>
</ul>

<p>
Examples, again, are Zend\Application resources, as well as (current
incarnation) form elements, decorators, validators, and filters. Another
example is Zend\Filter\InputFilter, which is often configured well before being
used.
</p>

<p>
For these purposes, we defined the interface LazyLoadingBroker, which I
detailed earlier. A concrete implementation of this is the PluginSpecBroker,
which extends PluginBroker and implements LazyLoadingBroker. This is used
almost exactly like PluginBroker, with a few minor differences in workflow.
</p>

<p>
As noted, you typically will pre-configure this broker, allowing you to define
it early, likely from a configuration file.
</p>

<p>
As an example, you might have the following configuration:
</p>

<div class="example"><pre><code class="language-ini">
resources.frontcontroller.module_directory = APPLICATION_PATH \&quot;/modules\&quot;
resources.view.encoding = \&quot;iso-8859-1\&quot;
resources.view.doctype = \&quot;html5\&quot;
resources.layout.layout_path = APPLICATION_PATH \&quot;/layouts/scripts/\&quot;
resources.layout.layout = \&quot;layout\&quot;
</code></pre></div>

<p>
Configuration might be passed as follows:
</p>

<div class="example"><pre><code class="language-php">
// in the Zend\Application namespace:
$broker = new ResourcesBroker($config-&gt;resources);
</code></pre></div>

<p>
Then, at a later point, your code loops over these plugins, retrieves them, and
acts on them:
</p>

<div class="example"><pre><code class="language-php">
foreach ($broker-&gt;getRegisteredPlugins() as $resource) {
    // do something with $resource...
}
</code></pre></div>

<p>
In our case, we'd loop over the "frontcontroller", "view", and "layout"
resources, and each would be given the appropriate configuration.
</p>

<p>
If you were to loop multiple times, you get immediate benefits: the plugins are
already present and instantiated!
</p>

<h2> Status </h2>

<p>
We completed the "autoloading and plugin loading" milestone of ZF2 in the past
few weeks. This involved refactoring all places using the old PluginLoader
solution to use the new PluginBroker instead.
</p>

<p>
There are a few outliers, however:
</p>

<ul>
    <li> Zend\Cache is currently being refactored, and will either incorporate the change during this work, or when complete.</li>
    <li> Zend\Form still needs to be updated. However, we are considering using ValidatorChain and FilterChain objects (which will likely mean modifying these to implement LazyLoadingBroker), and we will also likely change how rendering of forms and elements will occur -- which may mean elimination of that plugin broker need. As such, the only broker that may need to be in place is for elements themselves.</li>
</ul>

<p>
Zend\View was refactored to use PluginBroker and FilterChain. In fact, a
ton of functionality was refactored in Zend\View, and there will be more to
occur during the MVC milestone.
</p>

<h2> Synopsis </h2>

<p>
In closing the Autoloading/PluginLoading milestone of ZF2, we've accomplished
an important goal of improving consistency in the framework, while
simultaneously also improving performance of the framework. Early benchmarks
show that using the new autoloading system in conjunction with the plugin
broker system as outlined in this post, we gain anywhere between 7- and 20-fold
increases in performance. Let that sink in for a moment. The basic
functionality remains the same, with simply some minor API changes in how
plugins are retrieved -- but with those changes, we can have a major
improvement in framework performance. As far as I'm concerned, this is a
win-win situation.
</p>

<p>
You can check out ZF2 status by following our <a href="http://github.com/zendframework/zf2">GitHub repository</a> or <a href="http://framework.zend.com/announcements/2010-11-03-zf2dev2">downloading the 2.0.0dev2 snapshot</a>.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
