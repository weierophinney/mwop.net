---
id: 202-Model-Infrastructure
author: matthew
title: 'Model Infrastructure'
draft: false
public: true
created: '2008-12-30T07:35:01-05:00'
updated: '2009-01-05T11:51:07-05:00'
tags:
    0: php
    1: mvc
    3: 'zend framework'
---
In the last two entries in this series on models, I covered [using forms as input filters](/blog/200-Using-Zend_Form-in-Your-Models.html) and [integrating ACLs into models](/blog/201-Applying-ACLs-to-Models.html). In this entry, I tackle some potential infrastructure for your models.

The Model is a complex subject. However, it is often boiled down to either a single model class or a full object relational mapping (ORM). I personally have never been much of a fan of ORMs as they tie models to the underlying database structure; I don't always use a database, nor do I want to rely on an ORM solution too heavily on the off-chance that I later need to refactor to use services or another type of persistence store. On the other hand, the model as a single class is typically too simplistic.

<!--- EXTENDED -->

I *am*, however, a fan of the [Domain Model](http://en.wikipedia.org/wiki/Domain_model). To quote wikipedia,

> [The] domain model can be thought of as a conceptual model of a system which describes the various entities involved in that system and their relationships.

When you think in these terms, you start breaking your system into discrete pieces that you need to manipulate, as well as consider how each piece relates to the others. This type of exercise also helps you stop thinking of your model in terms of database tables; instead, your database becomes the container in which data is persisted from one use of your model to the next. Your model instead is an object that can *do* things with either incoming or stored data — or even completely autonomously.

As an example, when starting with Zend Framework, it's tempting to use `Zend_Db_Table` and `Zend_Db_Table_Row` as models. However, there's one big argument against doing so: when using a Table Data Gateway (TDG) or a Row Data Gateway (RDG), you're returning an object that is tied to the data storage implementation. You're basically putting on blinders and thinking of your model as simply the database table or an individual row, and the returned objects reflect this narrow view point. Furthermore, if you want to re-use your models with service layers, many web services do not work with objects, and of those that do, you likely do not want to expose *all* the properties and methods of the objects returned by your data provider. A row object in ZF, for instance, actually stores the data in protected members, effectively hiding it from services, and also includes methods for deleting the row, ArrayAccess methods, and access to the table object — which gives you full control over the table! The security implications of exposing this directly over a service should be obvious.

Additionally, if in the future you wish to refactor your application to utilize [memcached](http://www.danga.com/memcached/) or a web service, you now not only need to completely rewrite your models, but also all *consumer* code, because the return values from your model have changed.

So, if you're not going to use an ORM or a Table Data Gateway directly, how should you architect your model infrastructure?

What are you modelling?
-----------------------

The principal question to ask is, "What am I modelling?"

Let's look at a rather standard website issue: user management. Typically, you'll get a requirement such as, "Users should be able to register for an account on the site. Once registered, they should be able to login with the credentials they provided. Administrators should be able to ban accounts or grant users higher levels of privileges." That's assuming you actually get good requirement documents, of course.

Most developers will immediately setup a database with a few fields that represent a user — full name, username, email, password, etc — create a form for registration and another for login, write a routine to validate each, create a page to list users for the administration screen… you know the drill. But *what are you modelling?*

The answer is: *users*. So, now it's time to define what a user is, and what a user can do. We have to decide what constitutes a new user, and what constitutes an authenticated user. We have an additional modelling consideration that's often overlooked: user *roles*. There's also the matter of what a *group* of users might look like (since the administrator needs to be able to *list* users), and how we might want to work with groups.

Let's start with narrowing down the definition of a user:

- A user consists of the following metadata:
  - Unique username
  - Full name
  - Email address
  - Hashed password
  - A role within the site
- A *new user* must provide a unique username, their full name, a valid email address, and a password and password verification.
- An *authenticated* user is one who has provided a matching combination of *username* and *password*.
- A user may *logout* of the site.
- A user may be granted a new role.
- A user may be marked as banned.

Notice the fifth piece of metadata? It mentions a "role"? That's something to do with our ACLs — which means that ACLs are part of our user domain. I'll touch on this later.

If you look at the remaining points carefully, you'll note that there's talk of validation, authentication, and user and session persistence. Validation rules are part of our model — and we'll use `Zend_Form` to fulfill that role. Authentication on the web usually consists of both *validating* submitted credentials against *stored* credentials, as well as *persisting* a verified identity in the *session*. This means that other parts of our model include *data persistence* and *session management*. We'll use `Zend_Db_Table` for data persistence, and `Zend_Auth`/`Zend_Session` for identity persistence.

Now, let's turn to defining *lists* of users:

- Administrators should be able to pull lists of users. These lists should allow for:
  - Sorting by username, full name, email address, or role
  - Pagination (i.e., pulling a set number of users from a given offset)
  - Iteration
- Administrators should be able to specify criteria for selecting users to list.

These criteria indicate that a *list* of users should be an object. This list will likely implement the SPL class `Traversable` in some fashion. Looking at this criteria, another aspect of our model becomes clear: we are modelling *user selection* — which includes the ability to specify sorting and selection criteria. The *user selection* object would return a *user list* object, which would consist of *user* objects. User objects define ACL roles and can authenticate users.

We started this article by discussing the Domain Model, and defined it as a system, its entities, and the relations between those entities. We've now identified our domain: user management. The various entities include users, lists of users, ACL roles, a user persistence layer (database), and session persistence layer (web server sessions).

Now that we know what we're modelling, let's look at some of the objects in our model.

Gateway to the Domain
---------------------

We've identified "user management" as the purpose of our model. This will include retrieving and saving individual users, as well as selecting groups of users.

It's clear that we'll need an object to represent a user, as well as another to represent a selection or group of users. But what may not be entirely clear is that we should likely have an object that is used to create new user objects, create selections of users, and basically coordinate several of the related objects — the root ACL and data access, in particular.

This object will be what I'll term our domain *gateway*. It will be used to fetch other objects in our model, and will inject various dependencies into them when doing so, such as the data access and ACLs. The various dependencies may themselves be injected into the gateway — or it can lazy-load them.

The API of this gateway might look something like the following.

```php
// Instantiate the gateway
$userGateway = new Spindle_Model_UserGateway();

// configure the gateway:
$userGateway->setAcl(new Spindle_Acl_Spindle())
            ->setDbAdapter(Zend_Registry::get('db'));

// Alternately, do it all at instantiation:
$userGateway = new Spindle_Model_UserGateway(array(
    'acl'       => new Spindle_Acl_Spindle(),
    'dbAdapter' => Zend_Registry::get('db'),
));

// Grab a single user
$user = $userGateway->retrieve('matthew');

// Grab many users
$users = $userGateway->sort('email', 'ASC')
                     ->criteria(array('banned' => true))
                     ->fetch(array('offset' => 20, 'limit' => 20));

// Better yet, add some transaction script methods with preset criteria:
$users = $userGateway->fetchBannedUsers(array(
    'offset' => 20, 
    'limit'  => 20,
    'sort'   = array('email', 'ASC'),
));

// Create a new user:
$user = $userGateway->createUser(array(
    'username' => 'matthew',
    'fullname' => "Matthew Weier O'Phinney",
    'password' => 'secret',
    'email'    => 'matthew@local',
));
```

The basic idea is to provide a scaffold for lazyloading necessary objects, methods for specifying options (such as sort order, criteria, limits, etc), and transaction methods for retrieving individual users and groups of users.

Of Value Objects and Record Sets
--------------------------------

To other objects we've identified in our model are *users* and *user lists*. How should we define these?

The traditional answer is as *value* or *data transfer* objects and *record sets*. The Value Object is a standard design pattern used to aggregate all metadata that defines a single value. The Record Set is an aggregation of Value Objects.

### Value Objects

Martin Fowler makes a differentiation between value objects and data transfer objects in his book "Patterns of Enterprise Application Architecture" (PoEAA). In it, he associates value objects with language variable types (i.e., Value Objects act as custom variable types), while defining data transfer objects as aggregating related values for the purpose of serialization and data transfer between objects.

In Java, however, value objects are arbitrary objects used to store a specific set of attributes — very similar to the data transfer object. For the purposes of this discussion, I'll use the term "value object," as it will be familiar to those with a Java background, and to indicate that we are aggregating a unique *value* that is the sum of a number of *attributes*.

Basically, all of this verbiage describes something incredibly simple in implementation: an object with a specific set of attributes or properties. If you've been doing any OOP programming in PHP, this is the most natural and fundamental thing you can do.

```php
class Spindle_Model_User
{
    protected $_data = array(
        'username' => null,
        'email'    => null,
        'fullname' => '',
        'role'     => 'guest',
    );

    public function __construct($data)
    {
        $this->populate($data);

        if (!isset($this->username)) {
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

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid property "' . $name . '"');
        }
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        if (isset($this->$name)) {
            $this->_data[$name] = null;
        }
    }
}
```

The above example is fairly simplistic, but it gets the idea across: the object defines a limited range of valid values, and enforces that only these values may be set — as well as which values are required. You could certainly add accessor and mutator methods to enforce consistent access to member data, but the above will certainly suffice for many use cases. (I'll look at data integrity momentarily.)

One addition you might make to the class definition is to add some conversions from different types of objects. For instance, if you know that you'll be using `Zend_Db_Table` within your model, you might want to add the ability for your value object to accept a `Zend_Db_Table_Row` object, and pull its values from there:

```php
class Spindle_Model_User
{
    /* ... */

    public function populate($data)
    {
        if ($data instanceof Zend_Db_Table_Row_Abstract) {
            $data = $data->toArray();
        } elseif (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /* ... */
}
```

This will help keep your model code clean, as you can potentially take the results of data storage operations and push them directly into your value object — resulting in less re-working of code.

Now, what about data integrity? This is where `Zend_Form` comes into play. Don't think of `Zend_Form` as a web form; think of it as an input filter that has the ability to render itself as a form if so desired. If we think of it as an input filter, we can use it for data integrity:

```php
class Spindle_Model_User
{
    /* ... */

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception('Invalid property "' . $name . '"');
        }

        $inputFilter = $this->getForm();
        if ($element = $inputFilter->getElement($name)) {
            if (!$element->isValid($value)) {
                throw new Exception(sprintf(
                    'Invalid value provided for "%s": %s', 
                    $name, 
                    implode(', ', $element->getMessages())
                );
            }
        }

        $this->_data[$name] = $value;
    }

    /* ... */

    protected $_form;

    public function getForm()
    {
        if (null === $this->_form) {
            $this->_form = new Spindle_Form_User();
        }
        return $this->_form;
    }

    /* ... */
}
```

One note: if your model contains metadata that will never be represented as part of a form, you shoould look into using `Zend_Filter_Input` or custom validation chains instead of `Zend_Form`. That's outside the scope of this article, however.

Now that we have input filtering out of the way, how shall we address saving a user? Recall in our discussion of the domain gateway that one of its responsibilities is injecting other dependencies into our objects. I find it's often easier to inject the *gateway* into objects, and then pull what I need from it. Let's look at how that might work for saving the user.

```php
class Spindle_Model_User
{
    /* ... */

    protected $_gateway;

    public function __construct($data, $gateway)
    {
        $this->setGateway($gateway);

        /* ... */
    }

    public function setGateway(Spindle_Model_UserGateway $gateway)
    {
        $this->_gateway = $gateway;
        return $this;
    }

    public function getGateway()
    {
        return $this->_gateway;
    }

    public function save()
    {
        $gateway = $this->getGateway();
        $dbTable = $gateway->getDbTable('user');

        if ($row = $dbTable->find($this->username)) {
            foreach ($this->_data as $key => $value) {
                $row->$key = $value;
            }
            $row->save();
        } else {
            $dbTable->insert($this->_data);
        }
    }

    /* ... */
}
```

Note that the constructor now has a second argument — the gateway. This ensures that the user always has a gateway instance, which further ensures that operations like the one listed — retrieving the `Zend_Db_Table` instance from the gateway — will always work. In this example, we simply check to see if a row already exists, and then save the record accordingly.

Another requirement we identified was that a user be able to authenticate itself. This can be done trivially by implementing `Zend_Auth_Adapter_Interface`:

```php
class Spindle_Model_User implements Zend_Auth_Adapter_Interface
{
    /* ... */

    public function authenticate()
    {
        $gateway = $this->getGateway();
        $table   = $manager->getDbTable('user');
        $select  = $table->select();
        $select->where('username = ?', $this->username)
               ->where('password = ?', $this->password)
               ->where('date_banned IS NULL');
        $user = $table->fetchRow($select);
        if (null === $user) {
            // failed
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_UNCATEGORIZED,
                null
            );
        } else {
            // passed
            $this->populate($user);
            unset($this->password);
            $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this);
        }
        return $result;
    }

    /* ... */
}
```

To authenticate a user, you would create a new user object with the username and password, and then attempt to authenticate it:

```php
$auth = Zend_Auth::getInstance();
$user = $gateway->createUser(array(
    'username' => $username,
    'password' => $password,
));
if ($auth->authenticate($user)) {
    // AUTHENTICATED!
}
```

This also has the effect of populating the user from the persistence store, as well as storing the identity in the session.

I covered ACL roles [previously](/blog/201-Applying-ACLs-to-Models.html), so I won't go into that here. However, you should now be getting a pretty clear understanding of how this object works, and how it coordinates with the user gateway. It should also illustrate that this aspect of our model is much, much more than simply data access: we're coordinating authentication, input filtering, and ACLs — and providing a fairly simple API for manipulating the user itself.

### Record Sets

A Record Set is similarly easy to create. Typically, you will merely want the object to be iterable and countable. Like the user object, we'll require a gateway instance in the constructor.

```php
class Spindle_Model_Users implements Iterator,Countable
{
    protected $_count;
    protected $_gateway;
    protected $_resultSet;

    public function __construct($results, $gateway)
    {
        $this->setGateway($gateway);
        $this->_resultSet = $results;
    }

    public function setGateway(Spindle_Model_UserGateway $gateway)
    {
        $this->_gateway = $gateway;
        return $this;
    }

    public function getGateway()
    {
        return $this->_gateway;
    }

    public function count()
    {
        if (null === $this->_count) {
            $this->_count = count($this->_resultSet);
        }
        return $this->_count;
    }

    public function current()
    {
        if ($this->_resultSet instanceof Iterator) {
            $key = $this->_resultSet->key();
        } else {
            $key = key($this->_resultSet);
        }
        $result  = $this->_resultSet[$key];
        if (!$result instanceof Spindle_Model_User) {
            $gateway = $this->getGateway();
            $result  = $gateway->createUser($result);
            $this->_resultSet[$key] = $result;
        }
        return $result;
    }

    public function key()
    {
        return key($this-_resultSet);
    }

    public function next()
    {
        return next($this->_resultSet);
    }

    public function rewind()
    {
        return reset($this->_resultSet);
    }

    public function valid()
    {
        return (bool) $this->current();
    }
}
```

The logic here is incredibly simple. The main benefit from using a Record Set over an array is that it allows you to ensure the types of each item in the set, as well as allow your consuming code to perform type hinting on the Record Set class.

Using Value Objects and Record Sets in Your Gateway
---------------------------------------------------

Within your gateway class, it is then your responsibility to ensure that instances of your new classes are returned. As an example, let's look at some easy `fetch()` and `fetchAll()` methods:

```php
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetch($id)
    {
        $dbTable = $this->getDbTable();
        $select  = $dbTable->select();
        $select->where('id = ?', $id);
        $result = $dbTable->fetchRow($select);
        if (null !== $result) {
            $result = $this->createUser($result);
        }
        return $result;
    }

    public function fetchAll()
    {
        $result = $this->getDbTable()->fetchAll();
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
```

You'll notice the downside immediately: you have to introduce new objects, and that means re-casting of data. But let's look at it from a consumer viewpoint: the consuming code is looking for return types of `Spindle_Model_User` and `Spindle_Model_Users`.

But what's the point of the gateway, really? Couldn't both the value object and result set object simply inherit from a common base? Certainly they could. However, one common use case I have for gateways is providing pre-defined methods encapsulating common selection criteria. For instance, let's say you wanted to retrieve all *banned* users, and that this will be a common task. Define a method for it:

```php
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetchBannedUsers()
    {
        $dbTable = $this->getDbTable();
        $select  = $dbTable->select()->where('date_banned IS NOT NULL');
        $result  = $dbTable->fetchAll($select);
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
```

This is admittedly a trivial example, but it clearly illustrates the benefits: we now have an API method that tells us, in plain English, what operation we are performing, and provides a repeatable way to do it. The user consuming the model needs not know anything about how it works under the hood, only that they can expect to get a list of banned users when they call it.

Another key benefit to creating a gateway is for those times when we need to replace our data access layer with something else. Let's refactor our code to use a service instead:

```php
class Spindle_Model_UserGateway
{
    /* ... */

    public function fetch($id)
    {
        $result  = $this->getService()->fetchUser($id);
        return $this->createUser($result);
    }

    public function fetchAll()
    {
        $result = $this->getService()->fetchAll();
        return new Spindle_Model_Users($result, $this);
    }

    /* ... */
}
```

From a consumer standpoint, *nothing has changed*; they are still calling the same methods, and receiving the same responses. This is absolutely key in creating maintainable, future proof code.

Summary
-------

The solutions presented here are by no means canonical. You may find that your own models do not need a gateway class, or that you never work with lists of objects. Hopefully, however, I've illustrated that a model should cleanly provide a separation of concerns and consist of discrete objects — whether they are directly related to your model, or related to aspects of how your model *does stuff*, like validation and data persistence. You should strive to make your models as simple as possible, while still meeting each of your requirements. The end result should be a re-usable, testable suite of functionality, and careful architecture of your solution should make it robust and easy to refactor in the future.

**Updates:**

- *2009-01-04:* Updated `__unset()` per Gabriel's feedback (comment #14)
- *2009-01-05:* Updated `current()` implementation per Falk's feedback (comment #15)
- *2009-01-05:* Updated `current()` implementation per Martin's feedback (comment #15.1.1)
