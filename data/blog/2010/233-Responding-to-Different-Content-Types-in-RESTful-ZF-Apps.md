---
id: 233-Responding-to-Different-Content-Types-in-RESTful-ZF-Apps
author: matthew
title: 'Responding to Different Content Types in RESTful ZF Apps'
draft: false
public: true
created: '2010-03-04T15:28:07-05:00'
updated: '2010-03-10T09:28:22-05:00'
tags:
    - php
    - rest
    - 'zend framework'
---
In [previous](/blog/227-Exposing-Service-APIs-via-Zend-Framework.html)
[articles](/blog/228-Building-RESTful-Services-with-Zend-Framework.html), I've
explored building service endpoints and RESTful services with Zend Framework.
With RPC-style services, you get to cheat: the protocol dictates the content
type (XML-RPC uses XML, JSON-RPC uses JSON, SOAP uses XML, etc.). With REST,
however, you have to make choices: what serialization format will you support?

Why not support multiple formats?

There's no reason you can't re-use your RESTful web service to support multiple
formats. Zend Framework and PHP have plenty of tools to assist you in responding
to different format requests, so don't limit yourself. With a small amount of
work, you can make your controllers format agnostic, and ensure that you respond
appropriately to different requests.

<!--- EXTENDED -->

Content-Type Detection
----------------------

The first problem to solve is going to be how to retrieve passed parameters.
When using XML or JSON as your serialization format, you aren't getting your
standard POST variables — you're getting a raw post instead, and you'll need to
deserialize the payload. In fact, if you're getting a PUT request, you also have
some work to do, as PHP doesn't do anything with PUT requests.

I do this via an action helper. The basic algorithm is:

- Do we have a raw body in the request? If not, nothing more need be done.
- Determine the Content-Type passed in the request headers, and decode appropriately:
  - If it was JSON, pass the raw request body to `json_decode` or `Zend_Json::decode`.
  - If it was XML, I pass the raw request body to the `Zend_Config_XML` constructor, and then serialize to an arrya using the `toArray()` method. Yes, it's a hack, but it's effective.
  - Otherwise, I assume I've got a regular PUT-style request, and I pass the data to `parse_str()`.

I keep the values within the action helper, and then retrieve them on demand
within my action controller. The helper looks like the following:

```php
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
        $request     = $this->getRequest();
        $contentType = $request->getHeader('Content-Type');
        $rawBody     = $request->getRawBody();
        if (!$rawBody) {
            return;
        }
        switch (true) {
            case (strstr($contentType, 'application/json')):
                $this->setBodyParams(Zend_Json::decode($rawBody));
                break;
            case (strstr($contentType, 'application/xml')):
                $config = new Zend_Config_Xml($rawBody);
                $this->setBodyParams($config->toArray());
                break;
            default:
                if ($request->isPut()) {
                    parse_str($rawBody, $params);
                    $this->setBodyParams($params);
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
        $this->_bodyParams = $params;
        return $this;
    }

    /**
     * Retrieve body parameters
     * 
     * @return array
     */
    public function getBodyParams()
    {
        return $this->_bodyParams;
    }

    /**
     * Get body parameter
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getBodyParam($name)
    {
        if ($this->hasBodyParam($name)) {
            return $this->_bodyParams[$name];
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
        if (isset($this->_bodyParams[$name])) {
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
        if (!empty($this->_bodyParams)) {
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
        if ($this->hasBodyParams()) {
            return $this->getBodyParams();
        }
        return $this->getRequest()->getPost();
    }

    public function direct()
    {
        return $this->getSubmitParams();
    }
}
```

This helper is intended to be run on each request, so I register it in my
bootstrap:

```php
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
```

Within your action controller, all you need to do is call the helper:

```php
$data = $this->params();
```

In a RESTful controller, you'll only need to use this with your `postAction` and
`putAction`. The beauty is that your controller can remain ignorant of the
Content-Type — you write the same logic to retrieve your parameters regardless.

Responding to the client: Context Switching
-------------------------------------------

So, the first half of the problem is taken care of: how to handle the request.
The second half is responding appropriately.

Zend Framework has some built in tooling to help with this. The ContextSwitch
and AjaxContext action helpers look for a particular parameter — "format" by
default — and, if detected, will render an alternate view script named after the
context. As an example, if an "XML" context is detected, it will render
`<controller>/<action>.xml.phtml` — note the `.xml` segment of the script name.

Both helpers work in the same basic way (the latter, AjaxContext, will only
activate if the request is determined to originate from an XMLHttpRequest): you
define which actions in the controller are context sensitive, and then if the
context is detected, a new view script will be used.

So, the first trick is ensuring that the context is passed. As mentioned before,
the helpers look for a "format" parameter in the request object. You can pass
this using a query parameter — `?format=xml` — but I find that ugly. There's an
HTTP header defined for this purpose already: "Accept".

Detecting the header and injecting the context into the request is absurdly
simple, and can be done in a `dispatchLoopStartup` plugin:

```php
class Scrummer_Controller_Plugin_AcceptHandler
    extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            return;
        }

        $header = $request->getHeader('Accept');
        switch (true) {
            case (strstr($header, 'application/json')):
                $request->setParam('format', 'json');
                break;
            case (strstr($header, 'application/xml') 
                  && (!strstr($header, 'html'))):
                $request->setParam('format', 'xml');
                break;
            default:
                break;
        }
    }
}
```

The above can be registered in your application configuration:

```ini
resources.frontController.plugins[] = "Scrummer_Controller_Plugin_AcceptHandler"
```

I like my RESTful controllers to automatically expose their methods as
context-aware. To make this happen, I defined a marker interface,
`Scrummer_Rest_Controller`, and created an action helper that checks if the
current controller implements it; if it does, I then automatically add contexts
for the RESTful actions.

```php
class Scrummer_Controller_Helper_RestContexts
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_contexts = array(
        'xml', 
        'json',
    );

    public function preDispatch()
    {
        $controller = $this->getActionController();
        if (!$controller instanceof Scrummer_Rest_Controller) {
            return;
        }

        $this->_initContexts();

        // Set a Vary response header based on the Accept header
        $this->getResponse()->setHeader('Vary', 'Accept');
    }

    protected function _initContexts()
    {
        $cs = $this->getActionController()->contextSwitch;
        $cs->setAutoJsonSerialization(false);
        foreach ($this->_contexts as $context) {
            foreach (array('index', 'post', 'get', 'put', 'delete') as $action) {
                $cs->addActionContext($action, $context);
            }
        }
        $cs->initContext();
    }
}
```

Register this via the bootstrap as well:

```php
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
```

There are two things to note about this helper. First, you'll see that I specify
a "Vary" header. This is to ensure that if the client chooses to cache
responses, it will cache separate responses based on the value sent in the
"Accept" header.

Second, note that I turn off automatic JSON serialization in the ContextSwitch
helper. I do this so that I can keep my controller context agnostic; this will
require additional view scripts, but the ability to keep my controller logic
simple will be worth it. More on that in a moment.

We now have the infrastructure in place to respond to different contexts based
on the "Accept" header, and can retrieve parameters appropriately based on the
"Content-Type" provided us. Now comes the actual response.

Responding to the client: Views
-------------------------------

Recall that ContextSwitch will attach an additional prefix to the specified view
script — `<controller>/<action>.phtml` will become
`<controller>/<action>.xml.phtml` or `<controller>/<action>.json.phtml`.
Basically, for each context we will respond to, we have an additional view
script per action.

```
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
```

This may seem like overkill, but consider the following representative method
from my controller:

```php
    public function postAction()
    {
        $data    = $this->params();
        $service = $this->getService();
        $result  = $service->add($data);  
        if (!$result) {
            $this->view->form = $service->getBacklogForm();
            return;
        }

        $this->view->success = true;
        $this->view->backlog = $result;
    }
```

You don't see anything in there about headers, redirects, or XHR requests. Just
slinging data to services and views. Real simple.

The view scripts then take care of the appropriate display logic. Let's look at
two view scripts for the above action, one for plain old HTML, the other for a
JSON response:

```php
<?php // backlog/post.phtml ?>
<?php 
if ($this->success):
    $this->response->setRedirect($this->url(array(
        'controller' => 'backlog',
        'id'         => $this->backlog->id,
    ), 'rest', true));
else: ?>
<h2>Create new backlog</h2>
<?php
    $this->form->setAction($this->url())
               ->setMethod('post');
    echo $this->form;
endif ?>

<?php // backlog/post.json.phtml ?>
<?php
if ($this->success) {
    $url = $this->url(array(
        'controller' => 'backlog',
        'id'         => $this->backlog->id,
    ), 'rest', true);
    $this->response->setHeader('Location', $url)
                   ->setHttpResponseCode(201);
    echo $this->json($this->backlog->toArray());
    return;
}

$form = $this->form;
$form->setAction($this->url())
     ->setMethod('post');
echo $this->jsonFormErrors($form);
```

A few things to note: I inject my response object into the view. I feel HTTP
headers are part of the view, and thus I deal with them there. That also serves
the purpose of keeping my controllers thin and agnostic. Additionally, you'll
note that I use different response codes for HTML versus JSON — this allows my
JSON-REST support to be RESTful, by returning a 201 status code indicating the
resource was created; I also return a JSON representation of the object.
Finally, you'll note that I have a special view helper for creating JSON
representations of validation errors.

Closing points
--------------

This post is far from exhaustive, and I expect it will likely raise at least as
many questions as it tries to answer.

My main point in this article is to get you, the reader and developer, thinking
creatively about how to expose RESTful web services. Hopefully, you're taking
the following away:

1. Architect in such a way as to minimize the code in your controllers; keep that code as agnostic as possible in regards to where input comes from and what type of response is required.
2. Use front controller plugins and action helpers to create scaffolding for your services; these are incredibly flexible and re-usable, and help make point 1 that much easier.
3. Offload as much as possible to your views. This will allow you to isolate logic specific to given formats.

What are you waiting for? Don't you have an API to expose?
