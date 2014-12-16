<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('233-Responding-to-Different-Content-Types-in-RESTful-ZF-Apps');
$entry->setTitle('Responding to Different Content Types in RESTful ZF Apps');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1267734487);
$entry->setUpdated(1268231302);
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
    In <a
        href="/matthew/archives/227-Exposing-Service-APIs-via-Zend-Framework.html">previous</a>
    <a
        href="/matthew/archives/228-Building-RESTful-Services-with-Zend-Framework.html">articles</a>,
    I've explored building service endpoints and RESTful services with Zend
    Framework. With RPC-style services, you get to cheat: the protocol dictates
    the content type (XML-RPC uses XML, JSON-RPC uses JSON, SOAP uses XML,
    etc.). With REST, however, you have to make choices: what serialization
    format will you support? 
</p>

<p> 
    Why not support multiple formats?
</p>

<p>
    There's no reason you can't re-use your RESTful web service to support
    multiple formats. Zend Framework and PHP have plenty of tools to assist you
    in responding to different format requests, so don't limit yourself. With a
    small amount of work, you can make your controllers format agnostic, and
    ensure that you respond appropriately to different requests.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Content-Type Detection</h2>

<p>
    The first problem to solve is going to be how to retrieve passed parameters.
    When using XML or JSON as your serialization format, you aren't getting your
    standard POST variables -- you're getting a raw post instead, and you'll
    need to deserialize the payload. In fact, if you're getting a PUT request,
    you also have some work to do, as PHP doesn't do anything with PUT requests.
</p>

<p>
    I do this via an action helper. The basic algorithm is:
</p>

<ul>
    <li>Do we have a raw body in the request? If not, nothing more need be done.</li>
    <li>Determine the Content-Type passed in the request headers, and decode
    appropriately:
    <ul>
        <li>If it was JSON, pass the raw request body to
        <code>json_decode</code> or <code>Zend_Json::decode</code>.</li>
        <li>If it was XML, I pass the raw request body to the
        <code>Zend_Config_XML</code> constructor, and then serialize to an arrya
        using the <code>toArray()</code> method. Yes, it's a hack, but it's
        effective.</li>
        <li>Otherwise, I assume I've got a regular PUT-style request, and I pass
        the data to <code>parse_str()</code>.</li>
    </ul>
    </li>
</ul>

<p>
    I keep the values within the action helper, and then retrieve them on demand
    within my action controller. The helper looks like the following:
</p>

<div class="example"><pre><code class="language-php">
class Scrummer_Controller_Helper_Params 
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var array Parameters detected in raw content body
     */
    protected $_bodyParams = array();

    /**
     * Do detection of content type, and retrieve parameters from raw body if 
     * present
     * 
     * @return void
     */
    public function init()
    {
        $request     = $this-&gt;getRequest();
        $contentType = $request-&gt;getHeader('Content-Type');
        $rawBody     = $request-&gt;getRawBody();
        if (!$rawBody) {
            return;
        }
        switch (true) {
            case (strstr($contentType, 'application/json')):
                $this-&gt;setBodyParams(Zend_Json::decode($rawBody));
                break;
            case (strstr($contentType, 'application/xml')):
                $config = new Zend_Config_Xml($rawBody);
                $this-&gt;setBodyParams($config-&gt;toArray());
                break;
            default:
                if ($request-&gt;isPut()) {
                    parse_str($rawBody, $params);
                    $this-&gt;setBodyParams($params);
                }
                break;
        }
    }

    /**
     * Set body params
     * 
     * @param  array $params 
     * @return Scrummer_Controller_Action
     */
    public function setBodyParams(array $params)
    {
        $this-&gt;_bodyParams = $params;
        return $this;
    }

    /**
     * Retrieve body parameters
     * 
     * @return array
     */
    public function getBodyParams()
    {
        return $this-&gt;_bodyParams;
    }

    /**
     * Get body parameter
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getBodyParam($name)
    {
        if ($this-&gt;hasBodyParam($name)) {
            return $this-&gt;_bodyParams[$name];
        }
        return null;
    }

    /**
     * Is the given body parameter set?
     * 
     * @param  string $name 
     * @return bool
     */
    public function hasBodyParam($name)
    {
        if (isset($this-&gt;_bodyParams[$name])) {
            return true;
        }
        return false;
    }

    /**
     * Do we have any body parameters?
     * 
     * @return bool
     */
    public function hasBodyParams()
    {
        if (!empty($this-&gt;_bodyParams)) {
            return true;
        }
        return false;
    }

    /**
     * Get submit parameters
     * 
     * @return array
     */
    public function getSubmitParams()
    {
        if ($this-&gt;hasBodyParams()) {
            return $this-&gt;getBodyParams();
        }
        return $this-&gt;getRequest()-&gt;getPost();
    }

    public function direct()
    {
        return $this-&gt;getSubmitParams();
    }
}
</code></pre></div>

