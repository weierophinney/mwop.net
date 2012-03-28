<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('246-Using-Action-Helpers-To-Implement-Re-Usable-Widgets');
$entry->setTitle('Using Action Helpers To Implement Re-Usable Widgets');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1286199600);
$entry->setUpdated(1286792027);
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
I had a twitter/IRC exchange yesterday with <a href="http://twitter.com/andriesss">Andries Seutens</a> and <a href="http://twitter.com/NickBelhomme">Nick Belhomme</a> regarding applications that include widgets within their layout. During the exchange, I told Andriess not to use the <code>action()</code> view helper, and both Andriess and Nick then asked how to implement widgets if they shouldn't use that helper. While I ended up having an IRC exchange with Nick to give him a general idea on how to accomplish the task, I decided a longer writeup was in order.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2> Background </h2>

<p>
The situation all started when Andries tweeted asking about what he considered some mis-behavior on the part of the <code>action()</code> view helper -- a situation that turned out not to be an issue, <em>per se</em>, but more a case of bad architecture within Zend Framework. His assumption was that calling <code>action()</code> would fire off another circuit of the front controller's dispatch loop -- which would mean he could rely on plugins he'd established to fire. However, <code>action()</code> does nothing of the sort. It instead pulls the dispatcher from the front controller, and manually calls <code>dispatch()</code> on a new action. As such, action helpers will trigger, but no front controller plugins will. Additionally, if a redirect or "forward" condition is detected, it simply returns an empty string.
</p>

<p>
The helper was done this way because Zend Framework does not render views a single time -- it instead renders after each action, and accumulates views to render in the layout. If we were accumulating view variables and rendering once, and if we were using a finite state machine of some sort, we could probably operate the way one would expect -- within the dispatch loop. Since we don't, any solution around looping over actions (such as the <code>ActionStack</code> action helper/front controller plugin) or rendering the content of executing an action will be a hack. <em>Note: ZF2's MVC layer may make this possible... though still not necessarily recommended.</em>
</p>

<p>
There are other reasons to avoid the use of these solutions, though. If you are
invoking additional controller actions in order to help populate your view,
you're likely putting domain logic into your controllers. Think about it. The
controller should only be responsible for taking the input, funneling it to
the correct model or models, and then passing information on to the views
</p>

<p>
With that in mind, here's the approach I recommended to Nick and Andries.
</p>

<h2> The Secret Weapon: Action Helpers </h2>

<p>
I've <a href="http://devzone.zend.com/article/3350-Action-Helpers-in-Zend-Framework">blogged</a> about <a href="http://weierophinney.net/matthew/archives/233-Responding-to-Different-Content-Types-in-RESTful-ZF-Apps.html">action</a> <a href="http://weierophinney.net/matthew/archives/235-A-Simple-Resource-Injector-for-ZF-Action-Controllers.html">helpers</a> before. They're a built-in mechanism in Zend Framework to allow you to extend your action controllers in a way that uses composition instead of inheritance.
</p>

<p>
One approach to widgets for Zend Framework makes use of these. Consider the following "user" module:
</p>

<code><pre>
user
|-- Bootstrap.php
|-- configs
|   `-- user.ini
|-- controllers
|-- forms
|   `-- Login.php
|-- helpers
|   `-- HandleLogin.php
`-- views
    `-- scripts
        |-- login.phtml
        `-- profile.phtml
</pre></code>

<p>
Now, notice a few things about it. First, it has views, helpers, and forms -- but no controllers. So, there are no controllers or actions that may be invoked; you could definitely define some, but you don't <em>need</em> to; your widgets will work with or without them. Second, notice that the "views/scripts" subdirectory is not further subdivided; its view scripts are not part of any actions, so they can be at the top level within this module. Finally, notice that it has both a bootstrap and configuration.
</p>

<p>
Let's look at the bootstrap file. Since this is in a module, it gets a prefix named after the module, and is thus <code>User_Bootstrap</code>.
</p>

<div class="example"><pre><code lang="php">
class User_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function initResourceLoader()
    {
        $loader = $this-&gt;getResourceLoader();
        $loader-&gt;addResourceType('helper', 'helpers', 'Helper');
    }

    protected function _initConfig()
    {
        $env = $this-&gt;getEnvironment();
        $config = new Zend_Config_Ini(
            dirname(__FILE__) . '/configs/user.ini', 
            $this-&gt;getEnvironment()
        );
        return $config;
    }

    protected function _initHelpers()
    {
        $this-&gt;bootstrap('config');
        $config = $this-&gt;getResource('config');

        Zend_Controller_Action_HelperBroker::addHelper(
            new User_Helper_HandleLogin($config)
        );
    }
}
</code></pre></div>

