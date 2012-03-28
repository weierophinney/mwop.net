<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('202-Model-Infrastructure');
$entry->setTitle('Model Infrastructure');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1230640501);
$entry->setUpdated(1231174267);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'mvc',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    In the last two entries in this series on models, I covered 
    <a href="http://weierophinney.net/matthew/archives/200-Using-Zend_Form-in-Your-Models.html">using forms as input filters</a> 
    and <a href="http://weierophinney.net/matthew/archives/201-Applying-ACLs-to-Models.html">integrating ACLs into models</a>. 
    In this entry, I tackle some potential infrastructure for your models.
</p>

<p>
    The Model is a complex subject. However, it is often boiled down to either a
    single model class or a full object relational mapping (ORM). I personally
    have never been much of a fan of ORMs as they tie models to the underlying
    database structure; I don't always use a database, nor do I want
    to rely on an ORM solution too heavily on the off-chance that I later need
    to refactor to use services or another type of persistence store. On the
    other hand, the model as a single class is typically too simplistic.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I <em>am</em>, however, a fan of the <a href="http://en.wikipedia.org/wiki/Domain_model">Domain Model</a>. To quote wikipedia, 
</p>

<blockquote>
    [The] domain model can be thought of as a conceptual model of a system
    which describes the various entities involved in that system and their
    relationships.
</blockquote>

<p>
    When you think in these terms, you start breaking your system into
    discrete pieces that you need to manipulate, as well as consider how each
    piece relates to the others. This type of exercise also helps you stop
    thinking of your model in terms of database tables; instead, your database
    becomes the container in which data is persisted from one use of your model
    to the next. Your model instead is an object that can <em>do</em> things
    with either incoming or stored data -- or even completely autonomously.
</p>

<p>
    As an example, when starting with Zend Framework, it's tempting to use
    <code>Zend_Db_Table</code> and <code>Zend_Db_Table_Row</code> as models.
    However, there's one big argument against doing so: when using a Table
    Data Gateway (TDG) or a Row Data Gateway (RDG), you're returning an object
    that is tied to the data storage implementation. You're basically putting on
    blinders and thinking of your model as simply the database table or an
    individual row, and the returned objects reflect this narrow view point.
    Furthermore, if you want to re-use your models with service layers, many web
    services do not work with objects, and of those that do, you likely do not
    want to expose <em>all</em> the properties and methods of the objects
    returned by your data provider. A row object in ZF, for instance, actually
    stores the data in protected members, effectively hiding it from services,
    and also includes methods for deleting the row, ArrayAccess methods, and
    access to the table object -- which gives you full control over the table!
    The security implications of exposing this directly over a service should be
    obvious.
</p>

<p>
    Additionally, if in the future you wish to refactor your application to
    utilize <a href="http://www.danga.com/memcached/">memcached</a> or a web
    service, you now not only need to completely rewrite your models, but also
    all <em>consumer</em> code, because the return values from your model have
    changed.
</p>

<p>
    So, if you're not going to use an ORM or a Table Data Gateway directly, how
    should you architect your model infrastructure?
</p>

<h2>What are you modelling?</h2>

<p>
    The principal question to ask is, "What am I modelling?"
</p>

<p>
    Let's look at a rather standard website issue: user management. Typically,
    you'll get a requirement such as, "Users should be able to register for an
    account on the site. Once registered, they should be able to login with the
    credentials they provided. Administrators should be able to ban accounts or
    grant users higher levels of privileges." That's assuming you actually get
    good requirement documents, of course.
</p>

<p>
    Most developers will immediately setup a database with a few fields that
    represent a user -- full name, username, email, password, etc -- create a
    form for registration and another for login, write a routine to validate
    each, create a page to list users for the administration screen... you know
    the drill. But <em>what are you modelling?</em>
</p>

