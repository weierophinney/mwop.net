<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('250-Simple-Interfaces-and-Micro-MVCs');
$entry->setTitle('Simple Interfaces and Micro MVCs');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1293059224);
$entry->setUpdated(1298559246);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
My job is great: I get to play with technology and code most days. My job is
also hard: how does one balance both functionality and usability in programming
interfaces?
</p>

<p>
I've been working, with <a href="http://ralphschindler.com">Ralph Schindler</a>, on a
<a href="http://bit.ly/zf2mvcprops">set of proposals</a> around the 
<a href="http://framework.zend.com/">Zend Framework</a> 2.0 MVC layer,
specifically the "C", or "Controller" portion of the triad. There are a ton of
requirements we're trying to juggle, from making the code approachable to
newcomers all the way to making the code as extensible as possible for the
radical performance tuning developers out there. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
One interface I've been toying with is inspired by two very different sources.
The first is PHP's own <a href="http://php.net/SoapServer">SoapServer API</a> (which we use
already in our various server components); the other was a discussion I had with
<a href="http://fabien.potencier.org">Fabien Potencier</a> (of Symfony fame) a couple years
ago, where he said the goal of Symfony 2 would be "to transform a request into a
response."
</p>

<p>
What I've come up with right now is the following:
</p>

<div class="example"><pre><code lang="php">
interface Dispatchable
{
    /**
     * @return Response
     */
    public function dispatch(Request $request);
}
</code></pre></div>

<p>
I can hear some of you ZF folks saying already, "Really, that's all you've come
up with so far?" Here's why I think it may be remarkable:
</p>
<blockquote>
<strong><em>It makes it trivially simple to do a ZF1 style MVC, incorporate
        server endpoints as controllers, or to write your own micro MVC.</em></strong>
</blockquote>

<p>
The idea is that this interface (and the Request/Response interfaces) become the
basic building blocks for both a standard ZF MVC implementation, or your own
custom MVC implementation.
</p>

<p>
Which is where the subject of micro MVCs finally becomes relevant.
</p>

<h2 id="toc_1.1">Micro MVCs</h2>

<p>
A little over a year ago, with PHP 5.3 finally releasing, I started seeing a
number of "micro MVC frameworks" popping up; seriously, for a while there, it
seemed like every other day, <a href="http://phpdeveloper.org/">phpdeveloper</a> was posting
a new one every other day.
</p>

<p>
Micro MVCs are quite interesting. If you consider the bulk of the websites you
encounter, they really only consist of a few pages, and a smattering of actual
functionality that requires things like form handling or models. As such, using
a full-blown MVC such as ZF, Symfony, even CodeIgniter, seems crazy. A micro
MVC addresses simultaneously the issues of simplification and expressiveness;
the point is to get the work done as quickly as possible, preferably with as few
lines as possible.
</p>

<p>
In looking at many of these micro MVC frameworks, I noted a few things:
</p>

<ul>
<li>
Most were either using regex for routing, or a lightweight router such as
   <a href="http://dev.horde.org/routes/">Horde Routes</a> to route the request.
</li>
<li>
Most were utilizing closures and/or currying to then map the routing results
   to "actions".
</li>
</ul>

<p>
So I whipped up a little something using the above <code>Dispatchable</code> interface, to
see what I might be able to do.
</p>

<div class="example"><pre><code lang="php">
use Zend\Stdlib\Dispatchable,
    Zend\Http\Response as HttpResponse,
    Fig\Request,
    Fig\Response;

class Dispatcher implements Dispatchable
{
    protected $controllers;

    public function attach($spec, $callback = null)
    {
        if (is_array($spec) || $spec instanceof \Traversable) {
            foreach ($spec as $controller =&gt; $callback) {
                $this-&gt;attach($controller, $callback);
            }
            return $this;
        }

        if (!is_scalar($spec)) {
            throw new \InvalidArgumentException('Spec must be scalar or traversable');
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Callback must be callable');
        }

        $this-&gt;controllers[$spec] = $callback;
        return $this;
    }

