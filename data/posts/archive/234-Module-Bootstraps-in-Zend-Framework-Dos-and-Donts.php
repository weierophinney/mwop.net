<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('234-Module-Bootstraps-in-Zend-Framework-Dos-and-Donts');
$entry->setTitle('Module Bootstraps in Zend Framework: Do\'s and Don\'ts');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1268326555);
$entry->setUpdated(1268699513);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I see a number of questions regularly about module bootstraps in Zend
    Framework, and decided it was time to write a post about them finally.
</p>

<p>
    In Zend Framework 1.8.0, we added <code>Zend_Application</code>, which is
    intended to (a) formalize the bootstrapping process, and (b) make it
    re-usable. One aspect of it was to allow bootstrapping of individual
    application modules -- which are discrete collections of controllers, views,
    and models.
</p>

<p>
    The most common question I get regarding module bootstraps is: 
</p>

<blockquote>
    Why are all module bootstraps run on every request, and not just the one for
    the requested module?
</blockquote>

<p>
    To answer that question, first I need to provide some background.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    When it comes to modules, we have three typical problems or requirements:
</p>

<ul>
    <li>Ensuring that module resources -- models, view helpers, etcc. -- are
        available elsewhere in the application</li>
    <li>Initializing module-specific resources, such as routes, navigation
        elements, etc.</li>
    <li>Running code specific to this module (selecting a specific layout,
        selecting a specific database adapter, etc)</li>
</ul>

<p>
    <code>Zend_Application</code> answers the first two questions. By default,
    it sets up a resource autoloader with targets for all the common resources
    (models, forms, view helpers and filters, DbTable objects, etc.), and also
    allows you to specify resources to load at bootstrap time.
</p>

<p>
    And that's where things get interesting.
</p>

<p>
    The basic workflow of a ZF MVC request is as follows:
</p>

<ol>
    <li>Application bootstrapping</li>
    <li>Routing</li>
    <li>Dispatch</li>
</ol>

<p>
    <code>Zend_Application</code> takes care of only the first item in that
    list, bootstrapping. At that time, we have no idea what the request actually
    is -- that happens during routing. It's only after we have routed that we
    know what module, controller, and action were requested.
</p>

<p>
    So, what's the point of your module bootstraps, then?
</p>

<h2>Bootstrapping is for getting ready</h2>

<p>
    As noted earlier, <code>Zend_Application</code> is intended for
    bootstrapping your application. This means "getting it ready to execute".
    The idea is to get all your dependencies in order so that once you're ready
    to route and/or dispatch, everything the application may need is in place.
</p>

<p>
    When it comes to modules, the sorts of things you need to have in place
    <em>before</em> routing and dispatch include:
</p>

<ul>
    <li>Autoloading support for module resources. This is so that, if you need
    to, code from anywhere in your application can make uses of the module's
    resources. Examples include access to view helpers, access to models, access
    to forms, etc. Autoloading of resources is enabled by default</li>

    <li>Setting up module-specific routes. How can you get to the module's
    controllers in the first place? What routes does it answer to? The time to
    provide this information is during bootstrapping, <em>before</em> routing
    occurs.</li>

    <li>Module-specific navigation elements. This usually goes hand-in-hand with
    your routes (most <code>Zend_Navigation</code> pages utilize named
    routes).</li>

    <li>Setting up module-specific plugins. If there is functionality your
    module may be needing to enable as part of the routing/dispatch cycle, set
    this up in plugins and attach them to the front controller.</li>
</ul>

<p>
    This last point is the key to understanding the appropriate place to do
    module-specific initializations -- that is, initialization and/or
    bootstrapping that should only be done if the module is matched during
    routing.
</p>

<h2>Use plugins to do specific initializations</h2>

<p>
    To re-iterate: if you have initialization tasks that should only be done if
    the module is the one being executed, do it in a front controller plugin or
    action helper.
</p>

<p>
    If doing it in a front controller plugin, do these initializations any time
    after routing, as this is the only time you'll know what the module is. For
    general tasks like switching the layout, <code>routeShutdown()</code> or
    <code>dispatchLoopStartup()</code> are the right places. Simply compare the
    module in the request object to your module, and bail early if they don't
    match.
</p>

<div class="example"><pre><code lang="php">
class Foomodule_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ('foomodule' != $request-&gt;getModuleName()) {
            // If not in this module, return early
            return;
        }

        // Change layout
        Zend_Layout::getMvcInstance()-&gt;setLayout('foomodule');
    }
}
</code></pre></div>

<p>
    Your module <em>bootstrap</em> would take care of registering this plugin
    with the front controller:
</p>

<div class="example"><pre><code lang="php">
class Foomodule_Boootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initPlugins()
    {
        $bootstrap = $this-&gt;getApplication();
        $bootstrap-&gt;bootstrap('frontcontroller');
        $front = $bootstrap-&gt;getResource('frontcontroller');

        $front-&gt;registerPlugin(new Foomodule_Plugin_Layout());
    }
}
</code></pre></div>

<p>
    To keep things simple, and to reduce the performance overhead of having a
    lot of plugins, you might want to create a single plugin that performs all
    initialization; the Facade pattern is a good one to use here.
</p>

<p>
    If using action helpers, the idea is the same -- the only difference is that
    you register with the action helper broker, and will likely do your matching
    in a <code>preDispatch()</code> hook.
</p>

<h2>Isn't there a better way to do this?</h2>

<p>
    Yes, likely there are better ways to accomplish this. The true problem is
    that modules are really second-class citizens in ZF currently. There are a
    few neat ideas floating around:
</p>

<ul>
    <li><a href="http://binarykitten.me.uk/dev/zend-framework/177-active-module-based-config-with-zend-framework.html">Kathryn's Active
        module config</a></li>

    <li><a href="http://www.amazium.com/blog/zend-framework-module-specific-config">Jeroen's
        Moduleconfig</a></li>

    <li><a
        href="http://blog.vandenbos.org/2009/07/07/zend-framework-module-config/">Matthijs' ModuleConfig</a></li>

    <li><a href="http://framework.zend.com/wiki/pages/viewpage.action?pageId=16023853">Pádraic and Rob's Module Configurators proposal</a></li>
</ul>

<p>
    For 2.0, we'll be analyzing the situation and seeing if we can come up with
    a way to make module's first-class citizens in ZF. My hope is that this will
    allow users to start sharing modules easily -- which can foster a more
    "plugin"-like approach to building websites, and lead to collaboration on
    oft-needed site functionality (such as modules for blog, news, contact,
    etc.).
</p>

<p>
    In the meantime, hopefully this post has helped shed some light on how
    module configuration currently works, and provides some tips and techniques
    on how to setup your application to make use of module-specific resources
    and initialization.
</p>

<h4>Updates</h4>
<ul>
    <li>2010-03-12: added link to Paddy's proposal</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;