<p>
    The answer is: <em>users</em>. So, now it's time to define what a user is,
    and what a user can do. We have to decide what constitutes a new user, and
    what constitutes an authenticated user.  We have an additional modelling
    consideration that's often overlooked: user <em>roles</em>. There's also the
    matter of what a <em>group</em> of users might look like (since the
    administrator needs to be able to <em>list</em> users), and how we might
    want to work with groups.
</p>

<p>
    Let's start with narrowing down the definition of a user:
</p>

<ul>
    <li>A user consists of the following metadata:<ul>
        <li>Unique username</li>
        <li>Full name</li>
        <li>Email address</li>
        <li>Hashed password</li>
        <li>A role within the site</li>
    </ul></li>
    <li>A <em>new user</em> must provide a unique username, their full name, a
    valid email address, and a password and password verification.</li>
    <li>An <em>authenticated</em> user is one who has provided a matching
    combination of <em>username</em> and <em>password</em>.</li>
    <li>A user may <em>logout</em> of the site.</li>
    <li>A user may be granted a new role.</li>
    <li>A user may be marked as banned.</li>
</ul>

<p>
    Notice the fifth piece of metadata? It mentions a "role"? That's something
    to do with our ACLs -- which means that ACLs are part of our user domain.
    I'll touch on this later.
</p>

<p>
    If you look at the remaining points carefully, you'll note that there's talk
    of validation, authentication, and user and session persistence.  Validation
    rules are part of our model -- and we'll use <code>Zend_Form</code> to
    fulfill that role.  Authentication on the web usually consists of both
    <em>validating</em> submitted credentials against <em>stored</em>
    credentials, as well as <em>persisting</em> a verified identity in the
    <em>session</em>. This means that other parts of our model include <em>data
        persistence</em> and <em>session management</em>. We'll use
    <code>Zend_Db_Table</code> for data persistence, and
    <code>Zend_Auth</code>/<code>Zend_Session</code> for identity persistence.
</p>

<p>
    Now, let's turn to defining <em>lists</em> of users:
</p>

<ul>
    <li>Administrators should be able to pull lists of users. These lists should
    allow for:<ul>
        <li>Sorting by username, full name, email address, or role</li>
        <li>Pagination (i.e., pulling a set number of users from a given offset)</li>
        <li>Iteration</li>
    </ul></li>
    <li>Administrators should be able to specify criteria for selecting users to
    list.</li>
</ul>

<p>
    These criteria indicate that a <em>list</em> of users should be an object.
    This list will likely implement the SPL class <code>Traversable</code> in
    some fashion. Looking at this criteria, another aspect of our model becomes
    clear: we are modelling <em>user selection</em> -- which includes the
    ability to specify sorting and selection criteria. The <em>user
        selection</em> object would return a <em>user list</em> object, which
    would consist of <em>user</em> objects. User objects define ACL roles and
    can authenticate users. 
</p>

<p>
    We started this article by discussing the Domain Model, and defined it as a
    system, its entities, and the relations between those entities. We've now
    identified our domain: user management. The various entities include users,
    lists of users, ACL roles, a user persistence layer (database), and session
    persistence layer (web server sessions).
</p>

<p>
    Now that we know what we're modelling, let's look at some of the objects in
    our model.
</p>

<h2>Gateway to the Domain</h2>

<p>
    We've identified "user management" as the purpose of our model. This will
    include retrieving and saving individual users, as well as selecting groups
    of users.
</p>

<p>
    It's clear that we'll need an object to represent a user, as well as another
    to represent a selection or group of users. But what may not be entirely
    clear is that we should likely have an object that is used to create new
    user objects, create selections of users, and basically coordinate several
    of the related objects -- the root ACL and data access, in particular.
</p>

<p>
    This object will be what I'll term our domain <em>gateway</em>. It will be
    used to fetch other objects in our model, and will inject various
    dependencies into them when doing so, such as the data access and ACLs. The
    various dependencies may themselves be injected into the gateway -- or it
    can lazy-load them.
