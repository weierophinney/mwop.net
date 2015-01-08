<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2015-01-08-on-http-middleware-and-psr-7');
$entry->setTitle('On HTTP, Middleware, and PSR-7');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2015-01-08 17:15', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2015-01-08 17:15', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'http',
  'middleware',
  'php',
  'programming',
));

$body =<<<'EOT'
<p>As I've surveyed the successes and failures of ZF1 and ZF2, I've started 
    considering how we can address usability: how do we make the framework more 
    approachable?</p>

<p>One concept I've been researching a ton lately is <em>middleware</em>. 
    Middleware exists in a mature form in
    Ruby (via <a href="https://rack.github.io">Rack</a>),
    Python (via <a href="https://www.python.org/dev/peps/pep-0333/">WSGI</a>),
    and Node (via <a href="https://github.com/senchalabs/connect">Connect</a> /
    <a href="http://expressjs.com">ExpressJS</a>); just about every language 
    has some exemplar. Even PHP has some examples already, in
    <a href="http://stackphp.com">StackPHP</a> and
    <a href="http://www.slimframework.com">Slim Framework</a>.</p>

<p>The basic concept of middleware can be summed up in a single method signature:</p>

<pre><code class="lang-javascript"><span class="kw">function</span> (request, response) { }</code></pre>

<p>The idea is that objects, hashes, or structs representing the HTTP request 
    and HTTP response are passed to a callable, which does something with them. You 
    compose these in a number of ways to build an application.</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>In Rack and StackPHP, you use objects, and pass middleware to other middleware:</p>

<pre><code class="lang-php">// This is pseudocode, and does not 1:1 represent any specific project:
class Action
{
    private $middleware;

    public function __construct(callable $middleware)
    {
        $this-&gt;middleware = $middleware;
    }

    public function __invoke($request, $response)
    {
        // do something before

        call_user_func($this-&gt;middleware, $request, $response);

        // do something after
    }
}</code></pre>

<p>In Connect, and, by extension, ExpressJS, instead of injecting the object, 
    you pass an additional callable, <code>next</code>, to the middleware function, 
    which it can invoke if desired:</p>

<pre><code class="lang-php">// This is pseudocode, and does not 1:1 represent any specific project:
class Action
{
    public function __invoke($request, $response, callable $next = null)
    {
        // do something before

        if ($next) {
            $next();
        }

        // do something after
    }
}</code></pre>

<p>There are other patterns as well, but these are the two most prevalent. The 
    basic idea is the same: receive a request and response, do something with them, 
    and optionally tell the invoking process to do more.</p>

<p>What I like about the concept of middleware is that I can explain it 
    succinctly in such a way that another developer can understand it immediately. 
    This is one reason why middleware has thrived in these other languages: it's 
    approachable by developers from a wide-range of experience levels.</p>

<p>(Interesting side-note: Symfony 2 and Zend Framework 2 actually both 
    implement similar patterns -- Symfony in its <code>HttpKernelInterface</code> 
    and ZF2 in its <code>DispatchableInterface</code>.)</p>

<p>However, middleware can only exist when there are good HTTP request and 
    response abstractions. In fact, I'd argue that middleware naturally evolves 
    when those abstractions are present already. Languages with good middleware 
    implementations have good HTTP abstractions.</p>

<p>PHP does not.</p>

<p>&quot;But PHP was built for the web!&quot; I hear many of you say. True. But 
    it was built for the web in the 90s. More specifically, it was built with
    <a href="http://en.wikipedia.org/wiki/Common_Gateway_Interface">Common Gateway 
    Interface</a> (CGI) in mind. CGI was a way for the web server to offload the 
    incoming request to a script; originally, it actually would set a whole 
    bunch of environment variables, and your script would pull from those in 
    order to get input and return a response. This evolved into PHP's Server 
    APIs (SAPI) -- <code>mod_php</code> in Apache, the php-fpm/FastCGI SAPI, 
    etc. -- and that data is present in PHP's <code>$_SERVER</code> 
    superglobal. PHP also tacked on other superglobals such as 
    <code>$_GET</code>, <code>$_POST</code>, and <code>$_COOKIE</code> to 
    simplify getting the most common input data. But PHP stopped there, at 
    version 4.1.0 (!).</p>

<p>What this means is that PHP developers are left with a ton of work to do to 
    get at what should be the most common aspects of HTTP:</p>

<ul>
    <li>You must analyze the <code>SCHEME</code>, 
        <code>HTTP_X_FORWARDED_PROTO</code>, <code>HOST</code>,  
        <code>SERVER_NAME</code>, <code>SERVER_ADDR</code>, <code>REQUEST_URI</code>, 
        <code>UNENCODED_URL</code>,  <code>HTTP_X_ORIGINAL_URL</code>, 
        <code>ORIG_PATH_INFO</code>, and <code>QUERY_STRING</code> elements of the  
        <code>$_SERVER</code> superglobal elements in order to fully and accurately 
        determine the  request URI in a cross-platform way. (Bonus points if you know 
        why!) </li>
    <li>Headers are also in <code>$_SERVER</code>, with prefixes of 
        <code>HTTP_</code>... unless they have  to do with the various 
        <code>Content-Type*</code> headers. </li>
    <li>Until 5.6, <code>php://input</code>, which stores the raw message 
        content, is <em>read-once</em>,  which means if multiple handlers need to 
        inspect it, you must cache it --  which poses problems if the cache is not 
        known to all handlers.</li>
</ul>

<p>When it comes to the response, as PHP developers, we have to learn that 
    output buffering exists and how to work with it. Why? Because if any content is 
    sent by the output buffer to the client before a header is sent, then PHP 
    silently discards the header. Good developers learn how things like 
    <code>display_errors</code> and <code>error_reporting</code> can affect output 
    buffering, how to nest output buffers, and more -- and that's even when they're 
    aggregating content to emit at once!</p>