<p>
    This helper is intended to be run on each request, so I register it in my
    bootstrap:
</p>

<div class="example"><pre><code class="language-php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // ...
    protected function _initActionHelpers()
    {
        // ...
        $params = new Scrummer_Controller_Helper_Params();
        Zend_Controller_Action_HelperBroker::addHelper($params);
        // ...
    }
    // ...
}
</code></pre></div>

<p>
    Within your action controller, all you need to do is call the helper:
</p>

<div class="example"><pre><code class="language-php">
$data = $this-&gt;params();
</code></pre></div>

<p>
    In a RESTful controller, you'll only need to use this with your
    <code>postAction</code> and <code>putAction</code>. The beauty is that your
    controller can remain ignorant of the Content-Type -- you write the same
    logic to retrieve your parameters regardless.
</p>

<h2>Responding to the client: Context Switching</h2>

<p>
    So, the first half of the problem is taken care of: how to handle the
    request. The second half is responding appropriately.
</p>

<p>
    Zend Framework has some built in tooling to help with this. The
    ContextSwitch and AjaxContext action helpers look for a particular
    parameter -- "format" by default -- and, if detected, will render an
    alternate view script named after the context. As an example, if an "XML"
    context is detected, it will render
    "&lt;controller&gt;/&lt;action&gt;.xml.phtml" -- note the ".xml" segment of
    the script name.
</p>

<p>
    Both helpers work in the same basic way (the latter, AjaxContext, will only
    activate if the request is determined to originate from an XMLHttpRequest):
    you define which actions in the controller are context sensitive, and then
    if the context is detected, a new view script will be used.
</p>

<p>
    So, the first trick is ensuring that the context is passed. As mentioned
    before, the helpers look for a "format" parameter in the request object. You
    can pass this using a query parameter -- "?format=xml" -- but I find that
    ugly. There's an HTTP header defined for this purpose already: "Accept".
</p>

<p>
    Detecting the header and injecting the context into the request is absurdly
    simple, and can be done in a <code>dispatchLoopStartup</code> plugin:
</p>

<div class="example"><pre><code class="language-php">
class Scrummer_Controller_Plugin_AcceptHandler
    extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            return;
        }

        $header = $request-&gt;getHeader('Accept');
        switch (true) {
            case (strstr($header, 'application/json')):
                $request-&gt;setParam('format', 'json');
                break;
            case (strstr($header, 'application/xml') 
                  &amp;&amp; (!strstr($header, 'html'))):
                $request-&gt;setParam('format', 'xml');
                break;
            default:
                break;
        }
    }
}
</code></pre></div>

<p>
    The above can be registered in your application configuration:
</p>

<div class="example"><pre><code class="language-ini">
resources.frontController.plugins[] = \&quot;Scrummer_Controller_Plugin_AcceptHandler\&quot;
</code></pre></div>

<p>
    I like my RESTful controllers to automatically expose their methods as
    context-aware. To make this happen, I defined a marker interface,
    "Scrummer_Rest_Controller", and created an action helper that checks if the
    current controller implements it; if it does, I then automatically add
    contexts for the RESTful actions.
</p>

<div class="example"><pre><code class="language-php">
class Scrummer_Controller_Helper_RestContexts
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_contexts = array(
        'xml', 
        'json',
    );

    public function preDispatch()
    {
        $controller = $this-&gt;getActionController();
        if (!$controller instanceof Scrummer_Rest_Controller) {
            return;
        }

        $this-&gt;_initContexts();

        // Set a Vary response header based on the Accept header
        $this-&gt;getResponse()-&gt;setHeader('Vary', 'Accept');
    }

    protected function _initContexts()
    {
        $cs = $this-&gt;getActionController()-&gt;contextSwitch;
        $cs-&gt;setAutoJsonSerialization(false);
        foreach ($this-&gt;_contexts as $context) {
            foreach (array('index', 'post', 'get', 'put', 'delete') as $action) {
                $cs-&gt;addActionContext($action, $context);
            }
        }
        $cs-&gt;initContext();
    }
}
</code></pre></div>

<p>
    Register this via the bootstrap as well:
</p>

<div class="example"><pre><code class="language-php">
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // ...
    protected function _initActionHelpers()
    {
        // ...
        $params = new Scrummer_Controller_Helper_Params();
        Zend_Controller_Action_HelperBroker::addHelper($params);

        $contexts = new Scrummer_Controller_Helper_RestContexts();
        Zend_Controller_Action_HelperBroker::addHelper($contexts);
        // ...
    }
    // ...
}
</code></pre></div>

<p>
    There are two things to note about this helper. First, you'll see that I
    specify a "Vary" header. This is to ensure that if the client chooses to
    cache responses, it will cache separate responses based on the value sent in
    the "Accept" header.
</p>

