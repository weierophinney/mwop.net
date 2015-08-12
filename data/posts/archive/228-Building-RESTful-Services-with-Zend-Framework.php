<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('228-Building-RESTful-Services-with-Zend-Framework');
$entry->setTitle('Building RESTful Services with Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1257775200);
$entry->setUpdated(1257953921);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'rest',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    As a followup to my <a href="/matthew/archives/227-Exposing-Service-APIs-via-Zend-Framework.html">previous post</a>, I now turn to RESTful web
    services. I originally encountered the term when attending 
    php|tropics in 2005, where <a href="http://twitter.com/g_schlossnagle">George
        Schlossnaggle</a> likened it to simple GET and POST requests. Since
    then, the architectural style -- and developer understanding of the architectural style
    -- has improved a bit, and a more solid definition can be made.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    At its heart, <a
        href="http://en.wikipedia.org/wiki/Representational_State_Transfer"></a>REST
    simply dictates that a given resource have a unique address, and that you
    interact with that resource using HTTP verbs. The standard verbs utilized
    are:
</p>

<ul>
    <li><em>GET</em>: retrieve a list of resources, or, if an identifier is
    present, view a single resource</li>

    <li><em>POST</em>: create a new resource with the data provided in the POST</li>

    <li><em>PUT</em>: update an existing resource as specified by an identifier,
    using the PUT data</li>

    <li><em>DELETE</em>: delete an existing resource as specified by an
    identifier</li>
</ul>

<p>
    The standard URL structure used is as follows:
</p>

<ul>
    <li>"/resource" - GET (list) and POST operations</li>
    <li>"/resource/{identifier}" - GET (view), PUT, and DELETE operations</li>
</ul>

<p>
    What the REST paradigm provides you is a simple, standard way to structure
    your CRUD (Create-Read-Update-Delete) applications. Due to the large number
    of REST clients available, it also means that if you follow the rules, you
    get a ton of interoperability with those clients.
</p>

<p>
    As of <a href="http://framework.zend.com/">Zend Framework</a> 1.9.0, it's
    trivially easy to create RESTful routes for your MVC application, as well as
    to handle the various REST actions via action controllers.
</p>

<p>
    <a
        href="http://framework.zend.com/manual/en/zend.controller.router.html#zend.controller.router.routes.rest">Zend_Rest_Route</a>
    allows you to define RESTful controllers at several levels:
</p>

<ul>
    <li>You can make it the default route, meaning that unless you have
    additional routes, all controllers will be considered REST controllers.</li>

    <li>You can specify modules that contain RESTful controllers.</li>

    <li>You can specify specific controllers per module that are RESTful</li>
</ul>

<p>
    As examples:
</p>

<div class="example"><pre><code class="language-php">
$front = Zend_Controller_Front::getInstance();
$router = $front-&gt;getRouter();

// Specifying all controllers as RESTful:
$restRoute = new Zend_Rest_Route($front);
$router-&gt;addRoute('default', $restRoute);

// Specifying the \&quot;api\&quot; module only as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
));
$router-&gt;addRoute('rest', $restRoute);

// Specifying the \&quot;api\&quot; module as RESTful, and the \&quot;task\&quot; controller of the
// \&quot;backlog\&quot; module as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
    'backlog' =&gt; array('task'),
));
$router-&gt;addRoute('rest', $restRoute);
</code></pre></div>

<p>
    To define a RESTful action controller, you can either extend
    Zend_Rest_Controller, or simply define the following methods in a standard
    controller extending Zend_Controller_Action (you'll need to define them
    regardless):
</p>

<div class="example"><pre><code class="language-php">
// Or extend Zend_Rest_Controller
class RestController extends Zend_Controller_Action
{
    // Handle GET and return a list of resources
    public function indexAction() {}

    // Handle GET and return a specific resource item
    public function getAction() {}

    // Handle POST requests to create a new resource item
    public function postAction() {}

    // Handle PUT requests to update a specific resource item
    public function putAction() {}

    // Handle DELETE requests to delete a specific item
    public function deleteAction() {}
}
</code></pre></div>

<p>
    For those methods that operate on individual resources (getAction(),
    putAction(), and deleteAction()), you can test for the identifier using the
    following:
</p>

<div class="example"><pre><code class="language-php">
if (!$id = $this-&gt;_getParam('id', false)) {
    // report error, redirect, etc.
}
</code></pre></div>

<h2>Responding is an art</h2>

<p>
    Many developers are either unaware of or ignore the part of the
    specification that dictates what the <em>response</em> should look like.
</p>

<p>
    For instance, in classic REST, after performing a POST to create a new item,
    you should do the following:
</p>

<ul>
    <li>Set the HTTP response code to 201, indicating "Created"</li>

    <li>Set the Location header to point to the canonical URI for the newly
    created item: "/team/31"</li>

    <li>Provide a representation of the newly created item</li>
</ul>

<p>
    Note that there's no redirect, which flies in the face of standard web
    development (where GET-POST-Redirect is the typical format). This is a
    common "gotcha" moment.
</p>

<p>
    Similarly, with PUT requests, you simply indicate an HTTP 200 status when
    successful, and show a representation of the updated item. DELETE requests
    should return an HTTP 204 status (indicating success - no content), with no
    body content.
</p>

<p><em>
    Note: when building RESTful HTML applications, you may want to still do
    GET-POST-Redirect to prevent caching issues. The above applies to RESTful
    web services, which typically use XML or JSON for transactions, and have
    smart clients for interacting with the service.
</em></p>

<p>
    I'll be writing another article soon showing some tips and tricks for
    interacting with HTTP headers, both from the request and for the response,
    as it's a subject lengthy enough for a post of its own. In the meantime,
    start playing with Zend_Rest_Route and standardizing on it for your CRUD
    operations!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
