<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('201-Applying-ACLs-to-Models');
$entry->setTitle('Applying ACLs to Models');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1230121600);
$entry->setUpdated(1230418095);
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
    In my last post, I <a href="http://weierophinney.net/matthew/archives/200-Using-Zend_Form-in-Your-Models.html">discussed using Zend_Form as a combination input filter/value object within your models</a>. 
    In this post, I'll discuss using Access Control Lists (ACLs) as part of your
    modelling strategy.
</p>

<p>
    ACLs are used to indicate <em>who</em> has access to do <em>what</em>
    on a given <em>resource</em>. In the paradigm I will put forward, your
    <em>resource</em> is your model, and the <em>what</em> are the various
    methods of the model. If you finesse a bit, you'll have "user" objects that
    act as your <em>who</em>.
</p>

<p>
    Just like with forms, you want to put your ACLs as close to your domain
    logic as possible; in fact, ACLs are <em>part</em> of your domain.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    First up, however, let's review <code>Zend_Acl</code>.
</p>

<h2>Zend_Acl in a Nutshell</h2>

<p>
    <code>Zend_Acl</code> is divided into three areas of responsibility:
</p>

<ul>
    <li><b>Resources</b> are objects to which access is controlled</li>
    <li><b>Roles</b> are objects which may request access to one or more <em>resources</em></li>
    <li><b>ACLs</b> provide a tree structure to which resources and roles may
    be added, and which map <em>access</em> rules between them.</li>
</ul>

<p>
    <code>Zend_Acl</code> is primarily engineered to be configured and
    manipulated programmatically. While you can certainly write functionality to
    pull the information out of a data store -- say, an LDAP directory or a
    database -- in many cases, you don't need to. Let's look at this simple ACL
    definition:
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_Acl_Spindle extends Zend_Acl
{
    public function __construct()
    {
        // Define roles:
        $this-&gt;addRole(new Spindle_Model_Acl_Role_Guest)
             -&gt;addRole(new Spindle_Model_Acl_Role_User,      'guest')
             -&gt;addRole(new Spindle_Model_Acl_Role_Developer, 'user')
             -&gt;addRole(new Spindle_Model_Acl_Role_Manager,   'developer');

        // Deny privileges by default; i.e., create a whitelist
        $this-&gt;deny();

        // Define resources and add privileges
        $this-&gt;add(new Spindle_Model_Acl_Resource_Bug)
             -&gt;allow('guest',     'bug', array('list', 'view'))
             -&gt;allow('user',      'bug', array('add', 'comment', 'link', 'close'))
             -&gt;allow('developer', 'bug', array('update', 'delete'));

        $this-&gt;add(new Spindle_Model_Acl_Resource_Comment)
             -&gt;allow('guest',     'comment', array('view', 'list'))
             -&gt;allow('user',      'comment', array('add'))
             -&gt;allow('developer', 'comment', array('delete'));
    }
}
</code></pre></div>

<p>
    In this example, we do several things:
</p>

<ul>
    <li>Define our roles. You'll note that several role definitions take an
    additional argument. In each case, this argument specifies what role the new
    role inherits from. Thus, as we apply privileges for one role, any role that
    inherits from that role will also receive those privileges.</li>
    <li>Create a whitelist. The <code>deny()</code> method, when called before
    any other permissions, tells <code>Zend_Acl</code> that we want to deny
    permission unless we've specifically allowed it.</li>
    <li>Add resources.</li>
    <li>Specify privileges available on each resource based on the role
    accessing the resource. This is done via the <code>allow()</code>
    method.</li>
</ul>

<p>
    <em>Resources</em> and <em>Roles</em> in <code>Zend_Acl</code> need merely
    implement the appropriate interfaces. These interfaces merely define a
    single method apiece, each of which returns a string identifier used in the
    object graph in <code>Zend_Acl</code>. As an example:
</p>

<div class="example"><pre><code class="language-php">
// A simple role:
class Spindle_Model_Acl_Role_Guest implements Zend_Acl_Role_Interface
{
    public function getRoleId()
    {
        return 'guest';
    }
}

// A simple resource:
class Spindle_Model_Acl_Resource_Bug implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        return 'bug';
    }
}
</code></pre></div>