</p>
    
<p>
    The API of this gateway might look something like the following.
</p>

<div class="example"><pre><code lang="php">
// Instantiate the gateway
$userGateway = new Spindle_Model_UserGateway();

// configure the gateway:
$userGateway-&gt;setAcl(new Spindle_Acl_Spindle())
            -&gt;setDbAdapter(Zend_Registry::get('db'));

// Alternately, do it all at instantiation:
$userGateway = new Spindle_Model_UserGateway(array(
    'acl'       =&gt; new Spindle_Acl_Spindle(),
    'dbAdapter' =&gt; Zend_Registry::get('db'),
));

// Grab a single user
$user = $userGateway-&gt;retrieve('matthew');

// Grab many users
$users = $userGateway-&gt;sort('email', 'ASC')
                     -&gt;criteria(array('banned' =&gt; true))
                     -&gt;fetch(array('offset' =&gt; 20, 'limit' =&gt; 20));

// Better yet, add some transaction script methods with preset criteria:
$users = $userGateway-&gt;fetchBannedUsers(array(
    'offset' =&gt; 20, 
    'limit'  =&gt; 20,
    'sort'   = array('email', 'ASC'),
));

// Create a new user:
$user = $userGateway-&gt;createUser(array(
    'username' =&gt; 'matthew',
    'fullname' =&gt; \&quot;Matthew Weier O'Phinney\&quot;,
    'password' =&gt; 'secret',
    'email'    =&gt; 'matthew@local',
));
</code></pre></div>

<p>
    The basic idea is to provide a scaffold for lazyloading necessary objects,
    methods for specifying options (such as sort order, criteria, limits, etc),
    and transaction methods for retrieving individual users and groups of users.
</p>

<h2>Of Value Objects and Record Sets</h2>

<p>
    To other objects we've identified in our model are <em>users</em>
    and <em>user lists</em>. How should we define these?
</p>

<p>
    The traditional answer is as <em>value</em> or <em>data transfer</em>
    objects and <em>record sets</em>.  The Value Object is a standard design
    pattern used to aggregate all metadata that defines a single value. The
    Record Set is an aggregation of Value Objects.
</p>

<h3>Value Objects</h3>

<p>
    Martin Fowler makes a differentiation between value objects and data
    transfer objects in his book "Patterns of Enterprise Application
    Architecture" (PoEAA). In it, he associates value objects with language
    variable types (i.e., Value Objects act as custom variable types), while
    defining data transfer objects as aggregating related values for the purpose
    of serialization and data transfer between objects. 
</p>
    
<p>
    In Java, however, value objects are arbitrary objects used to store a
    specific set of attributes -- very similar to the data transfer object. For
    the purposes of this discussion, I'll use the term "value object," as it
    will be familiar to those with a Java background, and to indicate that we
    are aggregating a unique <em>value</em> that is the sum of a number of
    <em>attributes</em>.
</p>

<p>
    Basically, all of this verbiage describes something incredibly simple in
    implementation: an object with a specific set of attributes or properties.
    If you've been doing any OOP programming in PHP, this is the most natural
    and fundamental thing you can do.
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_User
{
    protected $_data = array(
        'username' =&gt; null,
        'email'    =&gt; null,
        'fullname' =&gt; '',
        'role'     =&gt; 'guest',
    );

    public function __construct($data)
    {
        $this-&gt;populate($data);

        if (!isset($this-&gt;username)) {
            throw new Exception('Initial data must contain an id');
        }
    }

    public function populate($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($data as $key =&gt; $value) {
            $this-&gt;$key = $value;
        }
        return $this;
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this-&gt;_data)) {
            throw new Exception('Invalid property \&quot;' . $name . '\&quot;');
        }
        $this-&gt;_data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this-&gt;_data)) {
            return $this-&gt;_data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this-&gt;_data[$name]);
    }

    public function __unset($name)
    {
        if (isset($this-&gt;$name)) {
            $this-&gt;_data[$name] = null;
        }
    }
}
</code></pre></div>

