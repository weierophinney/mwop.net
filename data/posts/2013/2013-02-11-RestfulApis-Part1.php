<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2013-02-11-restful-apis-with-zf2-part-1');
$entry->setTitle('RESTful APIs with ZF2, Part 1');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2013-02-12 05:42', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2013-02-13 07:40', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'rest',
  'http',
  'zf2',
  'zend framework',
));

$body =<<<'EOT'
<p>
    RESTful APIs have been an interest of mine for a couple of years, but due
    to <a href="http://framework.zend.com/blog//zend-framework-2-0-0-stable-released.html">circumstances</a>,
    I've not had much chance to work with them in any meaningful fashion until
    recently.
</p>

<p>
    <a href="http://akrabat.com/">Rob Allen</a> and I proposed a workshop for
    <a href="http://conference.phpbenelux.eu/2013/">PHP Benelux 2013</a> 
    covering RESTful APIs with ZF2. When it was accepted, it gave me the perfect
    opportunity to dive in and start putting the various pieces together.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Background</h2>

<p>
    I've attended any number of conference sessions on API design, read 
    countless articles, and engaged in quite a number of conversations. Three
    facts keep cropping up:
</p>

<ol>
    <li>JSON is fast becoming the preferred exchange format due to the ease 
    with which it de/serializes in almost every language.</li>
    <li>The "holy grail" is <a 
    href="http://martinfowler.com/articles/richardsonMaturityModel.html">Richardson 
    Maturity Model</a> Level 3.</li>
    <li>It's really hard to achieve RMM level 3 with JSON.</li>
</ol>

<h3>Richardson Maturity Model</h3>

<p>
    As a quick review, the Richardson Maturity Model has the following 4 levels:
</p>

<ul>
    <li>Level 0: "The swamp of POX." Basically, a service that uses TCP for 
        transport, primarily as a form of remote procedure call (RPC). 
        Typically, these are not really leveraging HTTP in any meaningful 
        fashion; most systems will use HTTP POST for all interactions. Also, 
        you will often have a single endpoint for all interactions, regardless 
        of whether or not they are strictly related. XML-RPC, SOAP, and 
        JSON-RPC fall under this category.
    </li>

    <li>Level 1: "Resources." In these services, you start breaking the service
        into multiple services, one per "resource," or, in object oriented terms,
        per object. This means a distinct URL per object, which means each
        has its own distinct identity on the web; this often extends not only 
        to the collection of objects, but to individual objects under the 
        collection as well (e.g., "/books" as well as "/books/life-of-pi"). The 
        service may still be RPC in nature, however, and, at this level, often 
        is still using a single HTTP method for all interactions with the 
        resource.
    </li>

    <li>Level 2: "HTTP Verbs." At this level, we start using HTTP verbs with
        our services in the way the HTTP specification intends. GET is for safe 
        operations, and should be cacheable; POST is used for creation and/or 
        updating; DELETE can be used to delete a resource; etc. Rather than 
        doing RPC style methods, we leverage HTTP, occasionally passing 
        additional parameters via the query string or request body.  
        Considerations such as HTTP caching and idempotence are taken into 
        account.
    </li>

    <li>Level 3: "Hypermedia Controls." Building on the previous level, our
        resource representations now also include <em>links</em>, which indicate
        what we can <em>do next</em>. At this level, our API becomes practically
        self-describing; given a single end-point, we should be able to start
        crawling it, using the links in a representation to lead us to the next
        actions.
    </li>
</ul>