<p>
    Second, note that I turn off automatic JSON serialization in the
    ContextSwitch helper. I do this so that I can keep my controller context
    agnostic; this will require additional view scripts, but the ability to keep
    my controller logic simple will be worth it. More on that in a moment.
</p>

<p>
    We now have the infrastructure in place to respond to different contexts
    based on the "Accept" header, and can retrieve parameters appropriately
    based on the "Content-Type" provided us. Now comes the actual response.
</p>

<h2>Responding to the client: Views</h2>

<p>
    Recall that ContextSwitch will attach an additional prefix to the specified
    view script -- "&lt;controller&gt;/&lt;action&gt;.phtml" will become
    "&lt;controller&gt;/&lt;action&gt;.xml.phtml" or
    "&lt;controller&gt;/&lt;action&gt;.json.phtml". Basically, for each context
    we will respond to, we have an additional view script per action.
</p>

<div class="example"><pre><code class="language-text">
views/
|-- scripts/
|   `-- foo/
|      |-- delete.phtml
|      |-- delete.json.phtml
|      |-- delete.xml.phtml
|      |-- get.phtml
|      |-- get.json.phtml
|      |-- get.xml.phtml
|      |-- index.phtml
|      |-- index.json.phtml
|      |-- index.xml.phtml
|      |-- post.phtml
|      |-- post.json.phtml
|      |-- post.xml.phtml
|      |-- put.phtml
|      |-- put.json.phtml
|      `-- put.xml.phtml
</code></pre></div>

<p>
    This may seem like overkill, but consider the following representative
    method from my controller:
</p>

<div class="example"><pre><code class="language-php">
    public function postAction()
    {
        $data    = $this-&gt;params();
        $service = $this-&gt;getService();
        $result  = $service-&gt;add($data);  
        if (!$result) {
            $this-&gt;view-&gt;form = $service-&gt;getBacklogForm();
            return;
        }

        $this-&gt;view-&gt;success = true;
        $this-&gt;view-&gt;backlog = $result;
    }
</code></pre></div>

<p>
    You don't see anything in there about headers, redirects, or XHR requests.
    Just slinging data to services and views. Real simple.
</p>

<p>
    The view scripts then take care of the appropriate display logic. Let's look
    at two view scripts for the above action, one for plain old HTML, the other
    for a JSON response:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php // backlog/post.phtml ?&gt;
&lt;?php 
if ($this-&gt;success):
    $this-&gt;response-&gt;setRedirect($this-&gt;url(array(
        'controller' =&gt; 'backlog',
        'id'         =&gt; $this-&gt;backlog-&gt;id,
    ), 'rest', true));
else: ?&gt;
&lt;h2&gt;Create new backlog&lt;/h2&gt;
&lt;?php
    $this-&gt;form-&gt;setAction($this-&gt;url())
               -&gt;setMethod('post');
    echo $this-&gt;form;
endif ?&gt;

&lt;?php // backlog/post.json.phtml ?&gt;
&lt;?php
if ($this-&gt;success) {
    $url = $this-&gt;url(array(
        'controller' =&gt; 'backlog',
        'id'         =&gt; $this-&gt;backlog-&gt;id,
    ), 'rest', true);
    $this-&gt;response-&gt;setHeader('Location', $url)
                   -&gt;setHttpResponseCode(201);
    echo $this-&gt;json($this-&gt;backlog-&gt;toArray());
    return;
}

$form = $this-&gt;form;
$form-&gt;setAction($this-&gt;url())
     -&gt;setMethod('post');
echo $this-&gt;jsonFormErrors($form);
</code></pre></div>

<p>
    A few things to note: I inject my response object into the view. I feel
    HTTP headers are part of the view, and thus I deal with them there. That
    also serves the purpose of keeping my controllers thin and agnostic.
    Additionally, you'll note that I use different response codes for HTML
    versus JSON -- this allows my JSON-REST support to be RESTful, by returning
    a 201 status code indicating the resource was created; I also return a JSON
    representation of the object. Finally, you'll note that I have a special
    view helper for creating JSON representations of validation errors.
</p>

<h2>Closing points</h2>

<p>
    This post is far from exhaustive, and I expect it will likely raise at least
    as many questions as it tries to answer.
</p>

<p>
    My main point in this article is to get you, the reader and developer,
    thinking creatively about how to expose RESTful web services. Hopefully,
    you're taking the following away:
</p>

<ol>
    <li>Architect in such a way as to minimize the code in your controllers;
    keep that code as agnostic as possible in regards to where input comes from
    and what type of response is required.</li>

    <li>Use front controller plugins and action helpers to create scaffolding
    for your services; these are incredibly flexible and re-usable, and help
    make point 1 that much easier.</li>

    <li>Offload as much as possible to your views. This will allow you to
    isolate logic specific to given formats.</li>
</ol>

<p>
    What are you waiting for? Don't you have an API to expose?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