<p>
I've overridden the <code>initResourceLoader()</code> method so that I can add a "helper" resource corresponding to my "helpers" subdirectory; this will be used to allow autoloading action helpers.
</p>

<p>
The <code>_initConfig()</code> method initializes bootstrap configuration. I pull in the configuration file relative to the Bootstrap class file, and use the registered environment to determine what section to use from that configuration.
</p>

<p>
Finally, I have an initializer method for loading my action helpers. This method is dependent on the <code>_initConfig()</code> method, as I want to pass my configuration to the helpers. In here, I instantiate a single action helper, <code>User_Helper_HandleLogin</code>.
</p>

<p>
Next, lets look at the configuration:
</p>

<div class="example"><pre><code lang="ini">
[production]
salt = \&quot;1471998176\&quot;
adapter.table = \&quot;users\&quot;
adapter.identity_column = \&quot;username\&quot;
adapter.password_column = \&quot;password\&quot;

[testing : production]

[development : production]
</code></pre></div>

<p>
These are values I'm going to use in my action helper(s). We'll revisit them
later; the general gist I'm getting at here is this is just a normal
configuration file.
</p>

<p>
Now, let's look at the action helper itself. As a reminder, action helpers can define hooks for <code>init()</code> (invoked by the helper broker each time it is passed to a new controller), <code>preDispatch()</code> (invoked prior to executing the controller's <code>preDispatch()</code> hook and executing the action itself), and <code>postDispatch()</code> (executed after the action and the controller's <code>postDispatch()</code> routine). In this particular helper, I'll define a <code>preDispatch()</code> hook that does the following:
</p>

<ul>
<li> If we do not have an established authentication identity, render a login widget.</li>
<li> If we do have an established authentication identity, render a user profile widget.</li>
<li> If we to not have an established authentication identity, but a <code>POST</code> has occurred, attempt to login the user; if successful, display the user profile widget, but if not, re-display the login widget with an error message.</li>
</ul>

<p>
The initial definition looks like this:
</p>

<div class="example"><pre><code lang="php">
class User_Helper_HandleLogin 
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $config;

    public function __construct(Zend_Config $config)
    {
        $this-&gt;config = $config;
    }

    public function preDispatch()
    {
        if (null === ($controller = $this-&gt;getActionController())) {
            return;
        }

        $auth = Zend_Auth::getInstance();
        if (!$auth-&gt;hasIdentity()) {
            $this-&gt;handleLogin();
            return;
        }

        $this-&gt;createProfileWidget();
    }

    /* ... */
}
</code></pre></div>

<p>
As noted earlier, we expect a configuration object to the constructor. We'll use this later to get some values for authentication. When we start our <code>preDispatch()</code> routine, we check first to see if we have an action controller available; if not, we'll move on, as we won't have a view to act on.
</p>

<p>
Next, we check for an identity. If we don't have one, we delegate to a <code>handleLogin()</code> method; otherwise, a <code>createWidgetProfile()</code> method. We'll look at the latter first, as it's simpler -- but first, we'll take a small digression and look at how we get the view object.
</p>

<div class="example"><pre><code lang="php">
class User_Helper_HandleLogin 
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    /* ... */

    public function getView()
    {
        if (null !== $this-&gt;view) {
            return $this-&gt;view
        }

        $controller = $this-&gt;getActionController();
        $view = $controller-&gt;view;
        if (!$view instanceof Zend_View_Abstract) {
            return;
        }
        $view-&gt;addScriptPath(dirname(__FILE__) . '/../views/scripts');
        $this-&gt;view = $view;
        return $view;
    }
}
</code></pre></div>

<p>
In here, we grab the view from the controller. If we don't have one we can work with, we simply return a null value. If we do, however, we add a script path pointing to the module's script path, and return the view.
</p>

<p>
Now, the <code>createWidgetProfile()</code> method:
</p>

