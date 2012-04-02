<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('165-Login-and-Authentication-with-Zend-Framework');
$entry->setTitle('Login and Authentication with Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1206717251);
$entry->setUpdated(1211462377);
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
    <b>Update:</b> this article is now <a href="http://blog.itanea.com/index.php/2008/05/04/login-et-authentification-avec-le-zend-framework/">available in French</a>, courtesy of Frédéric Blanc.
</p>

<p>
    I've fielded a number of questions from people wanting to know how to handle
    authentication and identity persistence in Zend Framework. The typical issue
    is that they're unsure how to combine:
</p>

<ul>
     <li>An authentication adapter</li>
     <li>A login form</li>
     <li>A controller for login/logout actions</li>
     <li>Checking for an authenticated user in subsequent requests</li>
</ul>

<p>
    It's not terribly difficult, but it does require knowing how the various
    pieces of the MVC fit together, and how to use Zend_Auth. Let's take a look.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Authentication Adapter</h2>

<p>
    For all this to work, you'll need an 
    <a href="http://framework.zend.com/manual/en/zend.auth.html#zend.auth.introduction.adapters">authentication
    adapter</a>. I'm not going to go into specifics on this, as the
    documentation covers them, and your needs will vary based on your site. I
    <em>will</em> make the assumption, however, that your authentication adapter
    requires a username and password for authentication credentials.
</p>

<p>
    Our login controller will make use of the adapter, but simply have a
    placeholder for retrieving it.
</p>

<h2>Login Form</h2>

<p>
    The login form itself is pretty simple. You can setup some basic validation
    rules so that you can prevent a database or other service hit, but otherwise
    keep things relatively simple. For purposes of this tutorial, we'll define
    the following criteria:
</p>

<ul>
     <li>Username must be alphabetic characters only, and must contain between 3
     and 20 characters</li>
     <li>Password must consist of alphanumeric characters only, and must be
     between 6 and 20 characters</li>
</ul>

<p>
    The form would look like this:
</p>

<div class="example"><pre><code lang="php">
class LoginForm extends Zend_Form
{
    public function init()
    {
        $username = $this-&gt;addElement('text', 'username', array(
            'filters'    =&gt; array('StringTrim', 'StringToLower'),
            'validators' =&gt; array(
                'Alpha',
                array('StringLength', false, array(3, 20)),
            ),
            'required'   =&gt; true,
            'label'      =&gt; 'Your username:',
        ));

        $password = $this-&gt;addElement('password', 'password', array(
            'filters'    =&gt; array('StringTrim'),
            'validators' =&gt; array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   =&gt; true,
            'label'      =&gt; 'Password:',
        ));

        $login = $this-&gt;addElement('submit', 'login', array(
            'required' =&gt; false,
            'ignore'   =&gt; true,
            'label'    =&gt; 'Login',
        ));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this-&gt;setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' =&gt; 'dl', 'class' =&gt; 'zend_form')),
            array('Description', array('placement' =&gt; 'prepend')),
            'Form'
        ));
    }
}
</code></pre></div>

<h2>Login Controller</h2>

<p>
    Now, let's create a controller for handling login and logout actions. The
    typical flow would be:
</p>

<ul>
     <li>User hits login form</li>
     <li>User submits form</li>
     <li>Controller processes form
        <ul>
             <li>Validation errors redisplay the form with error messages</li>
             <li>Successful validation redirects to home page</li>
        </ul>
     </li>
     <li>Logged-in user gets redirected to home page</li>
     <li>Logout action logs out user and redirects to login form</li>
</ul>

<p>
    The LoginController will make use of your chosen authentication adapter, as
    well as the login form. We will pass to the login form constructor the form
    action and method (since we now know what they will be for this usage of the
    form). When we have valid values, we'll pass them to our authentication
    adapter.
</p>

<p>
    So, let's create the controller. First off, we'll create accessors for the
    form and authentication adapter.
</p>

<div class="example"><pre><code lang="php">
class LoginController extends Zend_Controller_Action
{
    public function getForm()
    {
        return new LoginForm(array(
            'action' =&gt; '/login/process',
            'method' =&gt; 'post',
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
</code></pre></div>

<p>
    Next, we need to do some checking before we dispatch any actions to ensure
    the following:
</p>

<ul>
     <li>If the user is already authenticated, but has not requested to logout,
     we should redirect to the home page</li>
     <li>If the user is not authenticated, but has requested to logout, we
     should redirect to the login page</li>
</ul>

<p>
    The following <code>preDispatch()</code> routine will do this for us:
</p>

<div class="example"><pre><code lang="php">
class LoginController extends Zend_Controller_Action
{
    // ...

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()-&gt;hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this-&gt;getRequest()-&gt;getActionName()) {
                $this-&gt;_helper-&gt;redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this-&gt;getRequest()-&gt;getActionName()) {
                $this-&gt;_helper-&gt;redirector('index');
            }
        }
    }
}
</code></pre></div>

<p>
    Now, we need to do our login form. This is our simplest method -- we simply
    retrieve the form and assign it to the view:
</p>

<div class="example"><pre><code lang="php">
class LoginController extends Zend_Controller_Action
{
    // ...

