---
id: 201-Applying-ACLs-to-Models
author: matthew
title: 'Applying ACLs to Models'
draft: false
public: true
created: '2008-12-24T07:26:40-05:00'
updated: '2008-12-27T17:48:15-05:00'
tags:
    0: php
    2: 'zend framework'
---
In my last post, I [discussed using Zend_Form as a combination input filter/value object within your models](/blog/200-Using-Zend_Form-in-Your-Models.html). In this post, I'll discuss using Access Control Lists (ACLs) as part of your modelling strategy.

ACLs are used to indicate *who* has access to do *what* on a given *resource*. In the paradigm I will put forward, your *resource* is your model, and the *what* are the various methods of the model. If you finesse a bit, you'll have "user" objects that act as your *who*.

Just like with forms, you want to put your ACLs as close to your domain logic as possible; in fact, ACLs are *part* of your domain.

<!--- EXTENDED -->

First up, however, let's review `Zend_Acl`.

Zend_Acl in a Nutshell
----------------------

`Zend_Acl` is divided into three areas of responsibility:

- **Resources** are objects to which access is controlled
- **Roles** are objects which may request access to one or more *resources*
- **ACLs** provide a tree structure to which resources and roles may be added, and which map *access* rules between them.

`Zend_Acl` is primarily engineered to be configured and manipulated
programmatically. While you can certainly write functionality to pull the
information out of a data store — say, an LDAP directory or a database — in
many cases, you don't need to. Let's look at this simple ACL definition:

```php
class Spindle_Model_Acl_Spindle extends Zend_Acl
{
    public function __construct()
    {
        // Define roles:
        $this->addRole(new Spindle_Model_Acl_Role_Guest)
             ->addRole(new Spindle_Model_Acl_Role_User,      'guest')
             ->addRole(new Spindle_Model_Acl_Role_Developer, 'user')
             ->addRole(new Spindle_Model_Acl_Role_Manager,   'developer');

        // Deny privileges by default; i.e., create a whitelist
        $this->deny();

        // Define resources and add privileges
        $this->add(new Spindle_Model_Acl_Resource_Bug)
             ->allow('guest',     'bug', array('list', 'view'))
             ->allow('user',      'bug', array('add', 'comment', 'link', 'close'))
             ->allow('developer', 'bug', array('update', 'delete'));

        $this->add(new Spindle_Model_Acl_Resource_Comment)
             ->allow('guest',     'comment', array('view', 'list'))
             ->allow('user',      'comment', array('add'))
             ->allow('developer', 'comment', array('delete'));
    }
}
```

In this example, we do several things:

- Define our roles. You'll note that several role definitions take an additional argument. In each case, this argument specifies what role the new role inherits from. Thus, as we apply privileges for one role, any role that inherits from that role will also receive those privileges.
- Create a whitelist. The `deny()` method, when called before any other permissions, tells `Zend_Acl` that we want to deny permission unless we've specifically allowed it.
- Add resources.
- Specify privileges available on each resource based on the role accessing the resource. This is done via the `allow()` method.

*Resources* and *Roles* in `Zend_Acl` need merely implement the appropriate interfaces. These interfaces merely define a single method apiece, each of which returns a string identifier used in the object graph in `Zend_Acl`. As an example:

```php
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
```

As you may notice, these are trivial to implement — and the point is that they can be mixed in to your model classes to give them semantic meaning. That said, there's one caveat: when defining the actual ACL rules — which map roles and resources — the specified roles and resources must *already exist* in the ACL tree. As such, I find it convenient to define my roles early, and then add resources and privileges on an ad hoc basis.

By grouping the base ACL definition in an object, we now have a re-usable ACL that we can pass around or use within other contexts, finally bringing us to our model.

Using Zend_Acl in Models
------------------------

### Roles

Typically in Zend Framework, you'll authenticate a user using `Zend_Auth`, which will persist their "identity" in the session. This "identity" can be anything: a string, an array, an object. This latter gives some fantastic potential: if the object implements `Zend_Acl_Role_Interface`, then it can be used for ACL checks.

Let's define a "User" object that implements the role interface. Internally, we'll store the user's defined role as part of the object, and have the `getRoleId()` method return that value.

```php
class Spindle_Model_UserManager_User implements Zend_Acl_Role_Interface
{
    /* ... */

    public function getRoleId()
    {
        if (!isset($this->role)) {
            return 'guest';
        }
        return $this->role;
    }

    /* ... */
}
```

You'll notice that not only does this provide the user's current role, but it also provides a contingency for when none is set ("guest" is our lowest level of access).

I'll revisit this user class in later articles.

### Resources

*A model is a resource*. As such, it should implement the resource interface. Furthermore, it likely should know which roles are allowed which rights. Finally, it should be able to verify access before performing an action. So, we need a little code.

First, let's make our model a resource.

```php
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        return 'bug';
    }

    /* ... */
}
```

