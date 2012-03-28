<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('267-Getting-started-writing-ZF2-modules');
$entry->setTitle('Getting started writing ZF2 modules');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1320689940);
$entry->setUpdated(1321120962);
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
During <a href="http://zendcon.com/">ZendCon</a> this year, we <a href="http://framework.zend.com/zf2/blog/entry/Zend-Framework-2-0-0beta1-Released">released 2.0.0beta1</a> of <a href="http://framework.zend.com">Zend Framework</a>.
The key story in the release is the creation of a new MVC layer, and to sweeten
the story, the addition of a modular application architecture.
</p>

<p>
"Modular? What's that mean?" For ZF2, "modular" means that your application is
built of one or more "modules". In a lexicon agreed upon during our IRC
meetings, a module is a collection of code and other files that solves a
specific atomic problem of the application or website. 
</p>

<p>
As an example, consider a typical corporate website in a technical arena. You
might have:
</p>

<ul>
<li>
A home page
</li>
<li>
Product and other marketing pages
</li>
<li>
Some forums
</li>
<li>
A corporate blog
</li>
<li>
A knowledge base/FAQ area
</li>
<li>
Contact forms
</li>
</ul>

<p>
These can be divided into discrete modules:
</p>

<ul>
<li>
A "pages" modules for the home page, product, and marketing pages
</li>
<li>
A "forum" module
</li>
<li>
A "blog" module
</li>
<li>
An "faq" or "kb" module
</li>
<li>
A "contact" module
</li>
</ul>

<p>
Furthermore, if these are developed well and discretely, they can be <em>re-used</em>
between different applications!
</p>

<p>
So, let's dive into ZF2 modules!
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2 id="toc_1.2">What is a module?</h2>

<p>
In ZF2, a module is simply a namespaced directory, with a single "Module" class
under it; no more, and no less, is required.
</p>

<p>
So, as an example:
</p>

<pre>
modules/
    FooBlog/
        Module.php
    FooPages/
        Module.php
</pre>

<p>
The above shows two modules, "FooBlog" and "FooPages". The "Module.php" file
under each contains a single "Module" class, namespaced per the module:
<code>FooBlog\Module</code> and <code>FooPages\Module</code>, respectively.
</p>

<p>
This is the one and only requirement of modules; you can structure them however
you want from here. However, we <em>do</em> have a <em>recommended</em> directory structure:
</p>

<pre>
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
</pre>

<p>
The important bits from above:
</p>

<ul>
<li>
Configuration goes in a "configs" directory.
</li>
<li>
Public assets, such as javascript, CSS, and images, go in a "public"
   directory.
</li>
<li>
PHP source code goes in a "src" directory; code under that directory should
   follow <a href="https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md">PSR-0 standard structure</a>.
</li>
<li>
Unit tests should go in a "tests" directory, which should also contain your
   PHPUnit configuration and bootstrapping.
</li>
</ul>

<p>
Again, the above is simply a <em>recommendation</em>. Modules in that structure clearly
dileneate the purpose of each subtree, allowing developers to easily introspect
them.
</p>

<h2 id="toc_1.3">The Module class</h2>

<p>
Now that we've discussed the minimum requirements for creating a module and its
structure, let's discuss the minimum requirement: the Module class.
</p>

<p>
The module class, as noted previously, should exist in the module's namespace.
Usually this will be equivalent to the module's directory name. Beyond that,
however, there are no real requirements, other than the constructor should not
require any arguments.
</p>

<div class="example"><pre><code lang="php">
namespace FooBlog;

class Module
{
}
</code></pre></div>

<p>
So, what do module classes do, then? 
</p>

<p>
The module manager (class <code>Zend\Module\Manager</code>) fulfills three key purposes:
</p>

<ul>
<li>
It aggregates the enabled modules (allowing you to loop over the classes
   manually).
</li>
<li>
It aggregates configuration from each module.
</li>
<li>
It triggers module initialization, if any.
</li>
</ul>

<p>
I'm going to skip the first item and move directly to the configuration aspect.
</p>