<p>
    The above example is fairly simplistic, but it gets the idea across: the
    object defines a limited range of valid values, and enforces that only these
    values may be set -- as well as which values are required. You could
    certainly add accessor and mutator methods to enforce consistent access
    to member data, but the above will certainly suffice for many use cases.
    (I'll look at data integrity momentarily.)
</p>

<p>
    One addition you might make to the class definition is to add some
    conversions from different types of objects. For instance, if you know that
    you'll be using <code>Zend_Db_Table</code> within your model, you might want
    to add the ability for your value object to accept a
    <code>Zend_Db_Table_Row</code> object, and pull its values from there:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_User
{
    /* ... */

    public function populate($data)
    {
        if ($data instanceof Zend_Db_Table_Row_Abstract) {
            $data = $data-&gt;toArray();
        } elseif (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($data as $key =&gt; $value) {
            $this-&gt;$key = $value;
        }
        return $this;
    }

    /* ... */
}
</code></pre></div>

<p>
    This will help keep your model code clean, as you can potentially take the
    results of data storage operations and push them directly into your value
    object -- resulting in less re-working of code.
</p>

<p>
    Now, what about data integrity? This is where <code>Zend_Form</code> comes
    into play. Don't think of <code>Zend_Form</code> as a web form; think of it
    as an input filter that has the ability to render itself as a form if so
    desired. If we think of it as an input filter, we can use it for data
    integrity:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_User
{
    /* ... */

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this-&gt;_data)) {
            throw new Exception('Invalid property \&quot;' . $name . '\&quot;');
        }

        $inputFilter = $this-&gt;getForm();
        if ($element = $inputFilter-&gt;getElement($name)) {
            if (!$element-&gt;isValid($value)) {
                throw new Exception(sprintf(
                    'Invalid value provided for \&quot;%s\&quot;: %s', 
                    $name, 
                    implode(', ', $element-&gt;getMessages())
                );
            }
        }

        $this-&gt;_data[$name] = $value;
    }

    /* ... */

    protected $_form;

    public function getForm()
    {
        if (null === $this-&gt;_form) {
            $this-&gt;_form = new Spindle_Form_User();
        }
        return $this-&gt;_form;
    }

    /* ... */
}
</code></pre></div>

<p>
    One note: if your model contains metadata that will never be represented as
    part of a form, you shoould look into using <code>Zend_Filter_Input</code> 
    or custom validation chains instead of <code>Zend_Form</code>. That's
    outside the scope of this article, however.
</p>

<p>
    Now that we have input filtering out of the way, how shall we address saving
    a user? Recall in our discussion of the domain gateway that one of its
    responsibilities is injecting other dependencies into our objects. I find
    it's often easier to inject the <em>gateway</em> into objects, and then pull
    what I need from it. Let's look at how that might work for saving the user.
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_User
{
    /* ... */

    protected $_gateway;

    public function __construct($data, $gateway)
    {
        $this-&gt;setGateway($gateway);

        /* ... */
    }

    public function setGateway(Spindle_Model_UserGateway $gateway)
    {
        $this-&gt;_gateway = $gateway;
        return $this;
    }

    public function getGateway()
    {
        return $this-&gt;_gateway;
    }

    public function save()
    {
        $gateway = $this-&gt;getGateway();
        $dbTable = $gateway-&gt;getDbTable('user');

        if ($row = $dbTable-&gt;find($this-&gt;username)) {
            foreach ($this-&gt;_data as $key =&gt; $value) {
                $row-&gt;$key = $value;
            }
            $row-&gt;save();
        } else {
            $dbTable-&gt;insert($this-&gt;_data);
        }
    }

    /* ... */
}
</code></pre></div>

