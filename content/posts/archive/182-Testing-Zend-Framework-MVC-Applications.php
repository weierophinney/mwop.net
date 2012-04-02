<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('182-Testing-Zend-Framework-MVC-Applications');
$entry->setTitle('Testing Zend Framework MVC Applications');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1214841600);
$entry->setUpdated(1215074837);
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
    Since I originally started hacking on the <a
    href="http://framework.zend.com/">Zend Framework</a> MVC in the fall of
    2006, I've been touting the fact that you can test ZF MVC projects by
    utilizing the Request and Response objects; indeed, this is what I actually
    did to test the Front Controller and Dispatcher. However, until recently,
    there was never an easy way to do so in your userland projects; the default
    request and response objects make it difficult to easily and quickly setup
    tests, and the methods introduced into the front controller to make it
    testable are largely undocumented. 
</p>

<p>
    So, one of my ongoing projects the past few months has been to create an
    infrastructure for functional testing of ZF projects using <a
        href="http://phpunit.de/">PHPUnit</a>. This past weekend, I made the
    final commits that make this functionality feature complete.
</p>

<p>
    The new functionality provides several facets:
</p>

<ul>
    <li>Stub test case classes for the HTTP versions of our Request and Response
        objects, containing methods for setting up the request environment
        (including setting GET, POST, and COOKIE parameters, HTTP request
        headers, etc).</li>
    <li><code>Zend_Dom_Query</code>, a class for using CSS selectors (and XPath)
        to query (X)HTML and XML documents.</li>
    <li>PHPUnit constraints that consume <code>Zend_Dom_Query</code> and the
        Response object to make their comparisons.</li>
    <li>A specialized PHPUnit test case that contains functionality for
        bootstrapping an MVC application, dispatching requests, and a variety of
        assertions that utilize the above constraints and objects.</li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    What might you want to test?
</p>

<ul>
    <li>HTTP response codes</li>
    <li>Whether or not the action resulted in a redirect, and where it
        redirected to</li>
    <li>Whether or not certain DOM artifacts are present (particularly helpful
        for ensuring that the DOM structure is correct for JS actions)</li>
    <li>Presence of specific HTTP response headers and/or their content</li>
    <li>What module, controller, and/or action was used in the last iteration of
        the dispatch loop</li>
    <li>What route was selected</li>
</ul>

<p>
    The aim is to make testing your controllers trivial and fun. Let's look at
    an example:
</p>

<div class="example"><pre><code lang="php">
class UserControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this-&gt;bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this-&gt;frontController-&gt;registerPlugin(
            new Bugapp_Plugin_Initialize('test')
        );
    }

    public function testCallingControllerWithoutActionShouldPullFromIndexAction()
    {
        $this-&gt;dispatch('/user');
        $this-&gt;assertResponseCode(200);
        $this-&gt;assertController('user');
        $this-&gt;assertAction('index');
    }

    public function testIndexActionShouldContainLoginForm()
    {
        $this-&gt;dispatch('/user');
        $this-&gt;assertResponseCode(200);
        $this-&gt;assertSelect('form#login');
    }

    public function testValidLoginShouldInitializeAuthSessionAndRedirectToProfilePage()
    {
        $this-&gt;request
             -&gt;setMethod('POST')
             -&gt;setPost(array(
                 'username' =&gt; 'foobar',
                 'password' =&gt; 'foobar'
             ));
        $this-&gt;dispatch('/user/login');
        $this-&gt;assertTrue(Zend_Auth::getInstance()-&gt;hasIdentity());
        $this-&gt;assertRedirectTo('/user/view');
    }
}
</code></pre></div>

<p>
    You'll note that the <code>setUp()</code> method assigns a callback to the
    <code>$bootstrap</code> property. This allows the test case to call that
    callback to bootstrap the application; alternately, you can specify the path
    to a file to include that would do your bootstrapping. In the example above,
    I actually simply add a single "initialization" plugin to the front
    controller that takes care of bootstrapping my application (via the
    <code>routeStartup()</code> hook).
</p>

<p>
    I then have a few test cases. The first checks to ensure that the default
    action is called when no action is provided. The second checks to ensure
    that the login form is present on that page (by using a CSS selector to find
    a form with the id of 'login'). The third checks to see if I get a valid
    authentication session when logging in with good credentials, and that I get
    redirected to the appropriate location.
</p>

<p>
    This is, of course, just the tip of the iceberg; I've created a couple dozen
    other assertions as well.
</p>

<p>
    You can preview the functionality in the 
    <a href="http://framework.zend.com/svn/framework/standard/incubator/">Zend 
        Framework standard incubator</a>; look for
    Zend_Test_PHPUnit_ControllerTestCase in there, as well as the Zend_Test
    documentation in the documentation tree (in human-readable DocBook XML).
</p>

<p>
    For those of you who decide to start playing with this, I'd love any
    feedback I can get. The best place to do so, however, is on the fw-mvc
    mailing list; <a href="http://framework.zend.com/wiki/display/ZFDEV/Contributing+to+Zend+Framework#ContributingtoZendFramework-Subscribetotheappropriatemailinglists">instructions are on the ZF wiki</a>.
</p>
EOT;
$entry->setExtended($extended);

return $entry;