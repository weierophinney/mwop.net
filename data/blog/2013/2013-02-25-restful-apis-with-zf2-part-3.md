---
id: 2013-02-25-restful-apis-with-zf2-part-3
author: matthew
title: 'RESTful APIs with ZF2, Part 3'
draft: false
public: true
created: '2013-02-25T06:29:00-06:00'
updated: '2013-02-25T06:29:00-06:00'
tags:
    - php
    - rest
    - http
    - zf2
    - 'zend framework'
---
In my [previous](/blog/2013-02-11-restful-apis-with-zf2-part-1.html)
[posts](/blog/2013-02-13-restful-apis-with-zf2-part-2.html), I covered basics
of JSON hypermedia APIs using Hypermedia Application Language (HAL), and
methods for reporting errors, including API-Problem and `vnd.error`.

In this post, I'll be covering *documenting* your API — techniques you can use
to indicate what HTTP operations are allowed, as well as convey the full
documentation on what endpoints are available, what they accept, and what you
can expect them to return.

While I will continue covering general aspects of RESTful APIs in this post, I
will also finally introduce several ZF2-specific techniques.

<!--- EXTENDED -->

Why Document?
-------------

If you're asking this question, you've either never consumed software, or your
software is perfect and self-documenting. I frankly don't believe either one.

In the case of APIs, those consuming the API need to know how to use it.

- What endpoints are available? Which operations are available for each endpoint?
- What does each endpoint expect as a payload during the request?
- What can you expect as a payload in return?
- How will errors be communicated?

While the promise of hypermedia APIs is that each response tells you the next
steps available, you still, somewhere along the way, need more information —
what payloads look like, which HTTP verbs should be used, and more. If you're
**not** documenting your API, you're "doing it wrong."

Where Should Documentation Live?
--------------------------------

This is the much bigger question.

Of the questions I raised above, detailing what should be documented, there are
two specific types. When discussing what operations are available, we have a
technical solution in the form of the `OPTIONS` method and its counterpart, the
`Allow` header. Everything else falls under end-user documentation.

OPTIONS
-------

The HTTP specification details the `OPTIONS` method as idempotent,
non-cacheable, and for use in detailing what operations are available for the
given resource specified by the request URI. It makes specific mention of the
`Allow` header, but does not limit what is returned for requests made via this
method.

The `Allow` header details the allowed HTTP methods for the given resource.

Used in combination, you make an `OPTIONS` request to a URI, and it should
return a response containing an `Allow` header; from that header value, you
then know what other HTTP methods can be made to that URI.

What this tells us is that our RESTful endpoint should do the following:

- When an `OPTIONS` request is made, return a response with an `Allow` header
  that has a list of the available HTTP methods allowed.
- For any HTTP method we do *not* allow, we should return a "405 Not Allowed"
  response.

These are fairly easy to accomplish in ZF2. *(See? I promised I'd get to some
ZF2 code in this post!)*

When creating RESTful endpoints in ZF2, I recommend using
`Zend\Mvc\Controller\AbstractRestfulController`. This controller contains an
`options()` method which you can use to respond to an `OPTIONS` request. As
with any ZF2 controller, returning a response object will prevent rendering and
bubble out immediately so that the response is returned.

```php
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
```

The next trick is returning the 405 response if an invalid option is used. For
this, you can create a listener in your controller, and wire it to listen at
higher-than-default priority. As an example:

```php
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
```

Note that I moved the allowed methods into properties; if I did the above, I'd
refactor the `options()` method to use those properties as well to ensure they
are kept in sync.

Also note that in the case of an invalid method, I return a response object.
This ensures that nothing else needs to execute in the controller; I discover
the problem and return early.

End-User Documentation
----------------------

Now that we have the technical solution out of the way, we're still left with
the bulk of the work left to accomplish: providing end-user documentation
detailing the various payloads, errors, etc.

I've seen two compelling approaches to this problem. The first builds on the
`OPTIONS` method, and the other uses a hypermedia link in every response to
point to documentation.