<p>
    As you may notice, these are trivial to implement -- and the point is that
    they can be mixed in to your model classes to give them semantic meaning.
    That said, there's one caveat: when defining the actual ACL rules -- which
    map roles and resources -- the specified roles and resources must
    <em>already exist</em> in the ACL tree. As such, I find it convenient to
    define my roles early, and then add resources and privileges on an ad hoc
    basis.
</p>

<p>
    By grouping the base ACL definition in an object, we now have a re-usable
    ACL that we can pass around or use within other contexts, finally bringing
    us to our model.
</p>

<h2>Using Zend_Acl in Models</h2>

<h3>Roles</h3>

<p>
    Typically in Zend Framework, you'll authenticate a user using
    <code>Zend_Auth</code>, which will persist their "identity" in the session.
    This "identity" can be anything: a string, an array, an object. This latter
    gives some fantastic potential: if the object implements
    <code>Zend_Acl_Role_Interface</code>, then it can be used for ACL checks.
</p>

<p>
    Let's define a "User" object that implements the role interface. Internally,
    we'll store the user's defined role as part of the object, and have the
    <code>getRoleId()</code> method return that value.
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_UserManager_User implements Zend_Acl_Role_Interface
{
    /* ... */

    public function getRoleId()
    {
        if (!isset($this-&gt;role)) {
            return 'guest';
        }
        return $this-&gt;role;
    }

    /* ... */
}
</code></pre></div>

<p>
    You'll notice that not only does this provide the user's current role, but
    it also provides a contingency for when none is set ("guest" is our lowest
    level of access). 
</p>

<p>
    I'll revisit this user class in later articles.
</p>

<h3>Resources</h3>

<p>
    <em>A model is a resource</em>. As such, it should implement the resource
    interface. Furthermore, it likely should know which roles are allowed which
    rights. Finally, it should be able to verify access before performing an
    action. So, we need a little code.
</p>

<p>
    First, let's make our model a resource.
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        return 'bug';
    }

    /* ... */
}
</code></pre></div>

<p>
    Now, let's allow injecting an ACL object, or lazyloading it if none is
    found. In each case, we should then setup the access list for our resource.
    We'll limit the ACL object to one of a known type -- which ensures that
    particular roles will be present.
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    protected $_acl;

    public function setAcl(Spindle_Model_Acl_Spindle $acl)
    {
        if (!$acl-&gt;has($this-&gt;getResourceId())) {
            $acl-&gt;add($this)
                -&gt;allow('guest',     $this, array('list', 'view'))
                -&gt;allow('user',      $this, array('save', 'comment', 'link', 'close'))
                -&gt;allow('developer', $this, array('delete'));
        }
        $this-&gt;_acl = $acl;
        return $this;
    }

    public function getAcl()
    {
        if (null === $this-&gt;_acl) {
            $this-&gt;setAcl(new Spindle_Model_Acl_Spindle());
        }
        return $this-&gt;_acl;
    }

    /* ... */
}

</code></pre></div>

<p>
    You'll notice that we pass <code>$this</code> as an argument. We can do this
    because our model is a resource. Also notice that we lazyload the ACL object
    if none has been injected.
</p>

<p>
    Next, we need a way to determine the current role. As noted earlier when
    discussing roles, you'll typically authenticate a user with
    <code>Zend_Auth</code>, which will persist the current identity. We'll allow
    injection of the current identity, as well as a way to lazyload it from
    <code>Zend_Auth</code>.
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    protected $_identity;

    public function setIdentity($identity)
    {
        if (is_array($identity)) {
            if (!isset($identity['role'])) {
                $identity['role'] = 'guest';
            }
            $identity = new Zend_Acl_Role($identity['role']);
        } elseif (is_scalar($identity) &amp;&amp; !is_bool($identity)) {
            $identity = new Zend_Acl_Role($identity);
        } elseif (null === $identity) {
            $identity = new Zend_Acl_Role('guest');
        } elseif (!$identity implements Zend_Acl_Role_Interface) {
            throw new Spindle_Model_Exception('Invalid identity provided');
        }
        $this-&gt;_identity = $identity;
        return $this;
    }

    public function getIdentity()
    {
        if (null === $this-&gt;_identity) {
            $auth = Zend_Auth::getInstance();
            if (!$auth-&gt;hasIdentity()) {
                return 'guest';
            }
            $this-&gt;setIdentity($auth-&gt;getIdentity());
        }

        return $this-&gt;_identity;
    }

    /* ... */
}
</code></pre></div>

