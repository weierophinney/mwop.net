<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-08-17-on-microframeworks');
$entry->setTitle('On Microframeworks');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-08-17 11:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-08-17 11:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'php',
  'zf2',
  'zend framework',
  'microphp',
));

$body =<<<'EOT'
<p>
    A number of months ago, <a href="http://funkatron.com/">Ed Finkler</a> 
    started a discussion in the PHP community about &#8220;<a 
    href="http://microphp.org/">MicroPHP</a>&#8221;; to summarize, the movement
    is about:
</p>

<ul>
    <li>Building small, single-purpose libraries.</li>
    <li>Using small things that work together to solve larger problems.</li>
</ul>

<p>
    I think there are some really good ideas that have come out of this, and 
    also a number of questionable practices<sup><a name="t1" href="#f1">1</a></sup>.
</p>

<p>
    One piece in particular I've focussed on is the concept of so-called 
    &#8220;microframeworks&#8221;.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>What is a microframework?</h2>

<p>
    PHP has had microframeworks for quite some time<sup><a name="t2" href="#f2">2</a></sup>, 
    though I only really first saw the term being used around 3 years ago. The 
    &#8220;grand-daddy&#8221; of modern-day microframeworks can actually be 
    traced to Ruby, however, and specifically 
    <a href="http://www.sinatrarb.com">Sinatra</a>.
</p>

<p>
    Sinatra is not so much a framework as it is a domain-specific language (DSL).
    The language and structure it created, however, have been re-created in the
    vast majority of microframeworks you see currently in the PHP arena. 
    Specifically, it describes how to map HTTP request methods and paths to the
    code that will handle them. It borrowed route matching ideas from
    <a href="http://rubyonrails.org/">Ruby on Rails</a>, and relied on the fact 
    that Ruby uses the last value of a block as the return value.
</p>

<p>
    As some simple examples:
</p>

<div class="example"><pre><code class="language-ruby">
get '/hello/:name' do |n|
    "Hello #{n}!"
end

post '/address'
    # create address
end

put '/address/:id' |i|
    # update address
end

get '/feed.?:format?', :provides => ['rss', 'atom', 'xml'] do
    builder :feed
end
</code></pre></div>

<p>
    The language is expressive, and allows the developer to focus on two things:
</p>

<ul>
    <li>What are the specific entry points (URIs) for the application?</li>
    <li>What needs to be done for each specific entry point?</li>
</ul>

<p>
    I'd argue that the above two points are the defining characteristics of 
    modern microframeworks. Typically, the entry points are given the term
    &#8220;routing &#8221;, and the second corresponds to &#8220;controllers&#8221;.
</p>

<h2>PHP implementations</h2>

<p>
    I'd argue one of the earliest microframework implementations, though it 
    wasn't termed as such, was <a href="http://dev.horde.org/routes/">Horde 
    Routes</a><sup><a name="t3" href="#f3">3</a></sup> (which was itself inspired by <a 
    href="http://routes.readthedocs.org/en/latest/index.html">Python Routes</a>, 
    in turn inspired by the Rails routing system, like Sinatra). It follows
    the two principles I outlined above: it allows defining routes (entry points),
    and mapping them to controllers. Controllers for Routes are simply classes, 
    and a route must provide both a controller and an action in the match, with 
    the latter corresponding to a method on the controller class.
</p>

<p>
    Since around 2009, I've seen an increasing number of new PHP microframeworks<sup><a name="t4" href="#f4">4</a></sup>
    that follow in the steps of Sinatra and Horde. In the various 
    implementations I've looked at, instead of using a DSL, the authors have all
    opted for either a procedural or OOP interface. Starting with PHP 5.3, most
    authors have also primarily targetted any PHP callable as a controller, 
    favoring callbacks specifically. The fundamental ideas remain the same as 
    Sinatra, however:
</p>

<div class="example"><pre><code class="language-php">
/* Procedural */
get('/hello/:name', function ($n) {
    return "Hello {$n}!";
});

post('/address', function () {
    // create address
});

put('/address/:id' function ($i) {
    // update address
});

get('/feed.?:format?', function($feed, $format) {
    return builder($feed, $format);
});

/* OOP */
$app->get('/hello/:name', function ($n) {
    return "Hello {$n}!";
});

$app->post('/address', function () {
    // create address
});
end

$app->put('/address/:id', function ($i) {
    // update address
});

$app->get('/feed.?:format?', function ($feed, $format) use ($app) {
    return $app->builder($feed, $format);
})->constraints(['format' => '/^(rss|atom|xml)$/']);
</code></pre></div>