<p>
Most applications require some sort of configuration. In an MVC application,
this may include routing information, and likely some dependency injection
configuration. In both cases, you likely don't want to configure anything until
you have the full configuration available -- which means all modules must be
loaded.
</p>

<p>
The module manager does this for you. It loops over all modules it knows about,
and then merges their configuration into a single configuration object. To do
this, it checks each Module class for a <code>getConfig()</code> method.
</p>

<p>
The <code>getConfig()</code> method simply needs to return an <code>array</code> or <code>Traversable</code>
object. This data structure should have "environments" at the top level -- the
"production", "staging", "testing", and "development" keys that you're used to
with ZF1 and <code>Zend_Config</code>. Once returned, the module manager merges it with its
master configuration so you can grab it again later.
</p>

<p>
Typically, you should provide the following in your configuration:
</p>

<ul>
<li>
Dependency Injection configuration
</li>
<li>
Routing configuration
</li>
<li>
If you have module-specific configuration that falls outside those, the
   module-specific configuration. We recommend namespacing these keys after the
   module name: <code>foo_blog.apikey = "..."</code>
</li>
</ul>

<p>
The easiest way to provide configuration? Define it as an array, and return it
from a PHP file -- usually your <code>configs/module.config.php</code> file. Then your
<code>getConfig()</code> method can be quite simple:
</p>

<div class="example"><pre><code lang="php">
public function getConfig()
{
    return include __DIR__ . '/configs/module.config.php';
}
</code></pre></div>

<p>
In the original bullet points covering the purpose of the module manager, the
third bullet point was about module initialization. Quite often you may need to
provide additional initialization once the full configuration is known and the
application is bootstrapped -- meaning the router and locator are primed and
ready. Some examples of things you might do:
</p>

<ul>
<li>
Setup event listeners. Often, these require configured objects, and thus need
   access to the locator.
</li>
<li>
Configure plugins. Often, you may need to inject plugins with objects managed
   by the locator. As an example, the <code>url()</code> view helper needs a configured
   router in order to work.
</li>
</ul>

<p>
The way to do these tasks is to subscribe to the bootstrap object's "bootstrap"
event:
</p>

<div class="example"><pre><code lang="php">
$events = StaticEventManager::getInstance();
$events-&gt;attach('bootstrap', 'bootstrap', array($this, 'doMoarInit'));
</code></pre></div>

<p>
That event gets the application and module manager objects as parameters, which
gives you access to everything you might possibly need.
</p>

<p>
The question is: where do I do this? The answer: the module manager will call a
Module class's <code>init()</code> method if found. So, with that in hand, you'll have the
following:
</p>

<div class="example"><pre><code lang="php">
namespace FooBlog;

use Zend\EventManager\StaticEventManager,
    Zend\Module\Manager as ModuleManager

class Module
{
    public function init(ModuleManager $manager)
    {
        $events = StaticEventManager::getInstance();
        $events-&gt;attach('bootstrap', 'bootstrap', array($this, 'doMoarInit'));
    }
    
    public function doMoarInit($e)
    {
        $application = $e-&gt;getParam('application');
        $modules     = $e-&gt;getParam('modules');
        
        $locator = $application-&gt;getLocator();
        $router  = $application-&gt;getRouter();
        $config  = $modules-&gt;getMergedConfig();
        
        // do something with the above!
    }
}
</code></pre></div>

<p>
As you can see, when the bootstrap event is triggered, you have access to the
<code>Zend\Mvc\Application</code> instance as well as the <code>Zend\Module\Manager</code> instance,
giving you access to your configured locator and router, as well as merged
configuration from all modules! Basically, you have everything you could
possibly want to access right at your fingertips.
</p>

<p>
What else might you want to do during <code>init()</code>? One very, very important thing:
setup autoloading for the PHP classes in your module!
</p>

