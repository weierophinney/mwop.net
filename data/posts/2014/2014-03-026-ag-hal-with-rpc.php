<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2014-03-26-apigility-rpc-with-hal');
$entry->setTitle('Apigility: Using RPC with HAL');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2014-03-26 15:30', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2014-03-26 15:30', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'apigility',
  'zf2',
  'zend framework',
  'rest',
  'hal',
));

$body =<<<'EOT'
<p>
    A few days ago, we <a href="http://bit.ly/ag-1-beta1">released our first beta of Apigility</a>.
    We've started our documentation effort now, and one question has arisen a few times that I
    want to address: How can you use Hypermedia Application Language (HAL) in RPC services?
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>HAL?</h2>

<p>
    <a href="http://tools.ietf.org/html/draft-kelly-json-hal-06">Hypermedia Application Language</a>
    is an IETF proposal for how to represent resources and their relations within APIs. Technically,
    it provides two mediatypes, <code>application/hal+json</code> and <code>application/hal+xml</code>;
    however, Apigility only provides the JSON variant.
</p>

<p>
    The important things to know about HAL are:
</p>

<ul>
    <li>
        <p>
            It provides a standard way of describing relational links. All relational
            links are under a <code>_links</code> property of the resource. That property
            is an object. Each property of that object is a link relation; the value of
            each link relation is an object (or array of such objects) describing the link
            that must minimally contain an <code>href</code> proerty. The link object
            itself can contain some additional metadata, such as a mediatype, a name
            (useful for differentiating between multiple link objects assigned to the same
            relation).
        </p>

        <p>
            While not required, the specification recommends resources contain a "self"
            relational link, indicating the canonical location for the resource. This
            is particularly useful when we consider embedding (the next topic).
        </p>

        <p>
            Sound hard? It's not:
        </p>

        <div class="example"><pre><code language="javascript">
{
    "_links": {
        "self": {
            "href": "/blog/2014-03-26-apigility-rpc-with-hal"
        }
    }
}
        </code></pre></div>
    </li>

    <li>
        <p>
            Besides link relations, HAL also provides a standard way of describing
            <em>embedded resources</em>. An embedded resource is any other resource
            you can address via your API, and, as such, would be structured as a HAL
            resource -- in other words, it would have a <code>_links</code> property
            with relational links. Essentially, any property of the resource you're
            returning that can itself be addressed via the URI must be <em>embedded</em>
            in the resource. This is done via the property <code>_embedded</code>.
        </p>

        <p>
            Like <code>_links</code>, <code>_embedded</code> is an object. Each key in the
            object is the local name by which the resource refers to the embedded resource.
            The value of such keys can either be HAL resources or <em>arrays</em> of HAL
            resources; in fact, this is how <em>collections</em> are represented in HAL!
        </p>

        <p>
            As examples:
        </p>

        <div class="example"><pre><code language="javascript">
{
    "_links": {
        "self": {
            "href": "/blog/2014-03-26-apigility-rpc-with-hal"
        }
    },
    "_embedded": {
        "author": {
            "_links": {
                "self": {
                    "href": "/blog/author/matthew"
                }
            },
            "id": "matthew",
            "name": "Matthew Weier O'Phinney",
            "url": "http://mwop.net"
        },
        "tags": [
            {
                "_links": {
                    "self": {
                        "href": "/blog/tag/php"
                    }
                },
                "id": "php"
            },
            {
                "_links": {
                    "self": {
                        "href": "/blog/tag/rest"
                    }
                },
                "id": "rest"
            }
        ]
    }
}
        </code></pre></div>

        <p>
            The example above shows two embedded resources. The first is the author;
            the second, a collection of tags. Note that <em>every</em> object
            under <code>_embedded</code> is a HAL object!
        </p>

        <p>
            You can go quite far with this -- you can also have embedded resources
            inside your embedded resources, arbitrarily deep.
        </p>
    </li>
</ul>

<h2>RPC?</h2>

<p>
    RPC stands for Remote Procedure Call, and, when describing a web API, is 
    usually used to describe a web service that publishes multiple method calls 
    at a single URI using only <code>POST</code>; XML-RPC and SOAP are the
    usual suspects.
</p>