<p>
    One key difference I've witnessed in the implementations is surrounding
    how route matches are passed to the callback. In the examples above, they
    are passed as individual arguments to the handler. Some, however, opt for
    an approach more like Sinatra, which passes a single "params" argument into
    the scope of the handler. This approach tends to be more expedient both 
    from an implementation standpoint as well as a performance standpoint, as
    it does not require reflection to determine name and position of arguments,
    and makes handling wildcard arguments simpler. I've seen this latter 
    approach handled several ways:
</p>

<div class="example"><pre><code class="language-php">
// Pass in route match parameters as an argument.
$app->get('/feed.:format', function ($params) {
    $format = $params['format'];
});

// Pass in the $app instance, and retrieve route 
// match parameters from it.
$app->get('/feed.:format', function ($app) {
    $format = $app->params('format');
});

// Curry in the $app instance when desired, and 
// retrieve route match parameters from it.
$app->get('/feed.:format', function () use ($app) {
    $format = $app->params('format');
});
</code></pre></div>

<p>
    Another difference I've seen is in how route constraints, defaults, and
    names are handled. The most elegant solutions usually allow chaining
    method calls in order to alter this data:
</p>

<div class="example"><pre><code class="language-php">
$app->get('/feed.:format', function ($app) {
  })->constraints(['format' => '/^(atom|xml|json)$/'])
    ->name('feed');
</code></pre></div>

<p>
    One common feature I've seen is the ability to generate URLs based on the 
    defined routes. Most commonly, this is a function or method 
    <code>urlTo()</code>, which takes a route name, and an associative array
    of replacements.
</p>

<div class="example"><pre><code class="language-php">
echo $app->urlTo('feed', ['format' => 'atom']);
</code></pre></div>

<p>
    That's it in a nutshell: the ability to match HTTP request methods and
    path information, and map it to controllers/handlers, and to generate
    URLs based on those present in the application.
</p>

<h2>What are they good for?</h2>

<p>
    In my research and experience, microframeworks have three typical use cases:
</p>

<ol>
    <li>
        <b>Prototyping.</b> Because of their simplicity, microframeworks are 
        fantastic for prototyping a basic website. Very often, in the early
        stages of a site, you have a limited number of pages, and most often
        simply need to render a template with limited variable substitutions.
        Microframeworks are a perfect fit for this.
    </li>

    <li>
        <b>APIs</b>. API needs are usually quite well-defined, and often 
        involve a small, finite number of URLs. The logic required is usually
        already encapsulated in business objects, so the application layer
        is simply for filtering and returning a representation. Microframeworks
        again offer a nice fit.
    </li>

    <li>
        <b>Small, mostly static sites</b>. Similar to the first point, if you
        know the site will be relatively small and mostly static, then the
        minimal overhead of a microframework is often a good fit.
    </li>
</ol>

<h2>Where do microframeworks fail?</h2>

<p>
    Because of the rather declarative nature of microframeworks, and the
    typically 1:1 mapping of a route to a controller, microframeworks do
    not tend to promote code re-use. Additionally, this extends to how
    microframework applications are organized: usually, there are no clear
    guidelines on how to organize routes and controllers, much less separate
    them into multiple files. This can lead to maintenance issues as the
    application grows, as well as logistical issues whenever you need to 
    add new routes and controllers (do they go at the top, or bottom? are
    there other routes that could potentially match as well? etc.). 
</p>

<p>
    Additionally, though many frameworks offer ways to alter the workflow
    of the application either via hooks, events, or &#8220;middleware&#8221;<sup><a name="t5" href="#f5">5</a></sup>,
    most of these are limited in scope, often non-reusable, and often
    non-stackable. As such, comprehensive manipulation of the application
    workflow is out of reach.
</p>

<p>
    One other area that is overlooked, however, is one I find curious, 
    particularly in light of the MicroPHP movement: so much of the
    underlying plumbing is basically the same, yet every microframework
    re-implements it. Specifically:
</p>

<ul>
    <li>
        Routing is basically the same across most implementations, following the
        same basic specifications outlined in Rails. There are very few differences
        in the public APIs.
    </li>

    <li>
        Request and Response object abstraction is largely the same as well, 
        providing access to query/post/cookie/session/etc. parameters through
        roughly equivalent APIs.
    </li>

    <li>
        Many implement their own view layers.<sup><a name="t6" href="#f6">6</a></sup>
    </li>
</ul>

