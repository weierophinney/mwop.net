<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-02-25-restful-apis-with-zf2-part-3');
$entry->setTitle('RESTful APIs with ZF2, Part 3');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2013-02-25 06:29', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-02-25 06:29', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'rest',
  'http',
  'zf2',
  'zend framework',
));

/**
 * Outline
 *
 * - Why is documentation important?
 * - What should you document?
 *   - What endpoints are available
 *   - Which operations are available for each endpoint (OPTIONS/Allow dance)
 *   - What payloads each endpoint expects
 *   - What payloads each endpoint will return
 *   - What errors are likely, and how they will look
 * - Where and how should you document?
 *   - OPTIONS
 *     - At the very least, to report Allow'd operations
 *     - Demonstrate how to react via AbstractRestfulController
 *     - Demonstrate a listener that raises an error (and correct code) when 
 *       request method is not allowed.
 *     - Potentially to provide the documentation itself
 *   - Static endpoint, linked via Link header
 *     - Documentation is linked in every request
 *     - What format?
 *       - Text-only formats are nice when you consider cURL, HTTPie, and other 
 *         tools.
 *       - Whatever you want, really.
 *     - Use PhlySimplePage or Soflomo\Prototype to return a page of docs
 */

$body =<<<'EOT'
<p>
    In my <a href="/blog/2013-02-11-restful-apis-with-zf2-part-1.html">previous</a> 
    <a href="/blog/2013-02-13-restful-apis-with-zf2-part-2.html">posts</a>, I 
    covered basics of JSON hypermedia APIs using Hypermedia Application Language
    (HAL), and methods for reporting errors, including API-Problem and vnd.error.
</p>

<p>
    In this post, I'll be covering <em>documenting</em> your API -- techniques 
    you can use to indicate what HTTP operations are allowed, as well as convey 
    the full documentation on what endpoints are available, what they accept, 
    and what you can expect them to return.
</p>

<p>
    While I will continue covering general aspects of RESTful APIs in this 
    post, I will also finally introduce several ZF2-specific techniques.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Why Document?</h2>

<p>
    If you're asking this question, you've either never consumed software, or
    your software is perfect and self-documenting. I frankly don't believe 
    either one.
</p>

<p>
    In the case of APIs, those consuming the API need to know how to use it. 
</p>

<ul>
    <li>What endpoints are available? Which operations are available for each endpoint?</li>
    <li>What does each endpoint expect as a payload during the request?</li>
    <li>What can you expect as a payload in return?</li>
    <li>How will errors be communicated?</li>
</ul>

<p>
    While the promise of hypermedia APIs is that each response tells you the
    next steps available, you still, somewhere along the way, need more
    information - what payloads look like, which HTTP verbs should be used,
    and more. If you're <strong>not</strong> documenting your API, you're
    "doing it wrong."
</p>

<h2>Where Should Documentation Live?</h2>

<p>
    This is the much bigger question.
</p>

<p>
    Of the questions I raised above, detailing what should be documented, there
    are two specific types. When discussing what operations are available, 
    we have a technical solution in the form of the <code>OPTIONS</code>
    method and its counterpart, the <code>Allow</code> header. Everything
    else falls under end-user documentation.
</p>

<h2>OPTIONS</h2>

<p>
    The HTTP specification details the <code>OPTIONS</code> method as 
    idempotent, non-cacheable, and for use in detailing what operations
    are available for the given resource specified by the request URI. It
    makes specific mention of the <code>Allow</code> header, but does not
    limit what is returned for requests made via this method.
</p>

<p>
    The <code>Allow</code> header details the allowed HTTP methods for the
    given resource.
</p>

<p>
    Used in combination, you make an <code>OPTIONS</code> request to a URI,
    and it should return a response containing an <code>Allow</code> header;
    from that header value, you then know what other HTTP methods can be made
    to that URI.
</p>

<p>
    What this tells us is that our RESTful endpoint should do the following:
</p>

<ul>
    <li>
        When an <code>OPTIONS</code> request is made, return a response with
        an <code>Allow</code> header that has a list of the available HTTP
        methods allowed.
    </li>

    <li>
        For any HTTP method we do <em>not</em> allow, we should return a
        "405 Not Allowed" response.
    </li>
