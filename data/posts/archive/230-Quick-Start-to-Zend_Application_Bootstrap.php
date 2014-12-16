<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('230-Quick-Start-to-Zend_Application_Bootstrap');
$entry->setTitle('Quick Start to Zend_Application_Bootstrap');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1262973440);
$entry->setUpdated(1270208697);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    We added <a
        href="http://framework.zend.com/manual/en/zend.application.html">Zend_Application</a>
    to Zend Framework starting in version 1.8.0. The intent behind the component
    was to formalize the application bootstrapping process, and provide a
    simplified, configuration-driven mechanism for it.
</p>

<p>
    <code>Zend_Application</code> works in conjunction with
    <code>Zend_Application_Bootstrap</code>, which, as you might guess from its
    name, is what really does the bulk of the work for bootstrapping your
    application. It allows you to utilize plugin bootstrap resources, or define
    local bootstrap resources as class methods. The former allow for
    re-usability, and the latter for application-specific initialization and
    configuration.
</p>

<p>
    Additionally, <code>Zend_Application_Bootstrap</code> provides for
    dependency tracking (i.e., if one resource depends on another, you can
    ensure that that other resource will be executed first), and acts as a
    repository for initialized resources. This means that once a resource has
    been bootstrapped, you can retrieve it later from the bootstrap itself.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>How it works</h2>

<p>
    Now that you know what it does, let's jump into the basics.
</p>

<p>
    If you use the <code>zf</code> command-line tool provided with Zend
    Framework to generate your project (<code>"zf create project"</code>),
    you'll get a bootstrap and a default configuration right out of the gate.
    This includes the following files in the tree:
</p>

<pre>
application/
|-- Bootstrap.php
|   `-- configs/
|   |   `-- application.ini
</pre>

<p>
    The <code>Bootstrap.php</code> file will contain the class
    <code>Bootstrap</code> which extends
    <code>Zend_Application_Bootstrap_Bootstrap</code>; this class will be empty
    at first. The <code>application.ini</code> file will contain the following:
</p>

<div class="example"><pre><code class="language-ini">
[production]
phpSettings.display_startup_errors = 0
phpSettings.display_errors = 0
includePaths.library = APPLICATION_PATH \&quot;/../library\&quot;
bootstrap.path = APPLICATION_PATH \&quot;/Bootstrap.php\&quot;
bootstrap.class = \&quot;Bootstrap\&quot;
appnamespace = \&quot;Application\&quot;
resources.frontController.controllerDirectory = APPLICATION_PATH \&quot;/controllers\&quot;
resources.frontController.params.displayExceptions = 0

[staging : production]