<p>
    Most of this code should be considered commodity code at this point. There are 
    several outstanding view layers and templating engines available (Smarty, Twig,
    Savant, Zend\View). Standalone routing libraries exist such as Horde Routes,
    and even those bundled with frameworks are often available separately via
    Composer or Pyrus; the same is true with Request and Response object 
    abstraction. It seems to me that a few microframework authors should be 
    working on abstracting these concerns, and then focussing their efforts on
    differentiators in their own microframeworks.
</p>

<h2>An experiment</h2>

<p>
    Building on my last point, I looked at the APIs of <a 
    href="http://limonade-php.github.com/">Limonade</a> and <a 
    href="http://www.slimframework.com/">Slim Framework</a>, and built up a 
    specification for a microframework. I then matched as many pieces of it 
    as possible to existing components in <a 
    href="http://packages.zendframework.com/">ZF2</a>, and started building.
</p>

<p>
    In a matter of a few hours, I had written up a complete test suite<sup><a name="t7" href="#f7">7</a></sup> 
    and all code for a microframework, featuring the following (this is 
    basically the testdox output from the unit test suite):
</p>

<ul>
    <li>Lazy loads request</li>
    <li>Lazy loads response</li>
    <li>Request is injectible</li>
    <li>Response is injectible</li>
    <li>Halt should raise halt exception</li>
    <li>Response should contain status provided to halt</li>
    <li>Response should contain message provided to halt</li>
    <li>Stop should raise halt exception</li>
    <li>Response should remain unaltered after stop</li>
    <li>Redirect should raise halt exception</li>
    <li>Redirect should set 302 response status by default</li>
    <li>Redirect should set response status based on provided status code</li>
    <li>Redirect should set location header</li>
    <li>Map creates a segment route when provided with a string route</li>
    <li>Map can receive a route object</li>
    <li>Passing invalid route raises exception</li>
    <li>Map can receive a callable</li>
    <li>Passing invalid controller to route does not immediately raise exception</li>
    <li>Accessing invalid controller raises exception</li>
    <li>Passing invalid method to route via method raises exception</li>
    <li>Can set methods route responds to singly</li>
    <li>Can set methods route responds to as array</li>
    <li>Can set methods route responds to as multiple arguments</li>
    <li>Can specify additional method types to respond to</li>
    <li>Can specify route name</li>
    <li>Adding route using method type creates route that responds to that method type</li>
    <li>Running with no matching routes raises page not found exception</li>
    <li>Routing sets list of named routes</li>
    <li>Routing sets lists of routes by method</li>
    <li>Successful routing dispatches controller</li>
    <li>Unsuccessful routing triggers 404 event</li>
    <li>Calling halt triggers halt event</li>
    <li>Invalid controller triggers 501 event</li>
    <li>Exception raised in controller triggers 500 event</li>
    <li>Can pass to next matching route</li>
    <li>Url for helper assembles url based on name provided</li>
    <li>Url for helper assembles url based on name and params provided</li>
    <li>Url for helper assembles url based on current route match when no name provided</li>
    <li>Composes logger instance by default</li>
    <li>Can inject specific logger instance</li>
    <li>Mustache view is used by default</li>
    <li>Can inject alternate view instance</li>
    <li>Render renders a template to the response</li>
    <li>View model returns mustache view model by default</li>
    <li>Subsequent calls to view model return separate instances</li>
    <li>Can provide view model prototype</li>
</ul>

<p>
    I utilized ZF2's routing library from its MVC component, the request and response
    objects from its HTTP component, its Log component, and the Session component. These
    had a few other dependencies, but nothing terribly onerous.
</p>

<p>
    For the view, I used my own <a 
    href="http://weierophinney.github.com/phly_mustache">phly_mustache</a>, and provided
    a basic "view model" implementation that receives the application instance, thus
    allowing the ability to call application helpers (such as url generation).
</p>

<p>
    To make installation simple, I used <a href="http://getcomposer.org">Composer</a>
    to manage my dependencies on specific ZF2 components and for phly_mustache. The
    microframework contains only the code it needs to get its work done, leveraging
    the work of others whenever possible.
</p>

<p>
    This post is not meant as a way to announce a new microframework, however.<sup><a name="t8" href="#f8">8</a></sup>
    The point of the experiment was to prove something: microframeworks are
    trivially easy to write, <em>particularly if you follow the principals of 
    MicroPHP, and re-use existing code</em>. Just because code comes from a framework
    or a third-party library does not make it suspect or inferior; in fact,
    whenever possible, you should leverage such code so you can focus on 
    <em>writing awesome applications</em>.