</ul>

<p>
    These are fairly easy to accomplish in ZF2. <em>(See? I promised I'd
    get to some ZF2 code in this post!)</em>
</p>

<p>
    When creating RESTful endpoints in ZF2, I recommend using
    <code>Zend\Mvc\Controller\AbstractRestfulController</code>. This controller
    contains an <code>options()</code> method which you can use to respond to
    an <code>OPTIONS</code> request. As with any ZF2 controller, returning
    a response object will prevent rendering and bubble out immediately so
    that the response is returned.
</p>

<div class="example"><pre><code class="language-php">
namespace My\Controller;
use Zend\Mvc\Controller\AbstractRestfulController;

class FooController extends AbstractRestfulController
{
    public function options()
    {
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        // If you want to vary based on whether this is a collection or an
        // individual item in that collection, check if an identifier from
        // the route is present
        if ($this->params()->fromRoute('id', false)) {
            // Allow viewing, partial updating, replacement, and deletion
            // on individual items
            $headers->addHeaderLine('Allow', implode(',', array(
                'GET',
                'PATCH',
                'PUT',
                'DELETE',
            )));
            return $response;
        }

        // Allow only retrieval and creation on collections
        $headers->addHeaderLine('Allow', implode(',', array(
            'GET',
            'POST',
        )));
        return $response;
    }
}
</code></pre></div>

<p>
    The next trick is returning the 405 response if an invalid option is used.
    For this, you can create a listener in your controller, and wire it to 
    listen at higher-than-default priority. As an example:
</p>

<div class="example"><pre><code class="language-php">
namespace My\Controller;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractRestfulController;

class FooController extends AbstractRestfulController
{
    protected $allowedCollectionMethods = array(
        'GET',
        'POST',
    );

    protected $allowedResourceMethods = array(
        'GET',
        'PATCH',
        'PUT',
        'DELETE',
    );

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', array($this, 'checkOptions'), 10);
    }

    public function checkOptions($e)
    {
        $matches  = $e->getRouteMatch();
        $response = $e->getResponse();
        $request  = $e->getRequest();
        $method   = $request->getMethod();

        // test if we matched an individual resource, and then test
        // if we allow the particular request method
        if ($matches->getParam('id', false)) {
            if (!in_array($method, $this->allowedResourceMethods)) {
                $response->setStatusCode(405);
                return $response;
            }
            return;
        }

        // We matched a collection; test if we allow the particular request 
        // method
        if (!in_array($method, $this->allowedCollectionMethods)) {
            $response->setStatusCode(405);
            return $response;
        }
    }
}
</code></pre></div>

<p>
    Note that I moved the allowed methods into properties; if I did the above,
    I'd refactor the <code>options()</code> method to use those properties as
    well to ensure they are kept in sync.
</p>

<p>
    Also note that in the case of an invalid method, I return a response object.
    This ensures that nothing else needs to execute in the controller; I
    discover the problem and return early.
</p>

<h2>End-User Documentation</h2>

<p>
    Now that we have the technical solution out of the way, we're still left 
    with the bulk of the work left to accomplish: providing end-user 
    documentation detailing the various payloads, errors, etc.
</p>

<p>
    I've seen two compelling approaches to this problem. The first builds on
    the <code>OPTIONS</code> method, and the other uses a hypermedia link in
    every response to point to documentation.
</p>

<p>
    The <code>OPTIONS</code> solution is this: <a 
    href="http://zacstewart.com/2012/04/14/http-options-method.html">use the 
    body of an <code>OPTIONS</code> response to provide documentation</a>.
    (Keith Casey <a href="http://vimeo.com/49613738">gave an excellent short 
    presentation about this at REST Fest 2012</a>).
</p>

<p>
    The <code>OPTIONS</code> method allows for you to return a body in the
    response, and also allows for content negotiation. The theory, then, is
    that you return media-type-specific documentation that details the
    methods allowed, and what they specifically accept in the body. While
    there is no standard for this at this time, the first article I linked
    suggested including a description, the parameters expected, and one or more 
    example request bodies for each HTTP method allowed; you'd likely also
    want to detail the responses that can be expected.
