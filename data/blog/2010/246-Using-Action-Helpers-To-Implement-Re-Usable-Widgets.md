---
id: 246-Using-Action-Helpers-To-Implement-Re-Usable-Widgets
author: matthew
title: 'Using Action Helpers To Implement Re-Usable Widgets'
draft: false
public: true
created: '2010-10-04T09:40:00-04:00'
updated: '2010-10-11T06:13:47-04:00'
tags:
    0: php
    2: 'zend framework'
---
I had a twitter/IRC exchange yesterday with [Andries Seutens](http://twitter.com/andriesss)
and [Nick Belhomme](http://twitter.com/NickBelhomme) regarding applications that
include widgets within their layout. During the exchange, I told Andriess not to
use the `action()` view helper, and both Andriess and Nick then asked how to
implement widgets if they shouldn't use that helper. While I ended up having an
IRC exchange with Nick to give him a general idea on how to accomplish the task,
I decided a longer writeup was in order.

<!--- EXTENDED -->

Background
----------

The situation all started when Andries tweeted asking about what he considered
some mis-behavior on the part of the `action()` view helper — a situation that
turned out not to be an issue, *per se*, but more a case of bad architecture
within Zend Framework. His assumption was that calling `action()` would fire off
another circuit of the front controller's dispatch loop — which would mean he
could rely on plugins he'd established to fire. However, `action()` does nothing
of the sort. It instead pulls the dispatcher from the front controller, and
manually calls `dispatch()` on a new action. As such, action helpers will
trigger, but no front controller plugins will. Additionally, if a redirect or
"forward" condition is detected, it simply returns an empty string.

The helper was done this way because Zend Framework does not render views a
single time — it instead renders after each action, and accumulates views to
render in the layout. If we were accumulating view variables and rendering once,
and if we were using a finite state machine of some sort, we could probably
operate the way one would expect — within the dispatch loop. Since we don't, any
solution around looping over actions (such as the `ActionStack` action
helper/front controller plugin) or rendering the content of executing an action
will be a hack. *Note: ZF2's MVC layer may make this possible… though still not
necessarily recommended.*

There are other reasons to avoid the use of these solutions, though. If you are
invoking additional controller actions in order to help populate your view,
you're likely putting domain logic into your controllers. Think about it. The
controller should only be responsible for taking the input, funneling it to the
correct model or models, and then passing information on to the views

With that in mind, here's the approach I recommended to Nick and Andries.

The Secret Weapon: Action Helpers
---------------------------------

I've [blogged](http://devzone.zend.com/article/3350-Action-Helpers-in-Zend-Framework)
about [action](/blog/233-Responding-to-Different-Content-Types-in-RESTful-ZF-Apps.html)
[helpers](/blog/235-A-Simple-Resource-Injector-for-ZF-Action-Controllers.html)
before. They're a built-in mechanism in Zend Framework to allow you to extend
your action controllers in a way that uses composition instead of inheritance.

One approach to widgets for Zend Framework makes use of these. Consider the
following "user" module:

```
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
```

Now, notice a few things about it. First, it has views, helpers, and forms — but
no controllers. So, there are no controllers or actions that may be invoked; you
could definitely define some, but you don't *need* to; your widgets will work
with or without them. Second, notice that the `views/scripts/` subdirectory is
not further subdivided; its view scripts are not part of any actions, so they
can be at the top level within this module. Finally, notice that it has both a
bootstrap and configuration.

Let's look at the bootstrap file. Since this is in a module, it gets a prefix
named after the module, and is thus `User_Bootstrap`.

```php
class User_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function initResourceLoader()
    {
        $loader = $this->getResourceLoader();
        $loader->addResourceType('helper', 'helpers', 'Helper');
    }

    protected function _initConfig()
    {
        $env = $this->getEnvironment();
        $config = new Zend_Config_Ini(
            dirname(__FILE__) . '/configs/user.ini', 
            $this->getEnvironment()
        );
        return $config;
    }

    protected function _initHelpers()
    {
        $this->bootstrap('config');
        $config = $this->getResource('config');

        Zend_Controller_Action_HelperBroker::addHelper(
            new User_Helper_HandleLogin($config)
        );
    }
}
```

I've overridden the `initResourceLoader()` method so that I can add a "helper"
resource corresponding to my `helpers/` subdirectory; this will be used to allow
autoloading action helpers.

The `_initConfig()` method initializes bootstrap configuration. I pull in the
configuration file relative to the Bootstrap class file, and use the registered
environment to determine what section to use from that configuration.

Finally, I have an initializer method for loading my action helpers. This method
is dependent on the `_initConfig()` method, as I want to pass my configuration
to the helpers. In here, I instantiate a single action helper,
`User_Helper_HandleLogin`.

Next, lets look at the configuration:

```ini
[production]
salt = "1471998176"
adapter.table = "users"
adapter.identity_column = "username"
adapter.password_column = "password"

[testing : production]

[development : production]
```

These are values I'm going to use in my action helper(s). We'll revisit them
later; the general gist I'm getting at here is this is just a normal
configuration file.

Now, let's look at the action helper itself. As a reminder, action helpers can
define hooks for `init()` (invoked by the helper broker each time it is passed
to a new controller), `preDispatch()` (invoked prior to executing the
controller's `preDispatch()` hook and executing the action itself), and
`postDispatch()` (executed after the action and the controller's
`postDispatch()` routine). In this particular helper, I'll define a
`preDispatch()` hook that does the following:

- If we do not have an established authentication identity, render a login
  widget.
- If we do have an established authentication identity, render a user profile
  widget.
- If we to not have an established authentication identity, but a `POST` has
  occurred, attempt to login the user; if successful, display the user profile
  widget, but if not, re-display the login widget with an error message.

The initial definition looks like this:

```php
class User_Helper_HandleLogin extends Zend_Controller_Action_Helper_Abstract
{
    protected $config;

    public function __construct(Zend_Config $config)
    {
        $this->config = $config;
    }

    public function preDispatch()
    {
        if (null === ($controller = $this->getActionController())) {
            return;
        }

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->handleLogin();
            return;
        }

        $this->createProfileWidget();
    }

    /* ... */
}
```

As noted earlier, we expect a configuration object to the constructor. We'll use
this later to get some values for authentication. When we start our
`preDispatch()` routine, we check first to see if we have an action controller
available; if not, we'll move on, as we won't have a view to act on.

Next, we check for an identity. If we don't have one, we delegate to a
`handleLogin()` method; otherwise, a `createWidgetProfile()` method. We'll look
at the latter first, as it's simpler — but first, we'll take a small digression
and look at how we get the view object.

```php
class User_Helper_HandleLogin extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    /* ... */

    public function getView()
    {
        if (null !== $this->view) {
            return $this->view
        }

        $controller = $this->getActionController();
        $view = $controller->view;
        if (!$view instanceof Zend_View_Abstract) {
            return;
        }
        $view->addScriptPath(dirname(__FILE__) . '/../views/scripts');
        $this->view = $view;
        return $view;
    }
}
```

In here, we grab the view from the controller. If we don't have one we can work
with, we simply return a null value. If we do, however, we add a script path
pointing to the module's script path, and return the view.

Now, the `createWidgetProfile()` method:

```php
class User_Helper_HandleLogin extends Zend_Controller_Action_Helper_Abstract
{
    /* ... */

    public function createProfileWidget()
    {
        if (!$view = $this->getView()) {
            return;
        }

        $view->user = $view->partial('profile.phtml', array(
            'identity' => Zend_Auth::getInstance()->getIdentity(),
        ));
    }

    /* ... */
}
```

Again, a simple bit of logic. We attempt to retrieve a view, and exit early if
we don't get one. Next, we render a partial view, using the identity from the
authentication storage, and assign it to a view member, "user". The view script
looks like this:

```php
<?php $identity = (array) $this->identity; ?>
<div id="user-profile">
    <h4><?php echo $this->escape($identity['username']) ?></h4>
    <dl>
    <?php foreach ($identity as $field => $value): ?>
        <?php if ($field == 'username'):
            continue;
        endif ?>
        <dt><?php echo ucfirst($field) ?>:</dt>
        <dd><?php echo $this->escape($value) ?>:</dd>
    <?php endforeach ?>
    </dl>
</div>
```

Nothing fancy — just a `div` with a heading and a definition list.

Next, the `handleLogin()` method. In this method we need to do several things:

- Check to see if we have a `POST` request; if not, simply render the form.
- Validate the form; if we have errors, re-render it.
- Attempt to authenticate against the form values; if we fail, re-render the form, with an error condition.
- Finally, on successful authentication, store the identity, and then render the profile.

The logic looks like this:

```php
class User_Helper_HandleLogin extends Zend_Controller_Action_Helper_Abstract
{
    /* ... */

    public function renderLoginForm(Zend_Form $form, $error = null)
    {
        if (!$view = $this->getView()) {
            return;
        }

        $view->user = $view->partial('login.phtml', array(
            'form'  => $form,
            'error' => $error,
        ));
    }

    public function handleLogin()
    {
        $request = $this->getRequest();
        $form    = new User_Form_Login();

        if (!$request->isPost()) {
            $this->renderLoginForm($form);
        }

        if (!$form->isValid($request->getPost())) {
            $this->renderLoginForm($form);
            return;
        }

        // Does the POST contain the form namespace? If not, just render the form
        $namespace = $form->getElementsBelongTo();
        if (!empty($namespace) && !is_array($request->getPost($namespace))) {
            $this->renderLoginForm($form);
            return; 
        }

        $username = $form->username->getValue();
        $password = $form->password->getValue();
        $password = substr($username, 0, 3) . $password . $this->config->salt;
        $password = hash('sha256', $password);

        $adapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table_Abstract::getDefaultAdapter(),
            $this->config->adapter->table,
            $this->config->adapter->identity_column,
            $this->config->adapter->password_column
        );
        $adapter->setIdentity($username)
                ->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            $this->renderLoginForm($form, 'Invalid Credentials');
            return;
        }

        $auth->getStorage()->write(
            $adapter->getResultRowObject(null, 'password')
        );

        $this->createProfileWidget();
    }

    /* ... */
}
```

If you look carefully, you'll see that the passed in configuration is utilized
in order to configure the authentication adapter, as well as salt the password
prior to hashing. We re-use the `createProfileWidget()` method in order to
render the profile when done, an dthe new `renderLoginForm()` method will render
our login form for us.

The form partial looks like this:

```php
<div id="login-widget">
<?php if ($this->error): ?>
    <p class="error"><?php echo $this->escape($this->error) ?></p>
<?php endif ?>
    <?php 
    $this->form->setAction('#')
               ->setMethod('post');
    echo $this->form;
    ?>
</div>
```

We could get more fancy, and set decorators and what not, but there's no need
within the scope of the example. I'm not showing the form definition here, as
it's somewhat out of scope for this post; any old form should do, however.

If you've been paying attention, you'll note that in both cases — displaying the
login form or displaying the user profile — I captured the rendered views to the
same view variable, "user". At this point, you can then do the following in your
action's view scripts in order to display the widget within the page you
generate:

```php
<?php echo $this->user ?>
```

Summary
-------

This example is fairly basic in terms of the functionality and structure
offered. You could expand this in a number of ways:

Instead of using `preDispatch()`, have your controllers explicitly invoke the
widget action helpers they need to consume.

- Potentially, your controllers could define a list of "widgets" they need, and
  each widget could inspect this on `preDispatch()` to determine if they need to
  do any work.
- Alternately, the widgets could maintain a list of actions, controllers, or
  modules in which they should render (or not).

Have your action helpers use models and service classes from their own module in
order to perform their work. For instance, I could have written an
authentication model and simply consumed that from the action helper, in order
to provide better separation of concerns.

You could also write view helpers that are specific to certain models you write
within your module. You would then need to add not only a script path, but a
helper path to your view.

You should setup some clear, clean rules for consistency with regards to how
widgets are named in your views, as well as how the helpers are named.

This technique is rather flexible, and keeps your code cleanly separated, as
well as protects you from the inconsistencies and issues inherent in the
`ActionStack` and `action()` view helper. With some discipline and creative
thinking, you should be able to accomplish a variety of effects, as well as make
re-usable widgets.

### Note

I've put a full sample of the code from this post up [my zf-examples repo on GitHub](http://github.com/weierophinney/zf-examples/tree/master/widgets-as-helpers).

Clarifications
--------------

A number of folks have indicated in comments that they have been using view
helpers in order to effect widget content on their site, and have asked if that
is an appropriate approach (or argued that it is).

Using view helpers makes a lot of sense, but if, *and only if*, the following
conditions are met:

- The helper *does not require Request data* in order to do its work, or can
  depend only on data injected into the view (don't cheat and inject the
  Request object!).
- The helper *will not be updating the model*.

If you are doing either of the above two items, you should consider using an
action helper. The view should only be responsible for display logic, which
*may* include *retrieving* data from a model. The controller is responsible for
inspecting the request and determining what models and views to marshall — and
for *updating* models.

If your widget is simply pulling data from a model, or displaying some markup,
by all means, use a view helper. If it's doing more than that, don't.

This also touches on a related topic brought up in the comments: what if you're
serving an alternate content type — e.g., `application/json`? Again, this is where
I feel using action helpers is to your advantage. It would be very easy to
define an interface for your action helpers that allows them to indicate what
content types they're allowed to operate on. Then, within the action helper
logic or within a plugin that marshalls the action helpers, you can easily
disable them from executing if they cannot serve that content type. Within the
view, you simply won't reference their captured output — and even if you do, the
value will be returned as simply `null` if they are disabled.
