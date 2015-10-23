---
id: 182-Testing-Zend-Framework-MVC-Applications
author: matthew
title: 'Testing Zend Framework MVC Applications'
draft: false
public: true
created: '2008-06-30T12:00:00-04:00'
updated: '2008-07-03T04:47:17-04:00'
tags:
    0: php
    1: mvc
    3: 'zend framework'
---
Since I originally started hacking on the [Zend Framework](http://framework.zend.com/)
MVC in the fall of 2006, I've been touting the fact that you can test ZF MVC
projects by utilizing the Request and Response objects; indeed, this is what I
actually did to test the Front Controller and Dispatcher. However, until
recently, there was never an easy way to do so in your userland projects; the
default request and response objects make it difficult to easily and quickly
setup tests, and the methods introduced into the front controller to make it
testable are largely undocumented.

So, one of my ongoing projects the past few months has been to create an
infrastructure for functional testing of ZF projects using
[PHPUnit](http://phpunit.de/). This past weekend, I made the final commits that
make this functionality feature complete.

The new functionality provides several facets:

- Stub test case classes for the HTTP versions of our Request and Response objects, containing methods for setting up the request environment (including setting GET, POST, and COOKIE parameters, HTTP request headers, etc).
- `Zend_Dom_Query`, a class for using CSS selectors (and XPath) to query (X)HTML and XML documents.
- PHPUnit constraints that consume `Zend_Dom_Query` and the Response object to make their comparisons.
- A specialized PHPUnit test case that contains functionality for bootstrapping an MVC application, dispatching requests, and a variety of assertions that utilize the above constraints and objects.

<!--- EXTENDED -->

What might you want to test?

- HTTP response codes
- Whether or not the action resulted in a redirect, and where it redirected to
- Whether or not certain DOM artifacts are present (particularly helpful for ensuring that the DOM structure is correct for JS actions)
- Presence of specific HTTP response headers and/or their content
- What module, controller, and/or action was used in the last iteration of the dispatch loop
- What route was selected

The aim is to make testing your controllers trivial and fun. Let's look at an example:

```php
class UserControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->frontController->registerPlugin(
            new Bugapp_Plugin_Initialize('test')
        );
    }

    public function testCallingControllerWithoutActionShouldPullFromIndexAction()
    {
        $this->dispatch('/user');
        $this->assertResponseCode(200);
        $this->assertController('user');
        $this->assertAction('index');
    }

    public function testIndexActionShouldContainLoginForm()
    {
        $this->dispatch('/user');
        $this->assertResponseCode(200);
        $this->assertSelect('form#login');
    }

    public function testValidLoginShouldInitializeAuthSessionAndRedirectToProfilePage()
    {
        $this->request
             ->setMethod('POST')
             ->setPost(array(
                 'username' => 'foobar',
                 'password' => 'foobar'
             ));
        $this->dispatch('/user/login');
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
        $this->assertRedirectTo('/user/view');
    }
}
```

You'll note that the `setUp()` method assigns a callback to the `$bootstrap`
property. This allows the test case to call that callback to bootstrap the
application; alternately, you can specify the path to a file to include that
would do your bootstrapping. In the example above, I actually simply add a
single "initialization" plugin to the front controller that takes care of
bootstrapping my application (via the `routeStartup()` hook).

I then have a few test cases. The first checks to ensure that the default
action is called when no action is provided. The second checks to ensure that
the login form is present on that page (by using a CSS selector to find a form
with the id of 'login'). The third checks to see if I get a valid
authentication session when logging in with good credentials, and that I get
redirected to the appropriate location.

This is, of course, just the tip of the iceberg; I've created a couple dozen
other assertions as well.

You can preview the functionality in the [Zend Framework standard incubator](http://framework.zend.com/svn/framework/standard/incubator/);
look for `Zend_Test_PHPUnit_ControllerTestCase` in there, as well as the `Zend_Test`
documentation in the documentation tree (in human-readable DocBook XML).

For those of you who decide to start playing with this, I'd love any feedback I
can get. The best place to do so, however, is on the fw-mvc mailing list;
[instructions are on the ZF wiki](http://framework.zend.com/wiki/display/ZFDEV/Contributing+to+Zend+Framework#ContributingtoZendFramework-Subscribetotheappropriatemailinglists).