<p>
    When I first started playing with web services around a decade ago, 
    everything was stuck at Level 0 or Level 1 -- usually with Level 1 users 
    downgrading to Level 0 because Level 0 offerred consistency and 
    predictability if you chose to use a service type that had a defined 
    envelope format (such as XML-RPC or SOAP).  (I even wrote the XML-RPC 
    server implementation for Zend Framework because I got sick of writing 
    one-off parsers/serializers for custom XML web service implementations.
    When you're implementing many services, predictability is a huge win.)
</p>

<p>
    A few years ago, I started seeing a trend towards Level 2. Web developers
    like the simplicity of using HTTP verbs, as they map very well to <a 
    href="http://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a>
    operations -- the bread and butter of web development. Couple this concept
    with JSON, and it becomes trivially simple to both create a web service, as
    well as consume it.
</p>

<p>
    <em>I'd argue that the majority of web developers are quite happy to be at 
    Level 2 -- and have no problem staying there. They're productive, and the 
    concepts are easy -- both to understand and to implement.</em>
</p>

<p>
    Level 3, though, is where it becomes really interesting. The idea that
    I can examine the represention <em>alone</em> in order to understand what
    I can do next is very intriguing and empowering.
</p>

<h3>JSON and Hypermedia</h3>

<p>
    With XML, hypermedia basically comes for free. Add some &lt;link&gt; 
    elements to your representation, and you're done -- and don't forget the 
    link <code>rel</code>ations!
</p>

<p>
    JSON, however, is another story.
</p>

<p>
    Where do the links go? <em>There is no single, defined way to represent a 
    hyperlink in JSON.</em>
</p>

<p>
    Fortunately, there are some emerging standards. 
</p>

<p>
    First is use of the <a href="http://www.w3.org/wiki/LinkHeader">"Link" HTTP 
    header</a>. While the page I linked shows only a single link in the
    header, you can have multiple links separated by commas. GitHub uses this
    when providing pagination links in their API. Critics will point out that
    the HTTP headers are not technically part of the representation, however;
    strict interpetations of REST and RMM indicate that the hypermedia links
    should be part of the resource representation. Regardless, having the links
    in the HTTP headers is useful for pre-traversal of a service, as you can
    perform HEAD requests only to discover possible actions and workflows.
</p>

<p>
    <a 
    href="http://amundsen.com/media-types/collection/format/">Collection+JSON</a>
    is interesting, as it describes the entire JSON envelope. My one criticism
    is that it details too much; whenever I see a format that dictates how to 
    describe types, I think of XML-RPC or SOAP, and get a little twitchy. It's
    definitely worth a look, though.
</p>

<p>
    What's captured my attention of late, however, is 
    <a href="http://stateless.co/hal_specification.html">Hypertext Application Language</a>,
    or HAL for short. HAL has very few rules, but succinctly describes both how
    to provide hypermedia in JSON as well as how to represent embedded resources
    - the two things that most need standardized structure in JSON. It does this
    while still providing a generic media type, and also describing a mirror 
    image XML format!
</p>

<h3>HAL Media Types</h3>

<p>
    HAL defines two generic media types: <code>application/hal+xml</code> and
    <code>application/hal+json</code>. You will use these as the response
    Content-Type, as they describe the response representation; the client
    can simply request <code>application/json</code>, and the response
    format remains compatible.
</p>

<h3>HAL and Links</h3>

<p>
    HAL provides a very simple structure for JSON hypermedia links. First, all 
    resource representations must contain hypermedia links, and all links are 
    provided in a "_links" object:
</p>

<div class="example"><pre><code class="language-javascript">
{
    "_links": {
    }
}
</code></pre></div>

<p>
    Second, links are properties of this object. The property name is the link
    relation, and the value is an object containing minimally an "href" 
    property.
</p>

<div class="example"><pre><code class="language-javascript">
{
    "_links": {
        "self": {"href": "http://example.com/api/status/1234"}
    }
}
</code></pre></div>

<p>
    If a given relation can have multiple links, you provide instead an array
    of objects:
</p>

<div class="example"><pre><code class="language-javascript">
{
    "_links": {
        "self": {"href": "http://example.com/api/status/1234"},
        "conversation": [
            {"href": "http://example.com/api/status/1237"},
            {"href": "http://example.com/api/status/1241"}
        ]
    }
}
</code></pre></div>

<p>
    Individual links can contain other attributes as desired -- I've seen
    people include the relation again so that it's self-contained in the link 
    object, and it's not uncommon to include a title or name.
</p>

<h3>HAL and Resources</h3>

<p>
    HAL imposes no structure over resources other than requiring the hypermedia 
    links; even then, you typically do not include the hypermedia links when 
    making a request of the web service; the hypermedia links are included only 
    in the representations <em>returned</em> by the service.
</p>

<p>
    So, as an example, you would POST the following:
</p>

<div class="example"><pre><code class="language-http">
POST /api/status
Host: example.com
Accept: application/json
Content-Type: application/json

{
    "status": "This is my awesome status update!",
    "user": "mwop"
}
</code></pre></div>

<p>
    And from that request, you'd receive the following:
</p>

<div class="example"><pre><code class="language-http">
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
</code></pre></div>

<h3>HAL and Embedded Resources</h3>

<p>
    The other important thing that HAL defines is how to <em>embed</em>
    resources. Why is this important? If the resource references other
    resources, you will want to be able to link to them so you can perform
    operations on them, too.
</p>

<p>
    Embedded resources are represented inside an "_embedded" object of the
    representation, and, as resources, contain their own "_links" object as 
    well. Each resource you embed is assigned to a property of that
    object, and if multiple objects of the same type are returned, an array
    of resources is assigned. In fact, this latter is how you represent
    <em>collections</em> in HAL.
</p>

<p>
    Let's consider a simple example first. In previous code samples, I have
    a "user" that's a string; let's make that an embedded resource instead.
</p>

<div class="example"><pre><code class="language-javascript">
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
</code></pre></div>

<p>
    I've moved the "user" out of the representation, and into the "_embedded"
    object -- because this is where you define embedded resources. Note that
    the "user" is a standard HAL resource itself -- containing hypermedia links.
</p>

<p>
    Now let's look at a collection:
</p>

<div class="example"><pre><code class="language-javascript">
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
</code></pre></div>

<p>
    Note that the "status" property is an array; semantically, all resources
    under this key are of the same type. Also note that the parent resource
    has some additional link relations -- these are related to pagination, and
    allow a client to determine what the next and last pages are (and, if we 
    were midway into the collection, previous and first pages). Since the 
    collection is also a resource, it has some interesting metadata -- how
    many resources are in the collection, how many we represent per page, and
    what the current page is.
</p>

<p>
    Also note that you can nest resources -- simply include an "_embedded" 
    object inside an embedded resource, with additional resources, as I've
    done with the "user" resource inside the status resource shown here. It's 
    turtles all the way down.
</p>

<h2>Next Time</h2>

<p>
    The title of this post indicates I'll be talking about building RESTful 
    APIs with ZF2 -- but so far, I've not said anything about ZF2.
</p>

<p>
    I'll get there. But there's another detour to take: reporting errors.
</p>

<h3>Updates</h3>

<p>
    <em>Note: I'll update this post with links to the other posts in the series 
    as I publish them.</em>
</p>

<ul>
    <li><a href="/blog/2013-02-13-restful-apis-with-zf2-part-2.html">Part 2</a></li>
    <li><a href="/blog/2013-02-25-restful-apis-with-zf2-part-3.html">Part 3</a></li>
</ul>

EOT;
$entry->setExtended($extended);

return $entry;