<p>
    In Apigility, we use the term RPC in a much looser sense; we use it to describe
    one-off services: actions like "authenticate," or "notify," or "register" 
    would all make sense here. They are actions that usually only need to respond
    to a single HTTP method, and which may or may not describe a "thing", which
    is what we usually consider a "resource" when discussing REST terminology.
</p>

<p>
    That said: what if what we want to return from the RPC call <em>are</em> REST
    resources?
</p>

<h2>Returning HAL from RPC Services</h2>

<p>
    In order to return HAL from RPC services, we need to understand (a) how 
    Content Negotiation works, and (b) what needs to be returned in order for the
    HAL renderer to be able to create a representation.
</p>

<p>
    For purposes of this example, I'm positing a <code>RegisterController</code> as
    an RPC service that, on success, is returning a <code>User</code> object 
    that I want rendered as a HAL resource.
</p>

<p>
    The <a href="https://github.com/zfcampus/zf-content-negotiation">zf-content-negotiation</a>
    module takes care of content negotiation for Apigility. It introspects the <code>Accept</code>
    header in order to determine if we can return a representation, and then, if it can, will
    cast any <code>ZF\ContentNegotiation\ViewModel</code> returned from a controller to the
    appropriate view model for the representation. From there, a renderer will pick up the view
    model and do what needs to be done.
</p>

<p>
    So, the first thing we have to do is return <code>ZF\ContentNegotiation\ViewModel</code>
    instances from our controller.
</p>

<div class="example"><pre><code language="php">
use Zend\Mvc\Controller\AbstractActionController;
use ZF\ContentNegotiation\ViewModel;

class RegisterController extends AbstractActionController
{
    public function registerAction()
    {
        /* ... do some work ... get a user ... */
        return new ViewModel(array('user' => $user));
    }
}
</code></pre></div>

<p>
    The <a href="https://github.com/zfcampus/zf-hal">zf-hal</a> module in Apigility
    creates the actual HAL representations. <code>zf-hal</code> looks for a "payload" variable in
    the view model, and expects that value to be either a <code>ZF\Hal\Entity</code>
    (single item) or <code>ZF\Hal\Collection</code>. When creating an <code>Entity</code>
    object, you need the object being represented, as well as the identifier. 
    So, let's update our return value.
</p>

<div class="example"><pre><code language="php">
use Zend\Mvc\Controller\AbstractActionController;
use ZF\ContentNegotiation\ViewModel;
use ZF\Hal\Entity;

class RegisterController extends AbstractActionController
{
    public function registerAction()
    {
        /* ... do some work
         * ... get a $user
         * ... assume we have also now have an $id
         */
        return new ViewModel(array('payload' => array(
            'user' => new Entity($user, $id),
        )));
    }
}
</code></pre></div>

<p>
    <code>zf-hal</code> contains what's called a "metadata map". This is a map of classes to
    information on how <code>zf-hal</code> should render them: what route to use, what additional
    relational links to inject, how to serialize the object, what field represents
    the identifier, etc.
</p>

<p>
    In most cases, you will have likely already defined a REST service for the
    resource you want to return from the RPC service, in which case you will
    be done. However, if you want, you can go in and manually configure
    the metadata map in your API module's <code>config/module.config.php</code>
    file:
</p>

<div class="example"><pre><code language="php">
return array(
    /* ... */
    'zf-hal' => array(
        'metadata_map' => array(
            'User' => array(
                'route_name' => 'api.rest.user',
                'entity_identifier_name' => 'username',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\Stdlib\Hydrator\ObjectProperty',
            ),
        ),
    ),
);
</code></pre></div>

<p>
    Finally, we need to make sure that the service is configured to actually return
    HAL. We can do this in the admin if we want. Find the "Content Negotiation" section
    of the admin, and the "Content Negotiation Selector" item, and set that to "HalJson";
    don't forget to save! Alternately, you can do this manually in the API module's 
    <code>config/module.config.php</code> file, under the <code>zf-content-negotiation</code>
    section:
</p>

<div class="example"><pre><code language="php">
return array(
    /* ... */
    'zf-content-negotiation' => array(
        'controllers' => array(
            /* ... */
            'RegisterController' => 'HalJson',
        ),
        /* ... */
    ),
);
</code></pre></div>

<p>
    Once your changes are complete, when you make a successful request to the URI
    for your "register" RPC service, you'll receive a HAL response pointing to the
    canonical URI for the user resource created!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