Now, let's allow injecting an ACL object, or lazyloading it if none is found. In each case, we should then setup the access list for our resource. We'll limit the ACL object to one of a known type — which ensures that particular roles will be present.

```php
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    protected $_acl;

    public function setAcl(Spindle_Model_Acl_Spindle $acl)
    {
        if (!$acl->has($this->getResourceId())) {
            $acl->add($this)
                ->allow('guest',     $this, array('list', 'view'))
                ->allow('user',      $this, array('save', 'comment', 'link', 'close'))
                ->allow('developer', $this, array('delete'));
        }
        $this->_acl = $acl;
        return $this;
    }

    public function getAcl()
    {
        if (null === $this->_acl) {
            $this->setAcl(new Spindle_Model_Acl_Spindle());
        }
        return $this->_acl;
    }

    /* ... */
}
```

You'll notice that we pass `$this` as an argument. We can do this because our model is a resource. Also notice that we lazyload the ACL object if none has been injected.

Next, we need a way to determine the current role. As noted earlier when discussing roles, you'll typically authenticate a user with `Zend_Auth`, which will persist the current identity. We'll allow injection of the current identity, as well as a way to lazyload it from `Zend_Auth`.

```php
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
        } elseif (is_scalar($identity) && !is_bool($identity)) {
            $identity = new Zend_Acl_Role($identity);
        } elseif (null === $identity) {
            $identity = new Zend_Acl_Role('guest');
        } elseif (!$identity implements Zend_Acl_Role_Interface) {
            throw new Spindle_Model_Exception('Invalid identity provided');
        }
        $this->_identity = $identity;
        return $this;
    }

    public function getIdentity()
    {
        if (null === $this->_identity) {
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity()) {
                return 'guest';
            }
            $this->setIdentity($auth->getIdentity());
        }

        return $this->_identity;
    }

    /* ... */
}
```

You'll note that `setIdentity()` has a fair bit of logic — since the identity can be arbitrary, we need to ensure it's usable for our purposes.

Now that we have our roles and our resources, we can address how to add checks in our methods to verify user rights prior to executing code.

An expedient way to do this is to use `__call()` to intercept public method calls and proxy them to protected members. However, this has the negative side effects of code obscurity and the inability of tools (IDEs, ctags, etc) to pick up on the method calls. So, instead, let's build a helper method we can use to check ACLs; each method will then be responsible for calling on it and acting on its advice.

```php
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    public function checkAcl($action)
    {
        return $this->getAcl()->isAllowed(
            $this->getIdentity(), 
            $this, 
            $action
        );
    }
}
```

Now, let's hook this into various methods. As an example, consider the `save()` example from my previous entry on using forms with models. We might name the requested action 'save', and then query it. We then need to make a decision: if the user does not have rights, how do we indicate this? Common solutions include:

- Throw an exception
- Unique return value
- Unique return value + marking error condition in the object

We'll consider insufficient privileges an exceptional condition for this example:

```php
class Spindle_Model_BugTracker implements Zend_Acl_Resource_Interface
{
    /* ... */

    public function save(array $data)
    {
        if (!$this->checkAcl('save')) {
            throw new Spindle_Model_Acl_Exception(\"Insufficient rights\");
        }

        /* ... */
    }

    /* ... */
}
```

When instantiating our model now, we need to either pass in the current identity, or set it after instantiation, but prior to calling an ACL-controlled action:

```php
// At instantiation:
$bugModel = new Spindle_Model_BugTracker(array('identity' => $user));

// Following instantiation:
$bugModel = new Spindle_Model_BugTracker();
$bugModel->setIdentity($user);

$bugModel->save($data);
```

(Of course, it will also pull it automatically from the authentication session, but it's good to know we can also inject it!)

### ACLs Revisited

Now that the resource and privilege definition has been moved to the model, we can simplify the actual ACL object a bit so that it only defines roles and initializes the whitelist:

```php
class Spindle_Model_Acl_Spindle extends Zend_Acl
{
    public function __construct()
    {
        // Define roles:
        $this->addRole(new Spindle_Model_Acl_Role_Guest)
             ->addRole(new Spindle_Model_Acl_Role_User,      'guest')
             ->addRole(new Spindle_Model_Acl_Role_Developer, 'user')
             ->addRole(new Spindle_Model_Acl_Role_Manager,   'developer');

        // Deny privileges by default; i.e., create a whitelist
        $this->deny();
    }
}
```

We still define the roles here, as our user object is only used for validating access; we still need to define roles, first.

Summary
-------

`Zend_Acl` is surprisingly simple and flexible. By using composition in your model, you can add ACLs trivially to your domain workflow, helping keep a separation of responsibilities while losing none of the power a good set of ACLs provides. The important takeaway is that ACLs should be part of your model logic, and that you can use object composition to achieve this end.

In the next installment, I'll look at how "Return Values are Part of Your Model, Too."
