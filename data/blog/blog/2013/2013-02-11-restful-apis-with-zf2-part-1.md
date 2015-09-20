---
id: 2013-02-11-restful-apis-with-zf2-part-1
author: matthew
title: 'RESTful APIs with ZF2, Part 1'
draft: false
public: true
created: '2013-02-12T05:42:00-06:00'
updated: '2013-02-13T07:40:00-06:00'
tags:
    - php
    - rest
    - http
    - zf2
    - 'zend framework'
---
RESTful APIs have been an interest of mine for a couple of years, but due to
[circumstances](http://framework.zend.com/blog//zend-framework-2-0-0-stable-released.html),
I've not had much chance to work with them in any meaningful fashion until
recently.

[Rob Allen](http://akrabat.com/) and I proposed a workshop for
[PHP Benelux 2013](http://conference.phpbenelux.eu/2013/) covering RESTful APIs
with ZF2.  When it was accepted, it gave me the perfect opportunity to dive in
and start putting the various pieces together.

<!--- EXTENDED -->

Background
----------

I've attended any number of conference sessions on API design, read countless
articles, and engaged in quite a number of conversations. Three facts keep
cropping up:

1. JSON is fast becoming the preferred exchange format due to the ease with
   which it de/serializes in almost every language.
2. The "holy grail" is [Richardson Maturity Model](http://martinfowler.com/articles/richardsonMaturityModel.html) Level 3.
3. It's really hard to achieve RMM level 3 with JSON.

### Richardson Maturity Model

As a quick review, the Richardson Maturity Model has the following 4 levels:

- Level 0: "The swamp of POX." Basically, a service that uses TCP for
  transport, primarily as a form of remote procedure call (RPC). Typically,
  these are not really leveraging HTTP in any meaningful fashion; most systems
  will use HTTP POST for all interactions. Also, you will often have a single
  endpoint for all interactions, regardless of whether or not they are strictly
  related. XML-RPC, SOAP, and JSON-RPC fall under this category.
- Level 1: "Resources." In these services, you start breaking the service into
  multiple services, one per "resource," or, in object oriented terms, per
  object. This means a distinct URL per object, which means each has its own
  distinct identity on the web; this often extends not only to the collection
  of objects, but to individual objects under the collection as well (e.g.,
  `/books` as well as `/books/life-of-pi`). The service may still be RPC in
  nature, however, and, at this level, often is still using a single HTTP
  method for all interactions with the resource.
- Level 2: "HTTP Verbs." At this level, we start using HTTP verbs with our
  services in the way the HTTP specification intends. GET is for safe
  operations, and should be cacheable; POST is used for creation and/or
  updating; DELETE can be used to delete a resource; etc. Rather than doing RPC
  style methods, we leverage HTTP, occasionally passing additional parameters
  via the query string or request body. Considerations such as HTTP caching and
  idempotence are taken into account.
- Level 3: "Hypermedia Controls." Building on the previous level, our resource
  representations now also include *links*, which indicate what we can *do
  next*. At this level, our API becomes practically self-describing; given a
  single end-point, we should be able to start crawling it, using the links in
  a representation to lead us to the next actions.

When I first started playing with web services around a decade ago, everything
was stuck at Level 0 or Level 1 — usually with Level 1 users downgrading to
Level 0 because Level 0 offerred consistency and predictability if you chose to
use a service type that had a defined envelope format (such as XML-RPC or
SOAP). (I even wrote the XML-RPC server implementation for Zend Framework
because I got sick of writing one-off parsers/serializers for custom XML web
service implementations. When you're implementing many services, predictability
is a huge win.)

A few years ago, I started seeing a trend towards Level 2. Web developers like
the simplicity of using HTTP verbs, as they map very well to
[CRUD](http://en.wikipedia.org/wiki/Create,_read,_update_and_delete) operations
— the bread and butter of web development. Couple this concept with JSON, and
it becomes trivially simple to both create a web service, as well as consume
it.

*I'd argue that the majority of web developers are quite happy to be at Level 2
— and have no problem staying there. They're productive, and the concepts are
easy — both to understand and to implement.*

Level 3, though, is where it becomes really interesting. The idea that I can
examine the represention *alone* in order to understand what I can do next is
very intriguing and empowering.

### JSON and Hypermedia

With XML, hypermedia basically comes for free. Add some `<link>` elements to
your representation, and you're done — and don't forget the link `rel`ations!

JSON, however, is another story.

Where do the links go? *There is no single, defined way to represent a
hyperlink in JSON.*

Fortunately, there are some emerging standards.

First is use of the ["Link" HTTP header](http://www.w3.org/wiki/LinkHeader).
While the page I linked shows only a single link in the header, you can have
multiple links separated by commas. GitHub uses this when providing pagination
links in their API. Critics will point out that the HTTP headers are not
technically part of the representation, however; strict interpetations of REST
and RMM indicate that the hypermedia links should be part of the resource
representation. Regardless, having the links in the HTTP headers is useful for
pre-traversal of a service, as you can perform HEAD requests only to discover
possible actions and workflows.

[Collection+JSON](http://amundsen.com/media-types/collection/format/) is
interesting, as it describes the entire JSON envelope. My one criticism is that
it details too much; whenever I see a format that dictates how to describe
types, I think of XML-RPC or SOAP, and get a little twitchy. It's definitely
worth a look, though.

What's captured my attention of late, however, is
[Hypertext Application Language](http://stateless.co/hal_specification.html),
or HAL for short. HAL has very few rules, but succinctly describes both how to
provide hypermedia in JSON as well as how to represent embedded resources — the
two things that most need standardized structure in JSON. It does this while
still providing a generic media type, and also describing a mirror image XML
format!

### HAL Media Types

HAL defines two generic media types: `application/hal+xml` and
`application/hal+json`. You will use these as the response `Content-Type`, as
they describe the response representation; the client can simply request
`application/json`, and the response format remains compatible.

### HAL and Links

HAL provides a very simple structure for JSON hypermedia links. First, all
resource representations must contain hypermedia links, and all links are
provided in a `_links` object:

```javascript
{
    "_links": {
    }
}
```

Second, links are properties of this object. The property name is the link
relation, and the value is an object containing minimally an "href" property.

```javascript
{
    "_links": {
        "self": {"href": "http://example.com/api/status/1234"}
    }
}
```

If a given relation can have multiple links, you provide instead an array of objects:

```javascript
{
    "_links": {
        "self": {"href": "http://example.com/api/status/1234"},
        "conversation": [
            {"href": "http://example.com/api/status/1237"},
            {"href": "http://example.com/api/status/1241"}
        ]
    }
}
```

Individual links can contain other attributes as desired — I've seen people
include the relation again so that it's self-contained in the link object, and
it's not uncommon to include a title or name.

### HAL and Resources

HAL imposes no structure over resources other than requiring the hypermedia
links; even then, you typically do not include the hypermedia links when making
a request of the web service; the hypermedia links are included only in the
representations *returned* by the service.

So, as an example, you would POST the following:

```http
POST /api/status
Host: example.com
Accept: application/json
Content-Type: application/json

{
    "status": "This is my awesome status update!",
    "user": "mwop"
}
```

And from that request, you'd receive the following:

```http
201 Created
Location: http://example.com/api/status/1347
Content-Type: application/hal+json

{
    "_links": {
        "self": {"href": "http://example.com/api/status/1347"}
    },
    "id": "1347",
    "timestamp": "2013-02-11 23:33:47",
    "status": "This is my awesome status update!",
    "user": "mwop"
}
```

### HAL and Embedded Resources

The other important thing that HAL defines is how to *embed* resources. Why is
this important? If the resource references other resources, you will want to be
able to link to them so you can perform operations on them, too.

Embedded resources are represented inside an `_embedded` object of the
representation, and, as resources, contain their own `_links` object as well.
Each resource you embed is assigned to a property of that object, and if
multiple objects of the same type are returned, an array of resources is
assigned. In fact, this latter is how you represent *collections* in HAL.

Let's consider a simple example first. In previous code samples, I have a
"user" that's a string; let's make that an embedded resource instead.

```javascript
{
    "_links": {
        "self": {"href": "http://example.com/api/status/1347"}
    },
    "id": "1347",
    "timestamp": "2013-02-11 23:33:47",
    "status": "This is my awesome status update!",
    "_embedded": {
        "user": {
            "_links": {
                "self": {"href": "http://example.com/api/user/mwop"}
            }
            "id": "mwop",
            "name": "Matthew Weier O'Phinney",
            "url": "http://mwop.net"
        }
    }
}
```

I've moved the "user" out of the representation, and into the `_embedded`
object — because this is where you define embedded resources. Note that the
"user" is a standard HAL resource itself — containing hypermedia links.

Now let's look at a collection:

```javascript
{
    "_links": {
        "self": {"href": "http://example.com/api/status"},
        "next": {"href": "http://example.com/api/status?page=2"},
        "last": {"href": "http://example.com/api/status?page=100"}
    },
    "count": 2973,
    "per_page": 30,
    "page": 1,
    "_embedded": {
        "status": [
            {
                "_links": {
                    "self": {"href": "http://example.com/api/status/1347"}
                },
                "id": "1347",
                "timestamp": "2013-02-11 23:33:47",
                "status": "This is my awesome status update!",
                "_embedded": {
                    "user": {
                        "_links": {
                            "self": {"href": "http://example.com/api/user/mwop"}
                        }
                        "id": "mwop",
                        "name": "Matthew Weier O'Phinney",
                        "url": "http://mwop.net"
                    }
                }
            }
            /* ... */
        ]
    }
}
```

Note that the "status" property is an array; semantically, all resources under
this key are of the same type. Also note that the parent resource has some
additional link relations — these are related to pagination, and allow a
client to determine what the next and last pages are (and, if we were midway
into the collection, previous and first pages). Since the collection is also a
resource, it has some interesting metadata — how many resources are in the
collection, how many we represent per page, and what the current page is.

Also note that you can nest resources — simply include an `_embedded` object
inside an embedded resource, with additional resources, as I've done with the
"user" resource inside the status resource shown here. It's turtles all the way
down.

Next Time
---------

The title of this post indicates I'll be talking about building RESTful APIs
with ZF2 — but so far, I've not said anything about ZF2.

I'll get there. But there's another detour to take: reporting errors.

### Updates

*Note: I'll update this post with links to the other posts in the series as I
publish them.*

- [Part 2](/blog/2013-02-13-restful-apis-with-zf2-part-2.html)
- [Part 3](/blog/2013-02-25-restful-apis-with-zf2-part-3.html)