<p>
    Note that the constructor now has a second argument -- the gateway. This
    ensures that the user always has a gateway instance, which further ensures
    that operations like the one listed -- retrieving the
    <code>Zend_Db_Table</code> instance from the gateway -- will always work. In
    this example, we simply check to see if a row already exists, and then save
    the record accordingly. 
</p>

<p>
    Another requirement we identified was that a user be able to authenticate
    itself. This can be done trivially by implementing
    <code>Zend_Auth_Adapter_Interface</code>:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_User implements Zend_Auth_Adapter_Interface
{
    /* ... */

    public function authenticate()
    {
        $gateway = $this-&gt;getGateway();
        $table   = $manager-&gt;getDbTable('user');
        $select  = $table-&gt;select();
        $select-&gt;where('username = ?', $this-&gt;username)
               -&gt;where('password = ?', $this-&gt;password)
               -&gt;where('date_banned IS NULL');
        $user = $table-&gt;fetchRow($select);
        if (null === $user) {
            // failed
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                null
            );
        } else {
            // passed
            $this-&gt;populate($user);
            unset($this-&gt;password);
            $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this);
        }
        return $result;
    }

    /* ... */
}
</code></pre></div>

<p>
    To authenticate a user, you would create a new user object with the username
    and password, and then attempt to authenticate it:
</p>

<div class="example"><pre><code lang="php">
$auth = Zend_Auth::getInstance();
$user = $gateway-&gt;createUser(array(
    'username' =&gt; $username,
    'password' =&gt; $password,
));
if ($auth-&gt;authenticate($user)) {
    // AUTHENTICATED!
}
</code></pre></div>

<p>
    This also has the effect of populating the user from the persistence store,
    as well as storing the identity in the session.
</p>

<p>
    I covered ACL roles <a href="http://weierophinney.net/matthew/archives/201-Applying-ACLs-to-Models.html">previously</a>,
    so I won't go into that here. However, you should now be getting a pretty
    clear understanding of how this object works, and how it coordinates with
    the user gateway. It should also illustrate that this aspect of our model
    is much, much more than simply data access: we're coordinating
    authentication, input filtering, and ACLs -- and providing a fairly simple
    API for manipulating the user itself.
</p>

<h3>Record Sets</h3>

<p>
    A Record Set is similarly easy to create. Typically, you will merely want
    the object to be iterable and countable. Like the user object, we'll require
    a gateway instance in the constructor.
</p>

<div class="example"><pre><code lang="php">
&lt;?php
class Spindle_Model_Users implements Iterator,Countable
{
    protected $_count;
    protected $_gateway;
    protected $_resultSet;

    public function __construct($results, $gateway)
    {
        $this-&gt;setGateway($gateway);
        $this-&gt;_resultSet = $results;
    }

    public function setGateway(Spindle_Model_UserGateway $gateway)
    {
        $this-&gt;_gateway = $gateway;
        return $this;
    }

    public function getGateway()
    {
        return $this-&gt;_gateway;
    }

    public function count()
    {
        if (null === $this-&gt;_count) {
            $this-&gt;_count = count($this-&gt;_resultSet);
        }
        return $this-&gt;_count;
    }

    public function current()
    {
        if ($this-&gt;_resultSet instanceof Iterator) {
            $key = $this-&gt;_resultSet-&gt;key();
        } else {
            $key = key($this-&gt;_resultSet);
        }
        $result  = $this-&gt;_resultSet[$key];
        if (!$result instanceof Spindle_Model_User) {
            $gateway = $this-&gt;getGateway();
            $result  = $gateway-&gt;createUser($result);
            $this-&gt;_resultSet[$key] = $result;
        }
        return $result;
    }

    public function key()
    {
        return key($this-_resultSet);
    }

    public function next()
    {
        return next($this-&gt;_resultSet);
    }

    public function rewind()
    {
        return reset($this-&gt;_resultSet);
    }

    public function valid()
    {
        return (bool) $this-&gt;current();
    }
}
</code></pre></div>