    /**
     * Dispatch a request
     * 
     * @param  Request $request 
     * @return Response
     */
    public function dispatch(Request $request)
    {
        if (!$controller = $request-&gt;getMetadata('controller')) {
            return new PageNotFoundResponse( '&lt;h1&gt;Page not found&lt;/h1&gt;' );
        }

        if (!array_key_exists($controller, $this-&gt;controllers)) {
            return new PageNotFoundResponse('&lt;h1&gt;Page not found&lt;/h1&gt;');
        }

        $handler  = $this-&gt;controllers[$controller];
        $response = $handler($request);

        if (is_string($response)) {
            return new HttpResponse($response);
        }
        if (!is_object($response)) {
            return new ApplicationErrorResponse('&lt;h1&gt;An error occurred&lt;/h1&gt;');
        }
        if (!$response instanceof Response) {
            if (!method_exists($response, '__toString')) {
                return new ApplicationErrorResponse('&lt;h1&gt;An error occurred&lt;/h1&gt;');
            }
            return new HttpResponse($response-&gt;__toString());
        }
        return $response;
    }
}
</code></pre></div>

<p>
Don't worry about the various objects referenced; the main thing to understand
is that it's using those same building blocks I referred to earlier: <code>Request</code>,
<code>Response</code>, <code>Dispatchable</code>. In action, it looks like this:
</p>

<div class="example"><pre><code lang="php">
use Zend\Controller\Router,
    Zend\Http\Request;

$request = new Request;

$router = new Router;
/*
 * Configure some routes here. We'll assume we've somehow configured routes
 * mapping the following controllers:
 * - homepage
 * - foo
 * - rest
 * - foobar
 */
$router-&gt;route($request);

$dispatcher = new Dispatcher();
$dispatcher
-&gt;attach('homepage', function($request) {
    // Simply returning a string:
    return '&lt;h1&gt;Welcome&lt;/h1&gt; &lt;p&gt;Welcometo our site!&lt;/p&gt;';
})
-&gt;attach('foo', function($request) {
    // Simply returning a string:
    return '&lt;h1&gt;Foo!&lt;/h1&gt;';
})
-&gt;attach('rest', function($request) {
    // Example of a \&quot;REST\&quot; service...
    switch ($request-&gt;getMethod()) {
        case 'GET':
            if (!$id = $request-&gt;query('id', false)) {
                // We have a \&quot;list operation\&quot;...
                // Assume we somehow grab the list and create a response
                return $response;
            }
            // We have an ID -- fetch it and return the page
            break;
        case 'POST':
            // Create document and return a response
            break;
        case 'PUT':
            if (!$id = $request-&gt;query('id', false)) {
                // No ID in the query string means no document!
                // Return a failure response
            }
            // We have an ID -- fetch and update from PUT params, and
            // return a response
            break;
        case 'DELETE':
            if (!$id = $request-&gt;query('id', false)) {
                // No ID in the query string means no document!
                // Return a failure response
            }
            // We have an ID -- delete, and // return a response
            break;
        default:
            return new ApplicationErrorResponse('Unknown Method');
            break;
    }
})
-&gt;attach('foobar', function($request) {
    // Curry in controllers to allow them to be lazy-loaded, and to ensure we 
    // get a response object back (Dispatcher will take care of that).
    $controller = new FooBarController();
    return $controller-&gt;dispatch($request);
});

$response = $dispatcher-&gt;dispatch($request);
$response-&gt;emit();
</code></pre></div>

<p>
It's dead simple: we attach named callbacks to the Dispatcher. The Dispatcher
checks to see if the Router found a controller name in the Request, and, if it
did and a callback for it exists, executes it. If it gets a string, we use that
as the content; an exception triggers an <code>ApplicationErrorResponse</code>, and if we
get a Response object back, we just use it.
</p>

<p>
While I did the Dispatcher configuration/setup in the same script, it could have
been done as an include file to simplify that script endpoint. 
</p>

<p>
The point is that the interface definitions made this really, really easy to
come up with and implement in a matter of minutes.
</p>

<p>
<em>I'm not sure if this will end up being in ZF2; even if it isn't, it still meets the goal I set out at the start of this post: balancing usability with flexibility.</em>
</p>

<p>
<a href="http://bit.ly/zf2mvcprops">Discuss!</a>
</p>

<h4>Updates</h4>
<ul>
    <li><b>2011-02-24</b>: Fixed first class declaration example to use "implements" instead of "extends"</li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;