</p>

<h2>Lessons learned</h2>

<p>
    I really like microframeworks for specific problems: prototyping, APIs, and 
    small, simple sites. I think they are ideally suited for these tasks. That
    said, I'd love to see some solid libraries targetting the fundamental, shared
    aspects of these efforts: routing, request and response abstraction, etc.
    With dependency management tools such as Composer and Pyrus, having required
    dependencies is not a big deal anymore, and re-use should be encouraged.
</p>

<p>
    Also, writing a microframework is an excellent coding exercise. It helps a 
    developer appreciate the complexities of abstraction while limiting the number
    of moving parts. I highly recommend it as an exercise -- but do it using 
    available components, and be prepared to throw it away and instead collaborate
    with others, or adopt something which better solves both the problems you have
    and the problems you anticipate.
</p>

<p>
    In sum: <em>Use the right tool for the job</em>. If you foresee expanding 
    requirements in your project's future, you may want to evaluate a full-stack
    framework,<sup><a name="t9" href="#f9">9</a></sup> or consider building something 
    robust that suits your specific project's needs. Use microframeworks where
    and when they make sense.
</p>

<h4>Afterword</h4>

<p>
    I'm well aware that Fabien Potencier has written
    <a href="http://fabien.potencier.org/article/50/create-your-own-framework-on-top-of-the-symfony2-components-part-1">a comprehensive series of posts on creating a microframework using Symfony 2
    components</a>. I deliberately chose not to read them until (a) ZF2 was 
    almost ready to release, and (b) I'd had a chance to formulate my own
    opinions on microframeworks. They're an excellent read, however, and show a 
    nice progression of development from flat PHP to a fully functional 
    microframework; click the link and see for yourself.
</p>

<h4>Footnotes</h4>

<ul>
    <li>
        <sup><a name="f1" href="#t1">1</a></sup> In particular, I feel that the movement 
        (a) disparages components from larger libraries simply because they
        originate from a larger library, and (b) distrust any code that has 
        additional dependencies. This latter I find truly puzzling, as I'd 
        think it fits the idea of &#8220;use small things that work together to 
        solve larger problems.&#8221; If the code solves a particular problem
        and allows you to focus on a larger problem, where it originates and 
        the number of dependencies should not be an issue.
    </li>

    <li>
        <sup><a name="f2" href="#t2">2</a></sup> In fact, my first foray into MVC in PHP 
        was writing a clone of Perl's <a href="http://cgi-app.org/">CGI::Application</a>,
        which in many ways is also a microframework.
    </li>

    <li>
        <sup><a name="f3" href="#t3">3</a></sup> Trivia: Both authors of Horde Routes 
        worked at Zend when I first started at the company, and Mike Naberezny
        wrote the very first lines of code for Zend Framework.
    </li>

    <li>
        <sup><a name="f4" href="#t4">4</a></sup> I swear, you see new ones on Github daily,
        and on <a href="http://phpdeveloper.org/">PHP Developer</a> at least
        once a week.
    </li>

    <li>
        <sup><a name="f5" href="#t5">5</a></sup> <a href="http://www.slimframework.com">Slim</a>
        has this concept. Basically, any callables placed between the route 
        string and the last callable when defining a route -- i.e., the &#8220;middle&#8221;
        arguments, and thus middleware -- will be executed in order prior to 
        attempting to execute the controller.
    </li>

    <li>
        <sup><a name="f6" href="#t6">6</a></sup> <a href="http://www.slimframework.com">Slim</a>
        is an outlier here, as it utilizes <a href="http://twig.sensiolabs.org/">Twig</a>
        by default.
    </li>

    <li>
        <sup><a name="f7" href="#t7">7</a></sup> I'm sure that my TDD experiment will warm the
        soul of <a href="http://www.littlehart.net/atthekeyboard/" alt="Chris 
        Hartjes">the Grumpy Programmer</a>.
    </li>

    <li>
        <sup><a name="f8" href="#t8">8</a></sup> That said, if you want to look at the results, 
        you can <a href="http://github.com/weierophinney/phlyty">find Phlyty on Github</a>.
    </li>

    <li>
        <sup><a name="f9" href="#t9">9</a></sup> As you may guess, I'm biased towards <a 
        href="http://framework.zend.com/">Zend Framework</a>. However, you should always
        carefully evaluate a framework against your project's needs.
    </li>
</ul>
EOT;
$entry->setExtended($extended);

return $entry;