<p>
    The logic here is incredibly simple. The main benefit from using a Record
    Set over an array is that it allows you to ensure the types of each item in
    the set, as well as allow your consuming code to perform type hinting on the
    Record Set class.
</p>

<h2>Using Value Objects and Record Sets in Your Gateway</h2>

<p>
    Within  your gateway class, it is then your responsibility to ensure that
    instances of your new classes are returned. As an example, let's look at
    some easy <code>fetch()</code> and <code>fetchAll()</code> methods:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetch($id)
    {
        $dbTable = $this-&gt;getDbTable();
        $select  = $dbTable-&gt;select();
        $select-&gt;where('id = ?', $id);
        $result = $dbTable-&gt;fetchRow($select);
        if (null !== $result) {
            $result = $this-&gt;createUser($result);
        }
        return $result;
    }

    public function fetchAll()
    {
        $result = $this-&gt;getDbTable()-&gt;fetchAll();
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
</code></pre></div>

<p>
    You'll notice the downside immediately: you have to introduce new objects,
    and that means re-casting of data. But let's look at it from a consumer
    viewpoint: the consuming code is looking for return types of
    <code>Spindle_Model_User</code> and <code>Spindle_Model_Users</code>. 
</p>
    
<p>
    But what's the point of the gateway, really? Couldn't both the value object
    and result set object simply inherit from a common base? Certainly they
    could. However, one common use case I have for gateways is providing
    pre-defined methods encapsulating common selection criteria. For instance,
    let's say you wanted to retrieve all <em>banned</em> users, and that this
    will be a common task. Define a method for it:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetchBannedUsers()
    {
        $dbTable = $this-&gt;getDbTable();
        $select  = $dbTable-&gt;select()-&gt;where('date_banned IS NOT NULL');
        $result  = $dbTable-&gt;fetchAll($select);
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
</code></pre></div>

<p>
    This is admittedly a trivial example, but it clearly illustrates the
    benefits: we now have an API method that tells us, in plain English, what
    operation we are performing, and provides a repeatable way to do it. The
    user consuming the model needs not know anything about how it works under
    the hood, only that they can expect to get a list of banned users when they
    call it.
</p>
    
<p>
    Another key benefit to creating a gateway is for those times when we need to
    replace our data access layer with something else.  Let's refactor our code
    to use a service instead:
</p>

<div class="example"><pre><code lang="php">
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetch($id)
    {
        $result  = $this-&gt;getService()-&gt;fetchUser($id);
        return $this-&gt;createUser($result);
    }

    public function fetchAll()
    {
        $result = $this-&gt;getService()-&gt;fetchAll();
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
</code></pre></div>

<p>
    From a consumer standpoint, <em>nothing has changed</em>; they are still
    calling the same methods, and receiving the same responses. This is
    absolutely key in creating maintainable, future proof code.
</p>

<h2>Summary</h2>

<p>
    The solutions presented here are by no means canonical. You may find that
    your own models do not need a gateway class, or that you never work with
    lists of objects. Hopefully, however, I've illustrated that a model should
    cleanly provide a separation of concerns and consist of discrete objects --
    whether they are directly related to your model, or related to aspects of
    how your model <em>does stuff</em>, like validation and data persistence.
    You should strive to make your models as simple as possible, while still
    meeting each of your requirements. The end result should be a re-usable,
    testable suite of functionality, and careful architecture of your solution
    should make it robust and easy to refactor in the future.
</p>

<p>
    <b>Updates:</b>
</p>
<ul>
    <li><em>2009-01-04:</em> Updated <code>__unset()</code> per Gabriel's
    feedback (comment #14)</li>
    <li><em>2009-01-05:</em> Updated <code>current()</code> implementation per
    Falk's feedback (comment #15)</li>
    <li><em>2009-01-05:</em> Updated <code>current()</code> implementation per
    Martin's feedback (comment #15.1.1)</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;