    public function indexAction()
    {
        $this-&gt;view-&gt;form = $this-&gt;getForm();
    }
}
</code></pre></div>

<p>
    Processing the form involves slightly more logic. We need to verify that we
    have a post request, then that the form is valid, and finally that the
    credentials are valid.
</p>

<div class="example"><pre><code lang="php">
class LoginController extends Zend_Controller_Action
{
    // ...
    
    public function processAction()
    {
        $request = $this-&gt;getRequest();

        // Check if we have a POST request
        if (!$request-&gt;isPost()) {
            return $this-&gt;_helper-&gt;redirector('index');
        }

        // Get our form and validate it
        $form = $this-&gt;getForm();
        if (!$form-&gt;isValid($request-&gt;getPost())) {
            // Invalid entries
            $this-&gt;view-&gt;form = $form;
            return $this-&gt;render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this-&gt;getAuthAdapter($form-&gt;getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth-&gt;authenticate($adapter);
        if (!$result-&gt;isValid()) {
            // Invalid credentials
            $form-&gt;setDescription('Invalid credentials provided');
            $this-&gt;view-&gt;form = $form;
            return $this-&gt;render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
        $this-&gt;_helper-&gt;redirector('index', 'index');
    }
}
</code></pre></div>

<p>
    Finally, we can tackle the logout action. This is almost as simple as
    displaying the login form; we simply clear the identity from the
    authentication object, and redirect:
</p>

<div class="example"><pre><code lang="php">
class LoginController extends Zend_Controller_Action
{
    // ...

    public function logoutAction()
    {
        Zend_Auth::getInstance()-&gt;clearIdentity();
        $this-&gt;_helper-&gt;redirector('index'); // back to login page
    }
}
</code></pre></div>

<p>
    Okay, that's it for our login/logout routines. Let's look at the one
    associated view we have, the form:
</p>

<div class="example"><pre><code lang="php">
&lt;? // login/index.phtml ?&gt;
&lt;h2&gt;Please Login&lt;/h2&gt;
&lt;?= $this-&gt;form ?&gt;
</code></pre></div>

<p>
    And that's it. Really. Zend_Form makes view scripts simple. :-)
</p>

<h2>Checking for Authenticated Users</h2>

<p>
    The last part of the question area is: how do I determine if a user is
    authenticated, and restrict access if not?
</p>

<p>
    If you look carefully at the <code>preDispatch()</code> method above, you
    can see already how this can be done. Zend_Auth persists the identity in the
    session, allowing you to query it directly using this construct:
</p>

<div class="example"><pre><code lang="php">
Zend_Auth::getInstance()-&gt;hasIdentity()
</code></pre></div>

<p>
    You can use this to determine if the user is logged in, and then use the
    redirector to redirect to the login page if not. You can pull the identity
    from the auth object as well:
</p>

<div class="example"><pre><code lang="php">
$identity = Zend_Auth::getInstance()-&gt;getIdentity();
</code></pre></div>

<p>
    This could be sprinkled into a helper to show login status in your layout,
    for instance:
</p>

<div class="example"><pre><code lang="php">
/**
 * ProfileLink helper
 *
 * Call as $this-&gt;profileLink() in your layout script
 */
class My_View_Helper_ProfileLink
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this-&gt;view = $view;
    }

    public function profileLink()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth-&gt;hasIdentity()) {
            $username = $auth-&gt;getIdentity()-&gt;username;
            return '&lt;a href=\&quot;/profile' . $username . '\&quot;&gt;Welcome, ' . $username .  '&lt;/a&gt;';
        } 

        return '&lt;a href=\&quot;/login\&quot;&gt;Login&lt;/a&gt;';
    }
}
</code></pre></div>

<h2>Conclusion</h2>

<p>
    <code>Zend_Auth</code> does a lot of behind the scenes work to make
    persisting an identity in the session trivial. Combine it with
    <code>Zend_Form</code>, and you have a very easy to implement solution
    for retrieving and validating credentials; add standard hooks in the
    <code>Zend_Controller</code> component for filtering actions prior to
    dispatch, and you can restrict access to applications easily based on
    authentication status.
</p>
EOT;
$entry->setExtended($extended);

return $entry;