</p>

<div class="example"><pre><code class="language-javascript">
{
    "POST": {
        "description": "Create a new status",
        "parameters": {
            "type": {
                "type": "string",
                "description": "Status type -- text, image, or url; defaults to text",
                "required": false
            },
            "text": {
                "type": "string",
                "description": "Status text; required for text types, optional for others",
                "required": false
            },
            "image_url": {
                "type": "string",
                "description": "URL of image for image types; required for image types",
                "required": false
            },
            "link_url": {
                "type": "string",
                "description": "URL of image for link types; required for link types",
                "required": false
            }
        },
        "responses": [
            {
                "describedBy": "http://example.com/problems/invalid-status",
                "title": "Submitted status was invalid",
                "detail": "Missing text field required for text type"
            },
            {
                "id": "abcdef123456",
                "type": "text",
                "text": "This is a status update",
                "timestamp": "2013-02-22T10:06:05+0:00"
            }
        ],
        "examples": [
            {
                "text": "This is a status update"
            },
            {
                "type": "image",
                "text": "This is the image caption",
                "image_url": "http://example.com/favicon.ico"
            },
            {
                "type": "link",
                "text": "This is a description of the link",
                "link_url": "http://example.com/"
            },
        ]
    }
}
</code></pre></div>

<p>
    If you were to use this methodology, you would alter the 
    <code>options()</code> method such that it does not return a response
    object, but instead return a view model with the documentation.
</p>

<div class="example"><pre><code class="language-php">
namespace My\Controller;
use Zend\Mvc\Controller\AbstractRestfulController;

class FooController extends AbstractRestfulController
{
    protected $viewModelMap = array(/* ... */);

    public function options()
    {
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        // Get a view model based on Accept types
        $model    = $this->acceptableViewModelSelector($this->viewModelMap);

        // If you want to vary based on whether this is a collection or an
        // individual item in that collection, check if an identifier from
        // the route is present
        if ($this->params()->fromRoute('id', false)) {
            // Still set the Allow header
            $headers->addHeaderLine('Allow', implode(
                ',', 
                $this->allowedResourceMethods
            ));

            // Set documentation specification as variables
            $model->setVariables($this->getResourceDocumentationSpec());
            return $model;
        }

        // Allow only retrieval and creation on collections
        $headers->addHeaderLine('Allow', implode(
            ',',
            $this->allowedCollectionMethods
        ));
        $model->setVariables($this->getCollectionDocumentationSpec());
        return $model;
    }
}
</code></pre></div>

<p>
    I purposely didn't provide the implementations of the 
    <code>getResourceDocumentationSpec()</code> and 
    <code>getCollectionDocumentationSpec()</code> methods, as that will likely
    be highly specific to your application. Another possibility is to use
    your view engine for this, and specify a template file that has the
    fully-populated information. This would require a custom renderer when
    using JSON or XML, but is a pretty easy solution.
</p>

<p>
    <strong>However, there's one cautionary tale to tell</strong>, something I 
    already mentioned: <code>OPTIONS</code>, per the specification, is 
    <em>non-cacheable</em>.  What this means is that everytime somebody makes an 
    <code>OPTIONS</code> request, any cache control headers you provide will be 
    ignored, which means hitting the server for each and every request to the 
    documentation.  Considering documentation is static, this is problematic; 
    it has even prompted <a href="http://www.mnot.net/blog/2012/10/29/NO_OPTIONS">blog 
    posts urging you not to use OPTIONS for documentation</a>.
</p>

<p>
    Which brings us to the second solution for end-user documentation: a static
    page referenced via a hypermedia link.
</p>

<p>
    This solution is insanely easy: you simply provide a <code>Link</code>
    header in your response, and provide a <code>describedby</code> reference
    pointing to the documentation page:
</p>

<div class="example"><pre><code class="language-http">
Link: &lt;http://example.com/api/documentation.md&gt;; rel="describedby"
</code></pre></div>

<p>
    With ZF2, this is trivially easy to accomplish: create a route and endpoint
    for your documentation, and then a listener on your controller that adds
    the <code>Link</code> header to your response.
</p>

<p>
    The latter, adding the link header, might look like this:
</p>

<div class="example"><pre><code class="language-php">
namespace My\Controller;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractRestfulController;

class FooController extends AbstractRestfulController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', array($this, 'injectLinkHeader'), 20);
    }

    public function injectLinkHeader($e)
    {
        $response = $e->getResponse();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Link', sprintf(
            '<%s>; rel="describedby"', 
            $this->url('documentation-route-name')
        ));
    }
}
</code></pre></div>

<p>
    If you want to ensure you get a fully qualified URL that includes the 
    schema, hostname, and port, there are a number of ways to do that as
    well; the above gives you the basic idea.
</p>

<p>
    Now, for the route and endpoint, there are tools that will help you
    simplify that task as well, in the form of a couple of ZF2 modules:
    <a href="https://github.com/weierophinney/PhlySimplePage">PhlySimplePage</a>
    and <a href="https://github.com/Soflomo/Prototype">Soflomo\Prototype</a>.
    <em>(Disclosure: I'm the author of PhlySimplePage.)</em>
</p>

<p>
    Both essentially allow you to specify a route and the corresponding
    template name to use, which means all you need to do is provide a little
    configuration, and a view template. <code>Soflomo\Prototype</code> has
    slightly simpler configuration, so I'll demonstrate it here:
</p>

<div class="example"><pre><code class="language-php">
return array(
    'soflomo_prototype' => array(
        'documentation-route-name' => array(
            'route'    => '/api/documentation',
            'template' => 'api/documentation',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'api/documentation' => __DIR__ . '/../view/api/documentation.phtml',
        ),
    ),
);
</code></pre></div>

<p>
    I personally have been using the <code>Link</code> header solution, as it's
    so simple to implement. It does <em>not</em> write the documentation for you,
    but thinking about it early and implementing it helps ensure you at least
    start writing the documentation, and, if you open source your project,
    you may find you have users who will write the documentation for you if
    they know where it lives.
</p>

<h2>Conclusions</h2>

<p>
    Document your API, or either nobody will use it, or all you're hear are
    complaints from your users about having to guess constantly about how to
    use it. Include the following information:
</p>

<ul>
    <li>What endpoint(s) is (are) available.</li>
    <li>Which operations are available for each endpoint.
        <ul>
            <li>What payloads are expected by the endpoint.</li>
            <li>What payloads can a user expect in return.</li>
            <li>What media types may be used for requests.</li>
            <li>What media types may be expected in responses.</li>
        </ul>
    </li>
</ul>

<p>
    Additionally, make sure that you do the <code>OPTIONS</code>/<code>Allow</code>
    dance; don't just accept any request method, and report the standard
    405 response for methods that you will not allow. Make sure you differentiate
    these for collections versus individual resources, as you likely may
    allow replacing or updating an individual resource, but likely will not
    want to do the same for a whole collection!
</p>

<h2>Next time</h2>

<p>
    So far, I've covered the basics of RESTful JSON APIS, specifically 
    recommending Hypermedia Application Language (HAL) for providing hypermedia
    linking and relations. I've covered error reporting, and provided two
    potential formats (API-Problem and vnd.error) for use with your APIs.
    Now, in this article, I've shown a bit about documenting your API both
    for machine consumption as well as end-users. What's left?
</p>

<p>
    In upcoming parts, I'll talk about ZF2's <code>AbstractRestfulController</code>
    in more detail, as well as how to perform some basic content negotiation.
    I've also had requests about how one might deal with API versioning, and will
    attempt to demonstrate some techniques for doing that as well. Finally,
    expect to see a post showing how I've tied all of this together in a 
    general-purpose ZF2 module so that you can ignore all of these posts and simply
    start writing APIs.
</p>

<h3>Updates</h3>

<p>
    <em>Note: I'll update this post with links to the other posts in the series 
    as I publish them.</em>
</p>

<ul>
    <li><a href="/blog/2013-02-11-restful-apis-with-zf2-part-1.html">Part 1</a></li>
    <li><a href="/blog/2013-02-13-restful-apis-with-zf2-part-2.html">Part 2</a></li>
</ul>

EOT;
$entry->setExtended($extended);

return $entry;