<p>
    You'll note that <code>setIdentity()</code> has a fair bit of logic -- since
    the identity can be arbitrary, we need to ensure it's usable for our
    purposes. 
</p>

<p>
    Now that we have our roles and our resources, we can address how to add
    checks in our methods to verify user rights prior to executing code.
</p>

<p>
    An expedient way to do this is to use <code>__call()</code> to intercept
    public method calls and proxy them to protected members. However, this has
    the negative side effects of code obscurity and the inability of tools
    (IDEs, ctags, etc) to pick up on the method calls. So, instead, let's build
    a helper method we can use to check ACLs; each method will then be
    responsible for calling on it and acting on its advice.
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    public function checkAcl($action)
    {
        return $this-&gt;getAcl()-&gt;isAllowed(
            $this-&gt;getIdentity(), 
            $this, 
            $action
        );
    }
}
</code></pre></div>

<p>
    Now, let's' hook this into various methods. As an example, consider the
    <code>save()</code> example from my previous entry on using forms with
    models. We might name the requested action 'save', and then query it. We
    then need to make a decision: if the user does not have rights, how do we
    indicate this? Common solutions include:
</p>

<ul>
    <li>Throw an exception</li>
    <li>Unique return value</li>
    <li>Unique return value + marking error condition in the object</li>
</ul>

<p>
    We'll consider insufficient privileges an exceptional condition for this
    example:
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    public function save(array $data)
    {
        if (!$this-&gt;checkAcl('save')) {
            throw new Spindle_Model_Acl_Exception(\&quot;Insufficient rights\&quot;);
        }

        /* ... */
    }

    /* ... */
}
</code></pre></div>

<p>
    When instantiating our model now, we need to either pass in the current
    identity, or set it after instantiation, but prior to calling an
    ACL-controlled action:
</p>

<div class="example"><pre><code class="language-php">
// At instantiation:
$bugModel = new Spindle_Model_BugTracker(array('identity' =&gt; $user));

// Following instantiation:
$bugModel = new Spindle_Model_BugTracker();
$bugModel-&gt;setIdentity($user);

$bugModel-&gt;save($data);
</code></pre></div>

<p>
    (Of course, it will also pull it automatically from the authentication
    session, but it's good to know we can also inject it!)
</p>

<h3>ACLs Revisited</h3>

<p>
    Now that the resource and privilege definition has been moved to the
    model, we can simplify the actual ACL object a bit so that it only defines
    roles and initializes the whitelist:
</p>

<div class="example"><pre><code class="language-php">
class Spindle_Model_Acl_Spindle extends Zend_Acl
{
    public function __construct()
    {
        // Define roles:
        $this-&gt;addRole(new Spindle_Model_Acl_Role_Guest)
             -&gt;addRole(new Spindle_Model_Acl_Role_User,      'guest')
             -&gt;addRole(new Spindle_Model_Acl_Role_Developer, 'user')
             -&gt;addRole(new Spindle_Model_Acl_Role_Manager,   'developer');

        // Deny privileges by default; i.e., create a whitelist
        $this-&gt;deny();
    }
}
</code></pre></div>

<p>
    We still define the roles here, as our user object is only used for
    validating access; we still need to define roles, first.
</p>

<h2>Summary</h2>

<p>
    <code>Zend_Acl</code> is surprisingly simple and flexible. By using
    composition in your model, you can add ACLs trivially to your domain
    workflow, helping keep a separation of responsibilities while losing none of
    the power a good set of ACLs provides.  The important takeaway is that ACLs
    should be part of your model logic, and that you can use object composition
    to achieve this end.
</p>

<p>
    In the next installment, I'll look at how "Return Values are Part of Your
    Model, Too."
</p>
EOT;
$entry->setExtended($extended);

return $entry;