<p>
ZF2 offers several different autoloaders to provide different strategies geared
towards ease of development to production speed. For beta1, they were refactored
slightly to make them even more useful. The primary change was to the
<code>AutoloaderFactory</code>, to allow it to keep single instances of each autoloader it
handles, and thus allow specifying additional configuration for each. As such,
this means that if you use the <code>AutoloaderFactory</code>, you'll only ever have one
instance of a <code>ClassMapAutoloader</code> or <code>StandardAutoloader</code> -- and this means
each module can simply add to their configuration.
</p>

<p>
As such, here's a typical autoloading boilerplate:
</p>

<div class="example"><pre><code lang="php">
namespace FooBlog;

use Zend\EventManager\StaticEventManager,
    Zend\Loader\AutoloaderFactory,
    Zend\Module\Manager as ModuleManager

class Module
{
    public function init(ModuleManager $manager)
    {
        $this-&gt;initializeAutoloader();
        // ...
    }
    
    public function initializeAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' =&gt; array(
                include __DIR__ .  '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' =&gt; array(
                'namespaces' =&gt; array(
                    __NAMESPACE__ =&gt; __DIR__ . '/src/' .  __NAMESPACE__,
                ),
            ),
        ));
    }
</code></pre></div>
    
<p>
During development, you can have <code>autoload_classmap.php</code> return an empty array,
but then during production, you can generate it based on the classes in your
module. By having the <code>StandardAutoloader</code> in place, you have a backup solution
until the classmap is updated.
</p>

<p>
Now that you know how your module can provide configuration, and how it can tie
into bootstrapping, I can finally cover the original point: the module manager
aggregates enabled modules. This allows modules to "opt-in" to additional
features of an application. As an example, you could make modules "ACL aware",
and have a "security" module grab module-specific ACLs:
</p>

<div class="example"><pre><code lang="php">
    public function initializeAcls($e)
    {
        $this-&gt;acl = new Acl;
        $modules   = $e-&gt;getParam('modules');
        foreach ($modules-&gt;getLoadedModules() as $module) {
            if (!method_exists($module, 'getAcl')) {
                continue;
            }
            $this-&gt;processModuleAcl($module-&gt;getAcl());
        }
    }
</code></pre></div>

<p>
This is an immensely powerful technique, and I'm sure we'll see a lot of
creative uses for it in the future!
</p>

<h2 id="toc_1.4">Composing modules into your application</h2>

<p>
So, writing modules should be easy, right? Right?!?!?
</p>

<p>
The other trick, then, is telling the module manager about your modules. There's
a reason I've used phrases like, "enabled modules" "modules it [the module
manager] knows about," and such: the module manager is opt-in. You have to
<em>tell</em> it what modules it will load.
</p>

<p>
Some may say, "Why? Isn't that against rapid application development?" Well, yes
and no. Consider this: what if you discover a security issue in a module? You
could remove it entirely from the repository, sure. Or you could simply update
the module manager configuration so it doesn't load it, and then start testing
and patching it in place; when done, all you need to do is re-enable it.
</p>

<p>
Loading modules is a two-stage process. First, the system needs to know where
and how to locate module classes. Second, it needs to actually load them. We
have two components surrounding this:
</p>

<ul>
<li>
<code>Zend\Loader\ModuleAutoloader</code>
</li>
<li>
<code>Zend\Module\Manager</code>
</li>
</ul>

<p>
The <code>ModuleAutoloader</code> takes a list of paths, or associations of module names to
paths, and uses that information to resolve <code>Module</code> classes. Often, modules
will live under a single directory, and configuration is as simple as this:
</p>

<div class="example"><pre><code lang="php">
$loader = new Zend\Loader\ModuleAutoloader(array(
    __DIR__ . '/../modules',
));
$loader-&gt;register();
</code></pre></div>

<p>
You can specify multiple paths, or explicit module:directory pairs:
</p>

<div class="example"><pre><code lang="php">
$loader = new Zend\Loader\ModuleAutoloader(array(
    __DIR__ . '/../vendors',
    __DIR__ . '/../modules',
    'User' =&gt; __DIR__ . '/../vendors/EdpUser-0.1.0',
));
$loader-&gt;register();
</code></pre></div>

