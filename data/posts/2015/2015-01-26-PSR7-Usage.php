<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-01-26-psr-7-by-example');
$entry->setTitle('PSR-7 By Example');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-01-26 09:20', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-01-26 09:20', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'http',
  'php',
  'programming',
));

$body =<<<'EOT'
<p>
    <a href="https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md">PSR-7</a>
    is shaping up nicely. I pushed some updates earlier this week, and we 
    tagged 0.6.0 of the <a href="https://github.com/php-fig/http-message">http-message package</a> 
    last week for implementors and potential users to start coding against.
</p>

<p>
    I'm still hearing some grumbles both of &quot;simplify!&quot; <em>and</em> 
    &quot;not far enough!&quot; so I'm writing this posts to demonstrate usage of 
    the currently published interfaces, and to illustrate both the ease of use and 
    the completeness and robustness they offer.
</p>

<p>First, though I want to clarify what PSR-7 is attempting.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>HTTP Messages</h2>

<p>
    HTTP messages are relatively simple, which is why the protocol has 
    succeeded over the years. All messages have the following structure:
</p>

<pre><code class="lang-http">
&lt;message line&gt;
Header: value
Another-Header: value

Message body
</code></pre>

<p>
    Headers are key/value pairs. The keys are case insensitive. Values are 
    strings. The same header type may be emitted multiple times, in which case 
    (typically) the values are considered as a list; in most cases, these can 
    also be expressed by concatenating the values with comma delimiters.
</p>

<p>
    The message body is a string, but typically handled by servers and clients 
    as a stream in order to conserve memory and processing overhead. This is 
    incredibly important when you transmit large data sets, and particularly 
    when transmitting files. As an example, PHP natively represents the 
    incoming request body as the stream <code>php://input</code>, and uses 
    output buffers — a form of stream — to return a response.
</p>

<p>The message line is what differentiates a request from a response.</p>

<p>The message line of a request is called the request line, and has the following format:</p>

<pre><code class="lang-http">
METHOD request-target HTTP/VERSION
</code></pre>

<p>
    <code>METHOD</code> indicates the operation requested: GET, POST, PUT, 
    PATCH, DELETE, OPTIONS, HEAD, etc. The <code>VERSION</code> is typically 
    1.0 or 1.1 (usually 1.1 in modern web clients). The 
    <code>request-target</code> is where things get complex.
</p>

<p>A request target can be one of four different forms:</p>

<ul>
    <li><code>origin-form</code>, which is the path and query string (if present) of the URI. </li>
    <li><code>absolute-form</code>, which is an absolute URI. </li>
    <li><code>authority-form</code>, which is the authority portion of the uri 
        (<code>user-info</code>, if  present; <code>host</code>; and 
        <code>port</code>, if non-standard). </li>
    <li><code>asterisk-form</code>, which is the string <code>*</code>.</li>
</ul>

<p>
    Typically, an HTTP client will use the scheme and authority from a URI to 
    make the connection to the HTTP server, and then pass an origin-form target 
    in the transmitted HTTP request message. However, it's perfectly valid to 
    send the absolute URI as well. authority-form is typically only used with 
    CONNECT requests, which are usually performed when working with a proxy 
    server. asterisk-form is used with OPTIONS requests to get general 
    capabilities of a web server.
</p>

<p>In short, there's a lot of moving parts in the request-target.</p>

<p>Now, to make things more complicated, when we look at URIs, we have the following:</p>

<pre><code class="lang-http">
&lt;scheme&gt;://&lt;authority&gt;[/&lt;path&gt;][?&lt;query string&gt;]
</code></pre>

<p>
    The scheme, when doing HTTP requests, will be one of <code>http</code> or 
    <code>https</code>. The path is a well-known format as well. But what about 
    authority?
</p>

<pre><code class="lang-http">[user-info@]host[:port]</code></pre>

<p>
    The authority <em>always</em> contains the host, which can be a domain name 
    or an IP address. The port is optional, and only needs to be included if 
    it's non-standard for the current scheme (or if the scheme is unknown). 
    user-info is of the form:
</p>

<pre><code class="lang-http">user[:pass]</code></pre>

<p>
    where password is optional. In fact, in current specifications, the 
    recommendation is to never include the password in a URI, to force prompting 
    for the value by the client.
</p>

<p>The query string is a set of key-value pairs delimited by ampersands:</p>

<pre><code class="lang-http">?foo=bar&amp;baz&amp;quz=1</code></pre>

<p>Depending on the language implementation, it can also model lists and hashes:</p>

<pre><code class="lang-http">?sort[]=ASC&amp;sort[]=date&amp;filter[product]=name</code></pre>

<p>PHP will parse the above to:</p>

<pre><code class="lang-php">
[
    &#39;sort&#39; =&gt; [
        &#39;ASC&#39;,
        &#39;date&#39;
    ],
    &#39;filter&#39; =&gt; [
        &#39;product&#39; =&gt; &#39;name&#39;
    ],
]
</code></pre>

<p>So, as if the request-target was not complex enough, URIs also present a fair amount of moving parts!</p>

<p>Fortunately, responses are simpler. The response line looks like this:</p>

<pre><code class="lang-http">HTTP/VERSION &lt;status&gt;[ &lt;reason&gt;]</code></pre>

<p>
    The <code>VERSION</code> is as stated earlier — usually 1.0 or 1.1, and 
    typically 1.1. The <code>status</code> code is an integer from 100—599 
    inclusive; usually the <code>reason</code> phrase will be standard for a given 
    status code.
</p>

<p>That's the birds-eye view of HTTP messages. Let's look at how PSR-7 currently models them.</p>

<h2>Message Headers</h2>

<p>
    Message header names are case insensitive. Unfortunately, most languages 
    and libraries do some sort of normalization that makes consumption difficult. 
    As an example, PHP has them in <code>$_SERVER</code> as all-caps, prefixed with 
    <code>HTTP_</code>, and substituting <code>_</code> for <code>-</code> (this is 
    to conform with the <a href="http://www.w3.org/CGI/">Common Gateway 
    Interface</a> (CGI) specification).
</p>

<p>PSR-7 simplifies access to the headers by providing an object-oriented layer on top of them:</p>

