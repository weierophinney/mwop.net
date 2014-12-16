<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('220-Autoloading-Doctrine-and-Doctrine-entities-from-Zend-Framework');
$entry->setTitle('Autoloading Doctrine and Doctrine entities from Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1250795866);
$entry->setUpdated(1251234477);
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
    A number of people on the mailing list and twitter recently have asked how
    to autoload Doctrine using Zend Framework's autoloader, as well as how to
    autoload Doctrine models you've created. Having done a few projects using
    Doctrine recently, I can actually give an answer.
</p>

<p>
    The short answer: just attach it to <code>Zend_Loader_Autoloader</code>.
</p>

<p>
    Now for the details.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First, make sure the path to the <code>Doctrine.php</code> file is on your
    <code>include_path</code>.
</p>

<p>
    Next, <code>Zend_Loader_Autoloader</code> allows you to specify "namespaces"
    (not actual PHP namespaces, more like class prefixes) it can autoload, both
    for classes it will autoload, as well as for autoload callbacks you attach
    to it. Typically, you include the trailing underscore when doing so:
</p>

<div class="example"><pre><code class="language-php">
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader-&gt;registerNamespace('Foo_');
$autoloader-&gt;pushAutoloader($callback, 'Bar_');
</code></pre></div>

<p>
    However, because Doctrine has a master class for handling common operations,
    "Doctrine", we have to omit the trailing underscore so that the
    <code>Doctrine</code> class itself may be autoloaded. We need to do two
    different operations: first, add a namespace to
    <code>Zend_Loader_Autoloader</code> for Doctrine (which will allow us to
    autoload the Doctrine class itself, as well as the various doctrine
    subcomponent classes), and then register the
    Doctrine autoloader (which will be used by Doctrine to load items such as
    table classes, listeners, etc.):
</p>

<div class="example"><pre><code class="language-php">
$autoloader-&gt;registerNamespace('Doctrine')
           -&gt;pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
</code></pre></div>

<p>
    This takes care of the Doctrine autoloader; now, let's turn to Doctrine models.
</p>

<p>
    First, tell Doctrine that you want to autoload. You do this by telling it to
    use "conservative" model loading (shorthand for lazyloading or autoloading),
    and to autoload table classes:
</p>

<div class="example"><pre><code class="language-php">
$manager = Doctrine_Manager::getInstance();
$manager-&gt;setAttribute(  
    Doctrine::ATTR_MODEL_LOADING, 
    Doctrine::MODEL_LOADING_CONSERVATIVE
);
$manager-&gt;setAttribute(  
    Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, 
    true
);
</code></pre></div>

<p>
    From here, you need to ensure you actually <em>can</em> autoload the models.
    Normally, you tell Doctrine where to find models, but we're in a Zend
    Framework application, so let's leverage ZF conventions.
</p>

<p>
    I typically put my model code with my application code:
</p>

<pre>
application
|-- Bootstrap.php
|-- configs
|-- controllers
|-- models                 &lt;- HERE
|-- modules
|   `-- blog
|       |-- Bootstrap.php
|       |-- controllers
|       |-- forms
|       |-- models         &lt;- HERE
|       |-- services
|       `-- views
`-- views
</pre>

<p>
    Zend Framework already provides mechanisms for autoloading application
    resources via <code>Zend_Loader_Autoloader_Resource</code> and
    <code>Zend_Application_Module_Autoloader</code>. Assuming you've extended
    <code>Zend_Application_Module_Bootstrap</code> in your module bootstraps,
    you're basically already set. The trick has to do with your table classes;
    your table classes <em>must</em> be placed in the same directory as your
    models, and they <em>must</em> be named exactly the same as your models,
    with the suffix "Table".
</p>

<p>
    For example, if you had the class <code>Blog_Model_Entry</code> extending
    <code>Doctrine_Record</code> in the file
    <code>application/modules/blog/models/Entry.php</code>, the related table
    class would be <code>Blog_Model_EntryTable</code> in the file
    <code>application/modules/blog/models/EntryTable.php</code>.
</p>

<p>
    I automate most of this setup via my <code>Bootstrap</code> class, which
    typically looks as follows:
</p>

<div class="example"><pre><code class="language-php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAppAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' =&gt; 'App',
            'basePath'  =&gt; dirname(__FILE__),
        ));
        return $autoloader;
    }

    protected function _initDoctrine()
    {
        $this-&gt;getApplication()-&gt;getAutoloader()
                               -&gt;pushAutoloader(array('Doctrine', 'autoload'));

        $manager = Doctrine_Manager::getInstance();
        $manager-&gt;setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager-&gt;setAttribute(
            Doctrine::ATTR_MODEL_LOADING, 
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );
        $manager-&gt;setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        $dsn = $this-&gt;getOption('dsn');
        $conn = Doctrine_Manager::connection($dsn, 'doctrine');
        $conn-&gt;setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        return $conn;
    }
}
</code></pre></div>

<p>
    Within your configuration, you need to add two keys: one for registering the
    Doctrine namespace with the default autoloader, and another for the dsn:
</p>

<div class="example"><pre><code class="language-ini">
autoloaderNamespaces[] = \&quot;Doctrine\&quot;
dsn = \&quot;DSN to use with Doctrine goes here\&quot;
</code></pre></div>

<p>
    I also have a script that I use to load all model classes at once in order
    to do things like generate the schema or test interactions. I'll blog about
    those at a later date. Hopefully the above information will help one or two
    of you out there trying to integrate these two codebases!
</p>

<h4>Updates</h4>
<ul>
    <li><b>2009-08-21:</b> added information about registering Doctrine
    namespace with default autoloader</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;