<div class="example"><pre><code lang="php">
class User_Helper_HandleLogin 
    extends Zend_Controller_Action_Helper_Abstract
{
    /* ... */

    public function createProfileWidget()
    {
        if (!$view = $this-&gt;getView()) {
            return;
        }

        $view-&gt;user = $view-&gt;partial('profile.phtml', array(
            'identity' =&gt; Zend_Auth::getInstance()-&gt;getIdentity(),
        ));
    }

    /* ... */
}
</code></pre></div>

<p>
Again, a simple bit of logic. We attempt to retrieve a view, and exit early if we don't get one. Next, we render a partial view, using the identity from the authentication storage, and assign it to a view member, "user". The view script looks like this:
</p>

<div class="example"><pre><code lang="php">
&lt;?php $identity = (array) $this-&gt;identity; ?&gt;
&lt;div id=\&quot;user-profile\&quot;&gt;
    &lt;h4&gt;&lt;?php echo $this-&gt;escape($identity['username']) ?&gt;&lt;/h4&gt;
    &lt;dl&gt;
    &lt;?php foreach ($identity as $field =&gt; $value): ?&gt;
        &lt;?php if ($field == 'username'):
            continue;
        endif ?&gt;
        &lt;dt&gt;&lt;?php echo ucfirst($field) ?&gt;:&lt;/dt&gt;
        &lt;dd&gt;&lt;?php echo $this-&gt;escape($value) ?&gt;:&lt;/dd&gt;
    &lt;?php endforeach ?&gt;
    &lt;/dl&gt;
&lt;/div&gt;
</code></pre></div>

<p>
Nothing fancy -- just a <code>div</code> with a heading and a definition list.
</p>

<p>
Next, the <code>handleLogin()</code> method. In this method we need to do several things:
</p>

<ul>
<li> Check to see if we have a <code>POST</code> request; if not, simply render the form.</li>
<li> Validate the form; if we have errors, re-render it.</li>
<li> Attempt to authenticate against the form values; if we fail, re-render the form, with an error condition.</li>
<li> Finally, on successful authentication, store the identity, and then render the profile.</li>
</ul>

<p>
The logic looks like this:
</p>

<div class="example"><pre><code lang="php">
class User_Helper_HandleLogin 
    extends Zend_Controller_Action_Helper_Abstract
{
    /* ... */

    public function renderLoginForm(Zend_Form $form, $error = null)
    {
        if (!$view = $this-&gt;getView()) {
            return;
        }

        $view-&gt;user = $view-&gt;partial('login.phtml', array(
            'form'  =&gt; $form,
            'error' =&gt; $error,
        ));
    }

    public function handleLogin()
    {
        $request = $this-&gt;getRequest();
        $form    = new User_Form_Login();

        if (!$request-&gt;isPost()) {
            $this-&gt;renderLoginForm($form);
        }

        if (!$form-&gt;isValid($request-&gt;getPost())) {
            $this-&gt;renderLoginForm($form);
            return;
        }

        // Does the POST contain the form namespace? If not, just render the form
        $namespace = $form-&gt;getElementsBelongTo();
        if (!empty($namespace) &amp;&amp; !is_array($request-&gt;getPost($namespace))) {
            $this-&gt;renderLoginForm($form);
            return; 
        }

        $username = $form-&gt;username-&gt;getValue();
        $password = $form-&gt;password-&gt;getValue();
        $password = substr($username, 0, 3) . $password . $this-&gt;config-&gt;salt;
        $password = hash('sha256', $password);

        $adapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table_Abstract::getDefaultAdapter(),
            $this-&gt;config-&gt;adapter-&gt;table,
            $this-&gt;config-&gt;adapter-&gt;identity_column,
            $this-&gt;config-&gt;adapter-&gt;password_column
        );
        $adapter-&gt;setIdentity($username)
                -&gt;setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth-&gt;authenticate($adapter);
        if (!$result-&gt;isValid()) {
            $this-&gt;renderLoginForm($form, 'Invalid Credentials');
            return;
        }

        $auth-&gt;getStorage()-&gt;write(
            $adapter-&gt;getResultRowObject(null, 'password')
        );

        $this-&gt;createProfileWidget();
    }

    /* ... */
}
</code></pre></div>

<p>
If you look carefully, you'll see that the passed in configuration is utilized
in order to configure the authentication adapter, as well as salt the password
prior to hashing. We re-use the <code>createProfileWidget()</code> method in order to
render the profile when done, an dthe new <code>renderLoginForm()</code> method will
render our login form for us.
</p>