<p>
In the above, the last will look for a <code>User\Module</code> class in the file
<code>vendors/EdpUser-0.1.0/Module.php</code>, but expect that modules found in the other
two directories specified will always have a 1:1 correlation between the
directory name and module namespace.
</p>

<p>
Once you have your <code>ModuleAutoloader</code> in place, you can invoke the module
manager, and inform it of what modules it should load. Let's say that we have
the following modules:
</p>

<pre>
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
</pre>

<p>
and we wanted to load the "Application", "Security", and "FooBlog" modules.
Let's also assume we've configured the <code>ModuleAutoloader</code> correctly already. We
can then do this:
</p>

<div class="example"><pre><code lang="php">
$manager = new Zend\Module\Manager(array(
    'Application',
    'Security',
    'FooBlog',
));
$manager-&gt;loadModules();
</code></pre></div>

<p>
We're done! If you were to do some profiling and introspection at this point,
you'd see that the "SpinDoctor" module will not be represented -- only those
modules we've configured. 
</p>

<p>
To make the story easy and reduce boilerplate, the <a href="https://github.com/zendframework/ZendSkeletonApplication">ZendSkeletonApplication</a> repository provides a basic bootstrap for you in <code>public/index.php</code>. This file consumes <code>configs/application.config.php</code>, in which you specify two keys, "module_paths" and "modules":
</p>

<div class="example"><pre><code lang="php">
return array(
    'module_paths' =&gt; array(
        realpath(__DIR__ . '/../modules'),
        realpath(__DIR__ . '/../vendors'),
    ),
    'modules' =&gt; array(
        'Application',
        'Security',
        'FooBlog',
    ),
);
</code></pre></div>

<p>
It doesn't get much simpler at this point.
</p>

<h2 id="toc_1.5">Tips and Tricks</h2>

<p>
One trick I've learned deals with how and when modules are loaded. In the
previous section, I introduced the module manager and how it's notified of what
modules we're composing in this application. One interesting thing is that
modules are processed in the order in which they are provided in your
configuration. This means that the configuration is merged in that order as
well.
</p>

<p>
The trick then, is this: if you want to override configuration settings, don't
do it in the modules; create a special module that loads last to do it!
</p>

<p>
So, consider this module class:
</p>

<div class="example"><pre><code lang="php">
namespace Local;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/configs/module.config.php';
    }
}
</code></pre></div>

<p>
We then create a configuration file in <code>configs/module.config.php</code>, and specify
any configuration overrides we want there!
</p>

<div class="example"><pre><code lang="php">
return array(
    'production' =&gt; array(
        'di' =&gt; 'alias' =&gt; array(
            'view' =&gt; 'My\Custom\Renderer',
        ),
    ),
);
</code></pre></div>

<p>
Then, in our <code>configs/application.config.php</code>, we simply enable this module as
the last in our list:
</p>

<div class="example"><pre><code lang="php">
return array(
    // ...
    'modules' =&gt; array(
        'Application',
        'Security',
        'FooBlog',
        'Local',
    ),
);
</code></pre></div>

<p>
Done!
</p>

<h2 id="toc_1.6">Fin</h2>

<p>
Modules in ZF2 are incredibly flexible and powerful. I didn't even cover some of
the features -- such as the ability to use phar files (or any format phar
supports) as modules, or the ability to cache module configuration, etc.
Hopefully, however, I've outlined their simplicity for you, so you can start
harnessing their power for yourself!
</p>

<h3 id="toc_1.6.1">Disclaimer</h3>

<p>
ZF2 is in beta stage at this time, and Zend Framework is not guaranteeing BC
between beta releases. If you choose to test or build on ZF2, be aware that you
may need to make changes between releases. That said, please <em>do</em> test, and
provide your feedback!
</p>

<h3 id="toc_1.6.2">Updates</h3>
<ul>
    <li><b>2011-11-07 14:30 CST</b>: Updated config FooBlog.apikey to read foo_blog.apikey, per ZF2 config naming standards</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;