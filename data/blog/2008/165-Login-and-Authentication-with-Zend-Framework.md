---
id: 165-Login-and-Authentication-with-Zend-Framework
author: matthew
title: 'Login and Authentication with Zend Framework'
draft: false
public: true
created: '2008-03-28T11:14:11-04:00'
updated: '2008-05-22T09:19:37-04:00'
tags:
    0: php
    2: 'zend framework'
---
**Update:** this article is now [available in French](http://blog.itanea.com/index.php/2008/05/04/login-et-authentification-avec-le-zend-framework/),
courtesy of Frédéric Blanc.

I've fielded a number of questions from people wanting to know how to handle
authentication and identity persistence in Zend Framework. The typical issue is
that they're unsure how to combine:

- An authentication adapter
- A login form
- A controller for login/logout actions
- Checking for an authenticated user in subsequent requests

It's not terribly difficult, but it does require knowing how the various pieces
of the MVC fit together, and how to use `Zend_Auth`. Let's take a look.

<!--- EXTENDED -->

Authentication Adapter
----------------------

For all this to work, you'll need an [authentication adapter](http://framework.zend.com/manual/en/zend.auth.html#zend.auth.introduction.adapters).
I'm not going to go into specifics on this, as the documentation covers them,
and your needs will vary based on your site. I *will* make the assumption,
however, that your authentication adapter requires a username and password for
authentication credentials.

Our login controller will make use of the adapter, but simply have a placeholder for retrieving it.

Login Form
----------

The login form itself is pretty simple. You can setup some basic validation
rules so that you can prevent a database or other service hit, but otherwise
keep things relatively simple. For purposes of this tutorial, we'll define the
following criteria:

- Username must be alphabetic characters only, and must contain between 3 and 20 characters
- Password must consist of alphanumeric characters only, and must be between 6 and 20 characters

The form would look like this:

```php
class LoginForm extends Zend_Form
{
    public function init()
    {
        $username = $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alpha',
                array('StringLength', false, array(3, 20)),
            ),
            'required'   => true,
            'label'      => 'Your username:',
        ));

        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));

        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}
```

Login Controller
----------------

Now, let's create a controller for handling login and logout actions. The typical flow would be:

- User hits login form
- User submits form
- Controller processes form
  - Validation errors redisplay the form with error messages
  - Successful validation redirects to home page
- Logged-in user gets redirected to home page
- Logout action logs out user and redirects to login form

The `LoginController` will make use of your chosen authentication adapter, as
well as the login form. We will pass to the login form constructor the form
action and method (since we now know what they will be for this usage of the
form). When we have valid values, we'll pass them to our authentication
adapter.

So, let's create the controller. First off, we'll create accessors for the form and authentication adapter.

```php
class LoginController extends Zend_Controller_Action
{
    public function getForm()
    {
        return new LoginForm(array(
            'action' => '/login/process',
            'method' => 'post',
        ));
    }

    public function getAuthAdapter(array $params)
    {
        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of 
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.
    }
}
```

Next, we need to do some checking before we dispatch any actions to ensure the following:

- If the user is already authenticated, but has not requested to logout, we should redirect to the home page
- If the user is not authenticated, but has requested to logout, we should redirect to the login page

The following `preDispatch()` routine will do this for us:

```php
class LoginController extends Zend_Controller_Action
{
    // ...

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }
}
```

Now, we need to do our login form. This is our simplest method — we simply
retrieve the form and assign it to the view:

```php
class LoginController extends Zend_Controller_Action
{
    // ...

    public function indexAction()
    {
        $this->view->form = $this->getForm();
    }
}
```

Processing the form involves slightly more logic. We need to verify that we
have a post request, then that the form is valid, and finally that the
credentials are valid.

```php
class LoginController extends Zend_Controller_Action
{
    // ...
    
    public function processAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
        $this->_helper->redirector('index', 'index');
    }
}
```

Finally, we can tackle the logout action. This is almost as simple as
displaying the login form; we simply clear the identity from the authentication
object, and redirect:

```php
class LoginController extends Zend_Controller_Action
{
    // ...

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }
}
```

Okay, that's it for our login/logout routines. Let's look at the one associated
view we have, the form:

```php
<? // login/index.phtml ?>
<h2>Please Login</h2>
<?= $this->form ?>
```

And that's it. Really. `Zend_Form` makes view scripts simple. :-)

Checking for Authenticated Users
--------------------------------

The last part of the question area is: how do I determine if a user is
authenticated, and restrict access if not?

If you look carefully at the `preDispatch()` method above, you can see already
how this can be done. `Zend_Auth` persists the identity in the session, allowing
you to query it directly using this construct:

```php
Zend_Auth::getInstance()->hasIdentity()
```

You can use this to determine if the user is logged in, and then use the
redirector to redirect to the login page if not. You can pull the identity from
the auth object as well:

```php
$identity = Zend_Auth::getInstance()->getIdentity();
```

This could be sprinkled into a helper to show login status in your layout, for instance:

```php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class My_View_Helper_ProfileLink
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function profileLink()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->username;
            return '<a href=\"/profile' . $username . '\">Welcome, ' . $username .  '</a>';
        } 

        return '<a href=\"/login\">Login</a>';
    }
}
```

Conclusion
----------

`Zend_Auth` does a lot of behind the scenes work to make persisting an identity
in the session trivial. Combine it with `Zend_Form`, and you have a very easy
to implement solution for retrieving and validating credentials; add standard
hooks in the `Zend_Controller` component for filtering actions prior to
dispatch, and you can restrict access to applications easily based on
authentication status.