<p>My point is that PHP's HTTP &quot;abstractions&quot; actually create a lot 
    of work for PHP developers. The abstractions present in Rack, WSGI, Node, and 
    others are often cleaner and more immediately usable (particularly
    <a href="http://nodejs.org/api/http.html">Node's</a>, in my opinion).</p>

<p><em><strong>We need good HTTP abstractions to simplify web development for 
    PHP developers.</strong></em></p>

<p>Good HTTP abstractions will <em>also</em> create an ecosystem in which 
    middleware can evolve.</p>

<p>As such, I've been working with the
    <a href="http://www.php-fig.org">Framework Interoperability Group</a> (FIG) since 
    September to help finalize a set of standard HTTP message interfaces so that we 
    can create an ecosystem in which PHP developers can create re-usable middleware 
    that they can share. (The new proposal has the designation
    <a href="https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md">PSR-7</a>.)</p>

<p>To me, this is the logical implication of Composer: <em>the ability to 
    package re-usable web-focussed widgets that can be composed into 
    applications</em>.</p>

<p>In other words, we'll no longer write Zend Framework or Symfony or Laravel 
    or framework-flavor-of-the-day applications or 
    modules/bundles/packages/what-have-you. We'll write middleware that solves a 
    discrete problem, potentially using other third-party libraries, and then 
    compose them into our applications -- whether those are integrated into a 
    framework or otherwise.</p>

<p>What this means is that we might compose middlewares that accomplish 
    discrete functionality in order to build up our website:</p>

<pre><code class="lang-php">$app = new MiddlewareRunner();
$app-&gt;add(&#39;/contact&#39;, new ContactFormMiddleware());
$app-&gt;add(&#39;/forum&#39;, new ForumMiddleware());
$app-&gt;add(&#39;/blog&#39;, new BlogMiddleware());
$app-&gt;add(&#39;/store&#39;, new EcommerceMiddleware());
$app-&gt;run($request, $response);</code></pre>

<p>Another use case would be to use middlewares that provide runtime aspects 
    that affect the behavior of our application as a whole. As an example, consider 
    an API engine, where you might have middleware for each behavior you want to 
    implement:</p>

<pre><code class="lang-php">$app = new MiddlewareRunner();
$app-&gt;add(new Versioning());
$app-&gt;add(new Router());
$app-&gt;add(new Authentication());
$app-&gt;add(new Options());
$app-&gt;add(new Authorization());
$app-&gt;add(new Accepts());
$app-&gt;add(new ContentType());
$app-&gt;add(new Parser());
$app-&gt;add(new Params());
$app-&gt;add(new Query());
$app-&gt;add(new Body());
$app-&gt;add(new Dispatcher());
$app-&gt;add(new ProblemHandler());
$app-&gt;run($request, $response);</code></pre>

<p>If I wanted to add my own authorization, I can look at the above, find the 
    line where that happens, and change it to use my own middleware. In other 
    words, <em>middleware can enable usability and composition for users</em>.</p>

<p>On top of that, in my experiments, well-written middleware and smart 
    middleware runners can also lead to incredible <em>performance</em>. You can 
    typically stop execution whenever you want by no longer calling 
    <code>next()</code>, or by skipping the decorated middleware, or by returning a 
    response (depending on the middleware runner architecture), and most 
    well-written middleware will do pre-emptive checks so that it exits (or calls 
    <code>next()</code>) early if it has nothing to do based on the current 
    request. Couple this with good architectural practices like dependency 
    injection and lazy-loading, and you can actually address each of usability, 
    performance, and maintainability in your projects -- not a bad coup!</p>

<p>(Caveat: as with any application architecture, you can also shoot yourself 
    in the foot; middleware is not a silver bullet or a guarantee.)</p>

<h2>Fin</h2>

<p>Too often, I feel as PHP developers we focus on the tools we use, and forget 
    that we're working in an HTTP-centric ecosystem. PHP doesn't help us, in that 
    regard. Additionally, I think we focus too much on our frameworks, and not 
    enough on how what we write could be useful across the entire PHP 
    ecosystem.</p>

<p>If PSR-7 is ratified, I think we have a strong foot forward towards building 
    framework-agnostic web-focused components that have real re-use capabilities -- 
    not just re-use within our chosen framework fiefdoms.</p>

<p>I'm working to do that, and I think we're getting close to a vote. If you're 
    interested in PSR-7, I urge you to take a look at the proposal:</p>

<ul>
    <li><a href="https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md">https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md</a></li>
</ul>

<p>the current related pull requests and issues:</p>

<ul>
    <li><a href="https://github.com/php-fig/fig-standards/issues?q=is%3Aopen+PSR-7">https://github.com/php-fig/fig-standards/issues?q=is%3Aopen+PSR-7</a></li>
</ul>

<p>and any discussion prefixed with <code>[PSR-7]</code> in the php-fig mailing list:</p>

<ul>
    <li><a href="https://groups.google.com/forum/#!searchin/php-fig/subject$3Apsr-7%7Csort:date">https://groups.google.com/forum/#!searchin/php-fig/subject$3Apsr-7%7Csort:date</li>
</ul>

<p>I've also created a prototype implementation of PSR-7:</p>

<ul>
    <li><a href="https://github.com/phly/http">https://github.com/phly/http</a></li>
</ul>

<p>and a port of Connect to PHP using it:</p>

<ul>
    <li><a href="https://github.com/phly/conduit">https://github.com/phly/conduit</a></li>
</ul>

<p>Join me in developing HTTP-centric PHP!</p>
EOT;
$entry->setExtended($extended);

return $entry;