The `OPTIONS` solution is this: [use the body of an `OPTIONS` response to provide documentation](http://zacstewart.com/2012/04/14/http-options-method.html).
(Keith Casey [gave an excellent short presentation about this at REST Fest 2012](https://vimeo.com/49613738)).

The `OPTIONS` method allows for you to return a body in the response, and also
allows for content negotiation. The theory, then, is that you return
media-type-specific documentation that details the methods allowed, and what
they specifically accept in the body. While there is no standard for this at
this time, the first article I linked suggested including a description, the
parameters expected, and one or more example request bodies for each HTTP
method allowed; you'd likely also want to detail the responses that can be
expected.

```javascript
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
```

If you were to use this methodology, you would alter the `options()` method
such that it does not return a response object, but instead return a view model
with the documentation.

```php
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
```

I purposely didn't provide the implementations of the
`getResourceDocumentationSpec()` and `getCollectionDocumentationSpec()`
methods, as that will likely be highly specific to your application. Another
possibility is to use your view engine for this, and specify a template file
that has the fully-populated information. This would require a custom renderer
when using JSON or XML, but is a pretty easy solution.

**However, there's one cautionary tale to tell**, something I already
mentioned: `OPTIONS`, per the specification, is *non-cacheable*. What this
means is that everytime somebody makes an `OPTIONS` request, any cache control
headers you provide will be ignored, which means hitting the server for each
and every request to the documentation. Considering documentation is static,
this is problematic; it has even prompted [blog posts urging you not to use OPTIONS for documentation](http://www.mnot.net/blog/2012/10/29/NO_OPTIONS).

Which brings us to the second solution for end-user documentation: a static
page referenced via a hypermedia link.

This solution is insanely easy: you simply provide a `Link` header in your
response, and provide a `describedby` reference pointing to the documentation
page:

```http
Link: <http://example.com/api/documentation.md>; rel="describedby"
```

With ZF2, this is trivially easy to accomplish: create a route and endpoint for
your documentation, and then a listener on your controller that adds the `Link`
header to your response.

The latter, adding the link header, might look like this:

```php
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
```

If you want to ensure you get a fully qualified URL that includes the schema,
hostname, and port, there are a number of ways to do that as well; the above
gives you the basic idea.

Now, for the route and endpoint, there are tools that will help you simplify
that task as well, in the form of a couple of ZF2 modules:
[PhlySimplePage](https://github.com/weierophinney/PhlySimplePage) and
[Soflomo\\Prototype](https://github.com/Soflomo/Prototype). *(Disclosure: I'm
the author of PhlySimplePage.)*

Both essentially allow you to specify a route and the corresponding template
name to use, which means all you need to do is provide a little configuration,
and a view template. `Soflomo\Prototype` has slightly simpler configuration, so
I'll demonstrate it here:

```php
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
```

I personally have been using the `Link` header solution, as it's so simple to
implement. It does *not* write the documentation for you, but thinking about it
early and implementing it helps ensure you at least start writing the
documentation, and, if you open source your project, you may find you have
users who will write the documentation for you if they know where it lives.

Conclusions
-----------

Document your API, or either nobody will use it, or all you're hear are
complaints from your users about having to guess constantly about how to use
it. Include the following information:

- What endpoint(s) is (are) available.
- Which operations are available for each endpoint.
  - What payloads are expected by the endpoint.
  - What payloads can a user expect in return.
  - What media types may be used for requests.
  - What media types may be expected in responses.

Additionally, make sure that you do the `OPTIONS`/`Allow` dance; don't just
accept any request method, and report the standard 405 response for methods
that you will not allow. Make sure you differentiate these for collections
versus individual resources, as you likely may allow replacing or updating an
individual resource, but likely will not want to do the same for a whole
collection!

Next time
---------

So far, I've covered the basics of RESTful JSON APIS, specifically recommending
Hypermedia Application Language (HAL) for providing hypermedia linking and
relations. I've covered error reporting, and provided two potential formats
(API-Problem and vnd.error) for use with your APIs. Now, in this article, I've
shown a bit about documenting your API both for machine consumption as well as
end-users. What's left?

In upcoming parts, I'll talk about ZF2's `AbstractRestfulController` in more
detail, as well as how to perform some basic content negotiation. I've also had
requests about how one might deal with API versioning, and will attempt to
demonstrate some techniques for doing that as well. Finally, expect to see a
post showing how I've tied all of this together in a general-purpose ZF2 module
so that you can ignore all of these posts and simply start writing APIs.

### Updates

*Note: I'll update this post with links to the other posts in the series as I
publish them.*

- [Part 1](/blog/2013-02-11-restful-apis-with-zf2-part-1.html)
- [Part 2](/blog/2013-02-13-restful-apis-with-zf2-part-2.html)