<pre><code class="lang-php">
// Returns null if not found:
$header = $message-&gt;getHeader(&#39;Accept&#39;);

// Test for a header:
if (! $message-&gt;hasHeader(&#39;Accept&#39;)) {
}

// If the header has multiple values, fetch them
// as an array:
$values = $message-&gt;getHeaderLines(&#39;X-Foo&#39;)/
</code></pre>

<p>
    All of the above work regardless of the case you specify for the header; 
    <code>accept</code>, <code>ACCEPT</code>, or even <code>aCCePt</code> would all 
    have been valid header names and received the same result.
</p>

<p>PSR-7 stipulates that fetching all headers will return a structure in the form:</p>

<pre><code class="lang-php">
/* Returns the following structure:
    [
        &#39;Header&#39; =&gt; [
            &#39;value1&#39;
            &#39;value2&#39;
        ]
    ]
 */
foreach ($message-&gt;getAllHeaders() as $header =&gt; $values) {
}
</code></pre>

<p>
    By specifying the structure to return, consumers know exactly what to 
    expect, and can process headers in a uniform manner -- regardless of the 
    implementation.
</p>

<p>
    But what about when you want to add headers to a message — for instance, to 
    create a request to pass to an HTTP client?
</p>

<p>
    The messages in PSR-7 are modeled as <a 
    href="http://en.wikipedia.org/wiki/Value_object">value objects</a>; this means 
    that any change to state is essentially a different value. So, assigning a 
    header will result in a new message instance:
</p>

<pre><code class="lang-php">
$new = $message-&gt;withHeader(&#39;Location&#39;, &#39;http://example.com&#39;);
</code></pre>

<p>If you are only interested in the updated value, you can just re-assign it:</p>

<pre><code class="lang-php">
$message = $message-&gt;withHeader(&#39;Location&#39;, &#39;http://example.com&#39;);
</code></pre>

<p>If you want to append another value to a header that may already be present, you can also do that:</p>

<pre><code class="lang-php">
$message = $message-&gt;withAddedHeader(&#39;X-Foo&#39;, &#39;bar&#39;);
</code></pre>

<p>Or even remove a header:</p>

<pre><code class="lang-php">$message = $message-&gt;withoutHeader(&#39;X-Foo&#39;);
</code></pre>

<h2>Message Bodies</h2>

<p>
    As noted above, message bodies are usually treated as streams for 
    performance reasons. This is particularly important when you're transmitting 
    files over HTTP, as you don't want to use up all available memory to your 
    current process. Most HTTP message implementations I've surveyed forget this or 
    try to hack it on after-the-fact (yes, even ZF2 is guilty of this!). If you 
    need more convincing, or just more background on why this is a good idea, <a 
    href="http://mtdowling.com/blog/2014/07/03/a-case-for-higher-level-php-streams/">Michael 
    Dowling blogged about the rationale to use streams in PSR-7</a> last summer.
</p>

<p>
    Accordingly, message bodies in PSR-7 are modeled as <a 
    href="https://github.com/php-fig/http-message/blog/master/src/StreamableInterface.php">streams</a>.
</p>

<p>
    &quot;But that's too hard for the 80% use case of using a string!&quot; is 
    the most common argument we hear on the list about this aspect of the 
    proposal. Well, then, consider this:
</p>

<pre><code class="lang-php">
$body = new Stream(&#39;php://temp&#39;);
$body-&gt;write(&#39;Here&#39;s the content for my message!&#39;);
</code></pre>

<blockquote><p>
    The above example, and all concrete examples of messages in this post will 
    be using <a href="https://github.com/phly/http">phly/http</a>, a library 
    I've written that tracks the progress of PSR_7. In this case, 
    <code>Stream</code> implements <code>StreamableInterface</code>.
</p></blockquote>

<p>
    Essentially, you get a slim, object oriented interface to the body that 
    allows you to append to it, read it, and more. Want to replace it? Create a 
    new message body and update your HTTP message:
</p>

<pre><code class="lang-php">
$message = $message-&gt;withBody(new Stream(&#39;php://temp&#39;));
</code></pre>

<p>
    My point is that while the concept of streams may be hard to wrap your head 
    around, the actual implementation and usage is not.
</p>

<p>
    One benefit to having the <code>StreamableInterface</code> in PSR-7 is that 
    it provides flexibility for a number of different patterns. As an example, 
    you could create a &quot;callback&quot; implementation that on a 
    <code>read()</code> or <code>getContents()</code> operation delegates to a 
    callback to return the message content (Drupal, in particular, uses this 
    pattern). Or an &quot;iterator&quot; implementation that uses any 
    <code>Traversable</code> to return and/or aggregate content. The point is, 
    you can get creative with the interface in order to accomplish a variety of 
    different patterns for modeling the message body, and you are not 
    restricted to simply strings or files.
</p>

<p>
    The <code>StreamableInterface</code> exposes the subset of stream 
    operations that will be of most use with HTTP message bodies; it is by no 
    means comprehensive, but it covers a large set of possible operations.
</p>

<p>
    I'm personally a fan of using <code>php://temp</code> streams, as they are 
    in-memory unless they grow too big — at which point they write to temp files on 
    disk. The approach can be quite performant.
</p>

<h2>Responses</h2>

<p>So far, I've looked at features common to any message. I'm now going to look at responses in particular.</p>

<p>A response has a status code and a reason phrase:</p>

<pre><code class="lang-php">
$status = $response-&gt;getStatusCode();
$reason = $response-&gt;getReasonPhrase();
</code></pre>

<p>That's pretty easy to remember. Now, what if I'm <em>building</em> a response?</p>

<p>
    Reason phrases are considered optional — but also specific to the status 
    code being set. As such, the only response-specific mutator is 
    <code>withStatus()</code>:
</p>

<pre><code class="lang-php">
$response = $response-&gt;withStatus(418, &quot;I&#39;m a teapot&quot;);
</code></pre>

<blockquote><p>
    Again, messages are modeled as value objects; a change to any value results 
    in a new instance, which needs to be assigned. In most cases, you'll just 
    reassign the current instance.
</p></blockquote>

<h2>Requests</h2>

<p>Requests contain the following:</p>

<ul>
    <li>Method. </li>
    <li>URI/request-target.</li>
</ul>

<p>
    The latter provides a bit of a challenge to model. In likely 99% of use 
    cases, we'll be seeing either an origin-form or an absolute-form 
    request-target -- in other words, something that looks like a URI. As such, 
    the request interface uses the verbiage &quot;URI&quot; — but the object it 
    composes is a <code>UriTargetInterface</code>, and models any 
    request-target.
</p>

<p>Let's get the method and URI from the request:</p>

<pre><code class="lang-php">
$method = $request-&gt;getMethod();
$uri    = $request-&gt;getUri();
</code></pre>

<p>
    <code>$uri</code> in this case will be an instance of the 
    <code>UriTargetInterface</code>, and allows you to introspect the 
    request-target:
</p>

<pre><code class="lang-php">
// Tests:
$uri-&gt;isOrigin();
$uri-&gt;isAbsolute();
$uri-&gt;isAuthority();
$uri-&gt;isAsterisk();

// URI parts:
$scheme    = $uri-&gt;getScheme();
$userInfo  = $uri-&gt;getUserInfo();
$host      = $uri-&gt;getHost();
$port      = $uri-&gt;getPort();
$path      = $uri-&gt;getPath();
$query     = $uri-&gt;getQuery();     // the query STRING
$authority = $uri-&gt;getAuthority(); // [user-info@]host[:port]
</code></pre>

<p>
    Just like the HTTP messages, URIs are treated as value objects, as changing 
    any portion of a URI changes its value; as such, mutator operations return 
    a new instance:
</p>

<pre><code class="lang-php">
$uri = $uri
    -&gt;withScheme(&#39;http&#39;)
    -&gt;withHost(&#39;example.com&#39;)
    -&gt;withPath(&#39;/foo/bar&#39;)
    -&gt;withQuery(&#39;?baz=bat&#39;);
</code></pre>

<p>
    Because changing the URI means a new instance, if you want the changes 
    reflected in your request, you'll need to notify the request; and, as with 
    any message, if you need to change the method or URI in your request 
    instance, use the <code>with</code> methods:
</p>

<pre><code class="lang-php">
$request = $request
    -&gt;withMethod(&#39;POST&#39;)
    -&gt;withUri($uri-&gt;withPath(&#39;/api/user&#39;));
</code></pre>

<h2>Server-Side requests</h2>

<p>
    Server-side requests have some slightly different concerns than a standard 
    HTTP request message. PHP's Server API (SAPI) does a number of things for us 
    normally that, as PHP developers, we've come to rely on:
</p>

<ul>
    <li>Deserialization of query string arguments (<code>$_GET</code>). </li>
    <li>Deserialization of urlencoded form data submitted via POST (<code>$_POST</code>). </li>
    <li>Deserialization of cookies (<code>$_COOKIE</code>). </li>
    <li>Identification and handling of file uploads (<code>$_FILES</code>). </li>
    <li>Encapsulation of CGI/SAPI parameters (<code>$_SERVER</code>).</li>
</ul>

<p>
    Query string arguments, form data, and cookies can be discovered from other 
    aspects of the request, but it's convenient to have them already parsed for us. 
    That said, there are cases where we may want to manipulate those values:
</p>

<ul>
    <li>For APIs, the data may be in XML or JSON, and may be submitted over 
        methods  other than POST. As such, we'll need to deserialize the data — and 
        then re-inject it into the request.</li>
    <li>Many frameworks are now encrypting cookies — which means that they need 
        to be decrypted, and re-injected into the request.</li>
</ul>

<p>
    So, PSR-7 offers another interface, <code>ServerRequestInterface</code>, 
    which extends the base <code>RequestInterface</code>, and offers features 
    around these values:
</p>

<pre><code class="lang-php">
$query   = $request-&gt;getQueryParams();
$body    = $request-&gt;getBodyParams();
$cookies = $request-&gt;getCookieParams();
$files   = $request-&gt;getFileParams();
$server  = $request-&gt;getServerParams();
</code></pre>

<p>
    Let's say you are writing an API, and want to accept JSON requests; doing 
so might look like the following:
</p>

<pre><code class="lang-php">
$accept = $request-&gt;getHeader(&#39;Accept&#39;);
if (! $accept || ! preg_match(&#39;#^application/([^+\s]+\+)?json#&#39;, $accept)) {
    $response-&gt;getBody()-&gt;write(json_encode([
        &#39;status&#39; =&gt; 405,
        &#39;detail&#39; =&gt; &#39;This API can only provide JSON representations&#39;,
    ]));
    emit($response
        -&gt;withStatus(405, &#39;Not Acceptable&#39;)
        -&gt;withHeader(&#39;Content-Type&#39;, &#39;application/problem+json&#39;)
    );
    exit();
}

$body = (string) $request-&gt;getBody();
$request = $request
    -&gt;withBodyParams(json_decode($body));
</code></pre>

<p>
    The above demonstrates several features. First, it shows retrieving a 
    request header, and branching logic based on that header. Second, it shows 
    populating a response object in the case of an error. (<code>emit()</code> 
    is a hypothetical function that would take the response object and emit 
    headers and content.) Finally, it shows retrieving the body, deserializing 
    it, and re-injecting the request.
</p>

<p>
    <code>with</code> methods exist for each of the various input types 
    available to <code>ServerRequestInterface</code> instances.
</p>

<h3>Attributes</h3>

<p>
    Another feature of server-side requests are &quot;attributes.&quot; These 
    are intended for storing values that are computed from the current request. 
    A common use case is for storing the results of routing (decomposing the 
    URI to key/value pairs).
</p>

<p>The <code>attributes</code> API includes:</p>

<ul>
    <li><code>getAttribute($name, $default = null)</code> to retrieve a single 
        named attribute, and return a default value if the attribute is not 
        present.</li>
    <li><code>getAttributes()</code> to retrieve the entire set of attributes 
        currently stored.</li>
    <li><code>withAttribute($name, $value)</code> to return a new 
        <code>ServerRequestInterface</code> instance that composes the given 
        attribute.</li>
    <li><code>withoutAttribute()</code> to return a new 
        <code>ServerRequestInterface</code> instance that  does not compose the 
        given attribute.</li>
</ul>

<p>
    As an example, let's use the <a 
    href="https://github.com/auraphp/Aura.Router">Aura Router</a> with our 
    request instance:
</p>

<pre><code class="lang-php">
use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;

$router = new Router(
    new RouteCollection(new RouteFactory()),
    new Generator()
);

$path  = $request-&gt;getUri()-&gt;getPath();
$route = $router-&gt;match($path, $request-&gt;getServerParams());
foreach ($route-&gt;params as $param =&gt; $value) {
    $request = $request-&gt;withAttribute($param, $value);
}
</code></pre>

<p>
    The request instance, in this case, is used to marshal data to feed to the 
    router, and then the results of routing are used to seed the request instance.
</p>

<h2>Use Cases</h2>

<p>
    Now that you've had a whirlwind tour of the various components of PSR-7, 
    let's turn to some concrete use cases.
</p>

<h3>Clients</h3>

<p>
    The editor prior to myself on PSR-7, <a href="http://mtdowling.com">Michael 
    Dowling</a>, is the author of the popular HTTP client <a 
    href="http://guzzlephp.org">Guzzle</a> — so it's a perfectly natural leap that 
    PSR-7 will benefit HTTP clients. Let's consider how.
</p>

<p>
    First, it means that developers will have a unified message interface to 
    use for making requests; they can pass PSR-7 request instances to a client, and 
    will get PSR-7 response instances in return.
</p>

<pre><code class="lang-php">$response = $client-&gt;send($request);</code></pre>

<p>
    Because messages and URIs are modeled as value objects, it also means that 
    developers can create base instances of requests and URIs, and create discrete 
    requests and URIs from them:
</p>

<pre><code class="lang-php">
$baseUri     = new Uri(&#39;https://api.example.com&#39;);
$baseRequest = (new Request())
    -&gt;withUri($baseUri)
    -&gt;withHeader(&#39;Authorization&#39;, $apiToken);

while ($action = $queue-&gt;dequeue()) {
    // New response instance! Only contains
    // URI and Authorization header from base.
    $request = $baseRequest
        -&gt;withMethod($action-&gt;method)
        -&gt;withUri($baseUri-&gt;withPath($action-&gt;path)); // new URI!

    foreach ($action-&gt;headers as $header =&gt; $value) {
        // The base request WILL NOT receive these headers, ensuring subsequent
        // requests only compose the headers they need!
        $request = $request-&gt;withHeader($header, $value);
    }
    
    $response = $client-&gt;send($request);
    $status   = $response-&gt;getStatusCode();
    if (! in_array($status, range(200, 204))) {
        // Request failed!
        break;
    }

    // Grab the data!
    $data-&gt;enqueue(json_decode((string) $response-&gt;getBody()));
}
</code></pre>

<p>
    What PSR-7 provides is a standard way to interact with the requests you 
    send with the client, and the responses you receive. By implementing value 
    objects, we enable some interesting use cases with regards to simplifying the 
    &quot;reset request&quot; pattern -- changing the request always results in a 
    new instance, allowing us to have a base instance with a known state that we 
    can always build from.
</p>

<h3>Middleware</h3>

<p>
    I won't go too much into this, as <a 
    href="https://mwop.net/blog/2015-01-08-on-http-middleware-and-psr-7.html">I've 
    already done so before</a>. The basic idea, however, is this:
</p>

<pre><code class="lang-php">
function (
    ServerRequestInterface $request,
    ResponseInterface $response,
    callable $next = null
) {
}
</code></pre>

<p>
    The function accepts the two HTTP messages, and does something with them — 
    which might include delegating to the &quot;next&quot; middleware available. 
    Middleware usually returns a response.
</p>

<p>
    Another pattern often used is the &quot;lambda&quot; pattern (thanks to <a 
    href="http://www.garfieldtech.com/">Larry Garfield</a> for coining this term on 
    the mailing list!):
</p>

<pre><code class="lang-php">
class Command
{
    private $wrapped;

    public function __construct(callable $wrapped)
    {
        $this-&gt;wrapped = $wrapped;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        // manipulate the request, maybe
        $new = $request-&gt;withAttribute(&#39;foo&#39;, &#39;bar&#39;);

        // delegate to the middleware we wrap:
        $result = ($this-&gt;wrapped)($new, $response);

        // see if we got a response
        if ($result instanceof ResponseInterface) {
            $response = $result;
        }

        // manipulate the response before returning
        return $reponse-&gt;withHeader(&#39;X-Foo&#39;, &#39;Bar&#39;);
    }
}
</code></pre>

<p>
    The idea behind middleware is that it's composable, and follows a standard, 
    predictable pattern with predictable behavior. It's a great way to write 
    re-usable web components.
</p>

<h3>Frameworks</h3>

<p>
    One thing frameworks have been providing for many years is... HTTP message 
    abstraction. PSR-7 aims to provide a common set of interfaces so that 
    frameworks can use the same set of abstractions. This will enable developers to 
    write re-usable, framework-agnostic web components that frameworks can consume 
    — or, at least, that's what <em>I</em> would like to see!
</p>

<p>
    Consider Zend Framework 2: it defines the interface 
    <code>Zend\Stdlib\DispatchableInterface</code> which is the base interface 
    for any controller you want to use in the framework:
</p>

<pre><code class="lang-php">
use Zend\Http\RequestInterface;
use Zend\Http\ResponseInterface;

interface DispatchableInterface
{
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response
    );
}
</code></pre>

<p>
    This actually looks a lot like the middleware examples above; the only real 
    difference is that it consumes framework-specific HTTP message 
    implementations. What if, instead, it could consume PSR-7?
</p>

<p>
    Most implementations of HTTP messages in frameworks are mutable, which 
    means that you can change the state of the message at any time. This can 
    lead to problems at times, particularly if you have assumptions about 
    message state that are no longer true. The other problem, though, is 
    tracking down when state changes.
</p>

<p>
    PSR-7's messages are value objects. As such, you would need to notify the 
    application somehow of any change to one of the messages. This becomes more 
    explicit, and thus easier to trace in your code (both with step debuggers 
    as well as static analysis tools).
</p>

<p>
    As an example, if ZF2 were updated to use PSR-7 messages, developers would 
    need to notify the <code>MvcEvent</code> of any changes they want propagated to 
    later consumers:
</p>

<pre><code class="lang-php">
// Inside a controller:
$request  = $request-&gt;withAttribute(&#39;foo&#39;, &#39;bar&#39;);
$response = $response-&gt;withHeader(&#39;X-Foo&#39;, &#39;bar&#39;);

$event = $this-&gt;getEvent();
$event-&gt;setRequest($request)
      -&gt;setResponse($response);
</code></pre>

<p>The above code makes it explicit that we are changing application state.</p>

<p>
    Having value objects makes simpler one specific practice: the idea of 
    dispatching &quot;sub-requests&quot; or implementing Hierarchical MVC (HMVC). 
    In these cases, you can create new requests based on the current without 
    altering it, <em>ensuring the application state remains unchanged.</em>
</p>

<p>
    Essentially, for many frameworks, dropping in the PSR-7 messages will lead 
    to portable abstractions that can be used across frameworks, and make it 
    possible to consume generic middleware relatively easily. To adopt the 
    messages, however, will likely require some minor changes such as the above 
    when developers need to modify the messages for use with the application state.
</p>

<h2>Resources</h2>

<p>
    Hopefully, you're starting to see the benefits PSR-7 will provide: a 
    unified, complete abstraction around HTTP messages. Further, the 
    abstraction can be used for either side of the HTTP transaction — whether 
    you're sending requests via an HTTP client, or handling a server-side 
    request.
</p>

<p>
    The PSR-7 specification is not yet final, but what I've outlined above is 
    not likely to undergo significant change before putting it forth for a vote. If 
    you want more details, you can read the specification:
</p>

<ul>
    <li><a href="https://github.com/php-fig/fig-standards/blog/master/proposed/http-message.md">https://github.com/php-fig/fig-standards/blog/master/proposed/http-message.md</a></li>
</ul>

<p>
    I also encourage you to read the metadocument for the proposal, as it 
    describes the goals, design decisions, and results of the (endless) debates 
    over the past two years:
</p>

<ul>
    <li><a href="https://github.com/php-fig/fig-standards/blog/master/proposed/http-message-meta.md">https://github.com/php-fig/fig-standards/blog/master/proposed/http-message-meta.md</a></li>
</ul>

<p>
    The actual interfaces are published as the package 
    <code>psr/http-message</code>, which you can install via composer. It is 
    always updated at the same time as the proposal.
</p>

<p>
    I've created a library, <code>phly/http</code>, which provides concrete 
    implementations of the proposed interfaces; I update it whenever I update 
    the proposal. It, too, is installable via composer.
</p>

<p>Finally, if you want to play with middleware based on PSR-7, you have a couple of options:</p>

<ul>
    <li><a href="https://github.com/phly/conduit">phly/conduit</a>, a port of 
        Sencha's <a href="https://github.com/senchalabs/connect">Connect</a> to 
        PHP using <code>phly/http</code> and <code>psr/http-message</code> as its 
        foundation.</li>
    <li><a href="https://github.com/Crell/stacker">Stacker</a>, a <a 
        href="http://stackphp.com">StackPHP</a>-like implementation written by 
        Larry Garfield.</li>
</ul>

<p>I'm looking forward to passage of PSR-7; I think it will enable a whole new breed of PHP applications.</p>
EOT;
$entry->setExtended($extended);

return $entry;