<p>
The form partial looks like this:
</p>

<div class="example"><pre><code lang="php">
&lt;div id=\&quot;login-widget\&quot;&gt;
&lt;?php if ($this-&gt;error): ?&gt;
    &lt;p class=\&quot;error\&quot;&gt;&lt;?php echo $this-&gt;escape($this-&gt;error) ?&gt;&lt;/p&gt;
&lt;?php endif ?&gt;
    &lt;?php 
    $this-&gt;form-&gt;setAction('#')
               -&gt;setMethod('post');
    echo $this-&gt;form;
    ?&gt;
&lt;/div&gt;
</code></pre></div>

<p>
We could get more fancy, and set decorators and what not, but there's no need within the scope of the example. I'm not showing the form definition here, as it's somewhat out of scope for this post; any old form should do, however.
</p>

<p>
If you've been paying attention, you'll note that in both cases -- displaying the login form or displaying the user profile -- I captured the rendered views to the same view variable, "user". At this point, you can then do the following in your action's view scripts in order to display the widget within the page you generate:
</p>

<div class="example"><pre><code lang="php">
&lt;?php echo $this-&gt;user ?&gt;
</code></pre></div>

<h2> Summary </h2>

<p>
This example is fairly basic in terms of the functionality and structure offered. You could expand this in a number of ways:
</p>

<ul>
<li> Instead of using <code>preDispatch()</code>, have your controllers explicitly invoke the widget action helpers they need to consume.</li>
<ul>
<li> Potentially, your controllers could define a list of "widgets" they need, and each widget could inspect this on <code>preDispatch()</code> to determine if they need to do any work.</li>
<li> Alternately, the widgets could maintain a list of actions, controllers, or modules in which they should render (or not).</li>
</ul>
<li> Have your action helpers use models and service classes from their own module in order to perform their work. For instance, I could have written an authentication model and simply consumed that from the action helper, in order to provide better separation of concerns.</li>
<li> You could also write view helpers that are specific to certain models you write within your module. You would then need to add not only a script path, but a helper path to your view.</li>
<li> You should setup some clear, clean rules for consistency with regards to how widgets are named in your views, as well as how the helpers are named.</li>
</ul>

<p>
This technique is rather flexible, and keeps your code cleanly separated, as well as protects you from the inconsistencies and issues inherent in the <code>ActionStack</code> and <code>action()</code> view helper. With some discipline and creative thinking, you should be able to accomplish a variety of effects, as well as make re-usable widgets.
</p>

<h3> Note </h3>

<p>
I've put a full sample of the code from this post up <a href="http://github.com/weierophinney/zf-examples/tree/master/widgets-as-helpers">my zf-examples repo on GitHub</a>.
</p>

<h2>Clarifications</h2>

<p>
    A number of folks have indicated in comments that they have been using view
    helpers in order to effect widget content on their site, and have asked if
    that is an appropriate approach (or argued that it is).
</p>

<p>
    Using view helpers makes a lot of sense, but if, <em>and only if</em>, the
    following conditions are met:
</p>

<ul>
    <li>The helper <em>does not require Request data</em> in order to do its
    work, or can depende only on data injected into the view (don't cheat and
    inject the Request object!).</li>

    <li>The helper <em>will not be updating the model</em>.</li>
</ul>

<p>
    If you are doing either of the above two items, you should consider using an
    action helper. The view should only be responsible for display logic, which
    <em>may</em> include <em>retrieving</em> data from a model. The
    controller is responsible for inspecting the request and determining what
    models and views to marshall -- and for <em>updating</em> models.
</p>

<p>
    If your widget is simply pulling data from a model, or displaying some
    markup, by all means, use a view helper. If it's doing more than that,
    don't.
</p>

<p>
    This also touches on a related topic brought up in the comments: what if
    you're serving an alternate content type -- e.g., application/json? Again,
    this is where I feel using action helpers is to your advantage. It would be
    very easy to define an interface for your action helpers that allows them to
    indicate what content types they're allowed to operate on. Then, within the
    action helper logic or within a plugin that marshalls the action helpers,
    you can easily disable them from executing if they cannot serve that content
    type. Within the view, you simply won't reference their captured output --
    and even if you do, the value will be returned as simply <code>null</code>
    if they are disabled.
</p>
EOT;
$entry->setExtended($extended);

return $entry;