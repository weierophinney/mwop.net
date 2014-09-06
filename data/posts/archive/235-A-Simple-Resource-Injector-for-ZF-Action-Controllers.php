<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('235-A-Simple-Resource-Injector-for-ZF-Action-Controllers');
$entry->setTitle('A Simple Resource Injector for ZF Action Controllers');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1269030042);
$entry->setUpdated(1269377994);
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
    <a href="http://www.brandonsavage.net/">Brandon Savage</a> approached me
    with an interesting issue regarding ZF bootstrap resources, and accessing
    them in your action controllers. Basically, he'd like to see any resource
    initialized by the bootstrap immediately available as simply a public member
    of his action controller.
</p>

<p>
    So, for instance, if you were using the "DB" resource in your application,
    your controller could access it via <code>$this-&gt;db</code>.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I quickly drafted up a proof of concept for him using an action helper:
</p>

<div class="example"><pre><code lang="php">
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function __construct(array $resources = array())
    {
        $this-&gt;_resources = $resources;
    }
 
    public function preDispatch()
    {
        $bootstrap  = $this-&gt;getBootstrap();
        $controller = $this-&gt;getActionController();
        foreach ($this-&gt;_resources as $name) {
            if ($bootstrap-&gt;hasResource($name)) {
                $controller-&gt;$name = $bootstrap-&gt;getResource($name);
            }
        }
    }
 
    public function getBootstrap()
    {
        return $this-&gt;getFrontController()-&gt;getParam('bootstrap');
    }
}
</code></pre></div>

<p>
    In this action helper, you would specify the specific resources you want
    injected via the <code>$_resources</code> property - which would be values
    you pass in. Each resource name would then be checked against those
    available in the bootstrap, and, if found, injected into the action
    controller as a property of the same name.
</p>

<p>
    You would initialize it in your bootstrap:
</p>

<div class="example"><pre><code lang="php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResourceInjector()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new My_ResourceInjector(array(
                'db',
                'layout',
                'navigation',
            ));
        );
    }
}
</code></pre></div>

<p>
    The above would map three resources: "db", "layout", and "navigation". This
    means you can refer to them directly as properties in your controllers:
</p>

<div class="example"><pre><code lang="php">
class FooController extends Zend_Controller_Action
{
    public function barAction()
    {
        $this-&gt;layout-&gt;disableLayout();
        $model = $this-&gt;getModel();
        $model-&gt;setDbAdapter($this-&gt;db);
        $this-&gt;view-&gt;assign(
            'model'      =&gt; $this-&gt;model,
            'navigation' =&gt; $this-&gt;navigation,
        );
    }

    // ...
}
</code></pre></div>

<p>
    This approach leads to some nice brevity -- you no longer need to fetch the
    bootstrap from the instantiation arguments, and then fetch the resource.
</p>

<p>
    I thought about it some more, and realized that there's a few problems: How
    do you know what is being injected from within the controller? How do you
    control what is being injected.
</p>

<p>
    So, I revised it to pull the expected dependencies from the action
    controller itself:
</p>

<div class="example"><pre><code lang="php">
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function preDispatch()
    {
        $bootstrap  = $this-&gt;getBootstrap();
        $controller = $this-&gt;getActionController();

        if (!isset($controller-&gt;dependencies) 
            || !is_array($controller-&gt;dependencies)
        ) {
            return;
        }

        foreach ($controller-&gt;dependencies as $name) {
            if ($bootstrap-&gt;hasResource($name)) {
                $controller-&gt;$name = $bootstrap-&gt;getResource($name);
            }
        }
    }
 
    public function getBootstrap()
    {
        return $this-&gt;getFrontController()-&gt;getParam('bootstrap');
    }
}
</code></pre></div>

<p>
    You would still register this in your bootstrap, but now you would no longer
    need any constructor arguments:
</p>

<div class="example"><pre><code lang="php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResourceInjector()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new My_ResourceInjector();
        );
    }
}
</code></pre></div>

<p>
    Instead, you define the resources you need to retrieve in your controller:
</p>

<div class="example"><pre><code lang="php">
class FooController extends Zend_Controller_Action
{
    public $dependencies = array(
        'db',
        'layout',
        'navigation',
    );

    public function barAction()
    {
        $this-&gt;layout-&gt;disableLayout();
        $model = $this-&gt;getModel();
        $model-&gt;setDbAdapter($this-&gt;db);
        $this-&gt;view-&gt;assign(
            'model'      =&gt; $this-&gt;model,
            'navigation' =&gt; $this-&gt;navigation,
        );
    }

    // ...
}
</code></pre></div>

<p>
    This makes it far more clear what your dependencies are, and also ensures
    that each controller only gets the dependencies it plans on using. However,
    I think it can still be improved: if the dependency is not found, we should
    likely throw an exception!
</p>

<div class="example"><pre><code lang="php">
class My_ResourceInjector extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;

    public function preDispatch()
    {
        $bootstrap  = $this-&gt;getBootstrap();
        $controller = $this-&gt;getActionController();

        if (!isset($controller-&gt;dependencies) 
            || !is_array($controller-&gt;dependencies)
        ) {
            return;
        }

        foreach ($controller-&gt;dependencies as $name) {
            if (!$bootstrap-&gt;hasResource($name)) {
                throw new DomainException(\&quot;Unable to find dependency by name '$name'\&quot;);
            }
            $controller-&gt;$name = $bootstrap-&gt;getResource($name);
        }
    }
 
    public function getBootstrap()
    {
        return $this-&gt;getFrontController()-&gt;getParam('bootstrap');
    }
}
</code></pre></div>


<p>
    This better satisfies the goals and needs of dependency tracking.
    Dependencies are defined by the object that needs them, they're injected by
    a collaborator, and missing dependencies results in an exception.
</p>

<p>
    One potential improvement would be to allow specifying "default" resources
    to inject into all controllers; this could be accomplished with a
    constructor argument similar to the second example provided, and merging
    that value with the controller dependencies. I'll leave that as an exercise
    for the reader, though.
</p>

<p>
    Action helpers are an area that is largely unexplored by many ZF users.
    Hopefully this post will show just how powerful they can be, and how much
    they can automate common tasks.
</p>
EOT;
$entry->setExtended($extended);

return $entry;