[testing : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1

[development : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
resources.frontController.params.displayExceptions = 1
</code></pre></div>

<p>
    <code>Zend_Application</code> runs in three stages. First, it initializes
    the PHP environment, using INI settings from your configuration if provided,
    and setting up the <code>include_path</code> and autoloading. Second, it
    initializes and executes the bootstrap class. Finally, it then "runs" the
    application (by calling the bootstrap's <code>run()</code> method).
</p>

<h2>Configuration Settings</h2>

<p>
    What we see in the above listing is a set of:
</p>

<ul>
    <li>PHP initialization settings (here, they indicate whether or not to
    display errors)</li>

    <li><code>include_path</code> settings</li>

    <li>Settings that indicate the name and location of the bootstrap class</li>

    <li>Application resource settings</li>
</ul>

<p>
    The <code>phpSettings</code> key accepts any <code>php.ini</code> keys as
    subkeys, and these key/value pairs will be passed to <code>ini_set</code>.
    This can be useful when you need to either ensure specific INI settings are
    made, particularly when you want them to vary based on environment. (In the
    example above, display_errors is enabled in testing and development, but
    disabled otherwise.)
</p>

<p>
    When it comes to the include_path and autoloading, probably the most often
    asked question is, "How do I add namespace prefixes for code other than ZF
    to the autoloader?" This can be done easily in the configuration file using
    the <code>autoloaderNamespaces</code> key, and appending namespace prefixes
    to it:
</p>

<div class="example"><pre><code class="language-ini">
autoloaderNamespaces[] = \&quot;Phly_\&quot;
</code></pre></div>

<p>
    Regarding the bootstrap class and file location, typically the defaults will
    be fine. However, if you want to specify a custom name -- for instance, to
    provide a class prefix -- or perhaps if your default module is in a
    subdirectory, you can notify <code>Zend_Application</code> of this via the
    <code>bootstrap.class</code> and <code>boostrap.path</code> settings:
</p>

<div class="example"><pre><code class="language-ini">
bootstrap.class = \&quot;Application_Bootstrap\&quot;
bootstrap.path = APPLICATION_PATH \&quot;/modules/application/Bootstrap.php\&quot;
</code></pre></div>

<h2>Getting started with Bootstrap Resources</h2>

<p>
    Now we finally get to the true fun: the bootstrap resources themselves.
</p>

<p>
    <em>
        Yes, I'm aware I'm glossing over the "appnamespace" setting; I'l cover
        that at another time.
    </em>
</p>

<p>
    Bootstrap resources may be one of two things:
</p>

<ul>
    <li>A protected method in the bootstrap class prefixed with "_init"; e.g.,
        "protected function _initFoo()"</li>

    <li>A class implementing <code>Zend_Application_Resource_Resource</code></li>
</ul>

<p>
    In the former case, _init*() methods, each will be executed in each request.
    In the latter, only those that you specify in your configuration will be
    executed, allowing you to selectively choose which of the various shipped
    resource plugins (or those you have written yourself!) will be used.
</p>

<p>
    In the case of the default configuration, only the "frontcontroller"
    resource plugin will be used, corresponding to
    <code>Zend_Application_Resource_Frontcontroller</code>. As of the upcoming
    1.10 release, you can pick and choose from the following additional resource
    plugins as well:
</p>

<ul>
    <li>Cachemanager</li>
    <li>Db</li>
    <li>Dojo</li>
    <li>Layout</li>
    <li>Locale</li>
    <li>Log</li>
    <li>Mail</li>
    <li>Modules</li>
    <li>Multidb</li>
    <li>Navigation</li>
    <li>Router</li>
    <li>Session</li>
    <li>Translate</li>
    <li>View</li>
</ul>

<p>
    Each has its own configuration options, <a
        href="http://framework.zend.com/manual/en/zend.application.available-resources.html">documented
        in the manual</a>.
</p>

<h2>Writing Resource Methods</h2>

<p>
    Writing your own resource methods is trivial: you simply create the method,
    and do some work. You then have the option of returning a value; if you do,
    it will be stored within the bootstrap so that you may retrieve it later. As
    an example:
</p>

<div class="example"><pre><code class="language-php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRegistry()
    {
        $registry = new Zend_Registry();
        return $registry;
    }
}
</code></pre></div>

<p>
    If we wanted to retrieve the registry later, we could do so using the
    bootstrap's <code>getResource()</code> method:
</p>

<div class="example"><pre><code class="language-php">
$registry = $bootstrap-&gt;getResource('Registry');
</code></pre></div>

<p>
    Note that we pass the name of the method <em>minus</em> the "_init" prefix;
    this "short name" is how the resource is referred to within the bootstrap,
    and how you will refer to it later.
</p>

<p>
    Now, let's say you have a resource that <em>depends</em> on your "Registry"
    resource; for instance, let's say you want to create a
    <code>Zend_Currency</code> object, and pass it to the registry.
    <code>Zend_Application_Bootstrap</code> was designed to handle this very
    situation, and institutes some powerful dependency tracking (this is, in
    fact, why the initialization methods are protected; it prevents them being
    called directly). Simply call the <code>bootstrap()</code> method with the
    name of the resource to initialize. Additionally, the
    <code>getResource()</code> method can then be used to retrieve the value
    registered for that resource. As an example:
</p>

<div class="example"><pre><code class="language-php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initCurrency()
    {
        $this-&gt;bootstrap('Registry');
        $registry = $this-&gt;getResource('Registry');

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
</code></pre></div>

<p>
    What will happen is this:
</p>

<ul>
    <li>
        <code>Zend_Application</code> will call <code>bootstrap()</code> with no
        arguments, which loops through the internal resource methods first, and
        then any configured resource plugins.
    </li>

    <li>The bootstrap will execute the <code>_initCurrency()</code> method</li>

    <li>It sees the <code>bootstrap()</code> call, and executes it</li>

    <li>
        The <code>bootstrap()</code> call executes the
        <code>_initRegistry()</code> method, storing a
        <code>Zend_Registry</code> instance (which was returned from the method)
        internally on completion
    </li>

    <li>
        Execution of <code>_initCurrency()</code> resumes, starting with the
        <code>getResource()</code> call; this returns the
        <code>Zend_Registry</code> instance stored under that key in the
        bootstrap.
    </li>

    <li>
        Execution of <code>_initCurrency()</code> completes, and the bootstrap
        stores the returned <code>Zend_Currency</code> instance.
    </li>

    <li>
        The <code>bootstrap()</code> method then attempts to call the
        <code>_initRegistry()</code> method, but notes that it has already been
        executed, and thus moves on to execute resource plugins.
    </li>
</ul>

<p>
    As you can see by now, the bootstrap functionality is quite flexible and
    powerful, and provides a number of benefits immediately out of the box.
</p>

<h2>Until next time...</h2>

<p>
    At this point, you should have enough to get started writing your own
    bootstrap initialization resources. In coming weeks, I'll blog about how to
    build reusable resource plugins, as well as discuss how bootstrapping fits
    into modular applications.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
