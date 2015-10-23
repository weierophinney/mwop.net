---
id: 2012-08-17-on-microframeworks
author: matthew
title: 'On Microframeworks'
draft: false
public: true
created: '2012-08-17T11:00:00-05:00'
updated: '2012-08-17T11:00:00-05:00'
tags:
    - php
    - zf2
    - 'zend framework'
    - microphp
---
A number of months ago, [Ed Finkler](http://funkatron.com/) started a
discussion in the PHP community about ["MicroPHP"](http://microphp.org/); to
summarize, the movement is about:

- Building small, single-purpose libraries.
- Using small things that work together to solve larger problems.

I think there are some really good ideas that have come out of this, and also a
number of questionable practices<sup>[1](#f1)</sup>.

One piece in particular I've focussed on is the concept of so-called
“microframeworks”.

<!--- EXTENDED -->

What is a microframework?
-------------------------

PHP has had microframeworks for quite some time<sup>[2](#f2)</sup>, though I
only really first saw the term being used around 3 years ago. The “grand-daddy”
of modern-day microframeworks can actually be traced to Ruby, however, and
specifically [Sinatra](http://www.sinatrarb.com).

Sinatra is not so much a framework as it is a domain-specific language (DSL).
The language and structure it created, however, have been re-created in the
vast majority of microframeworks you see currently in the PHP arena.
Specifically, it describes how to map HTTP request methods and paths to the
code that will handle them. It borrowed route matching ideas from
[Ruby on Rails](http://rubyonrails.org/), and relied on the fact that Ruby uses
the last value of a block as the return value.

As some simple examples:

```ruby
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
```

The language is expressive, and allows the developer to focus on two things:

- What are the specific entry points (URIs) for the application?
- What needs to be done for each specific entry point?

I'd argue that the above two points are the defining characteristics of modern
microframeworks. Typically, the entry points are given the term "routing", and
the second corresponds to "controllers".

PHP implementations
-------------------

I'd argue one of the earliest microframework implementations, though it wasn't
termed as such, was [Horde Routes](http://dev.horde.org/routes/)<sup>[3](#f3)</sup>
(which was itself inspired by [Python Routes](http://routes.readthedocs.org/en/latest/index.html),
in turn inspired by the Rails routing system, like Sinatra). It follows the two
principles I outlined above: it allows defining routes (entry points), and
mapping them to controllers. Controllers for Routes are simply classes, and a
route must provide both a controller and an action in the match, with the
latter corresponding to a method on the controller class.

Since around 2009, I've seen an increasing number of new PHP
microframeworks<sup>[4](#f4)</sup> that follow in the steps of Sinatra and
Horde. In the various implementations I've looked at, instead of using a DSL,
the authors have all opted for either a procedural or OOP interface. Starting
with PHP 5.3, most authors have also primarily targetted any PHP callable as a
controller, favoring callbacks specifically. The fundamental ideas remain the
same as Sinatra, however:

```php
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
```

One key difference I've witnessed in the implementations is surrounding how
route matches are passed to the callback. In the examples above, they are
passed as individual arguments to the handler. Some, however, opt for an
approach more like Sinatra, which passes a single "params" argument into the
scope of the handler. This approach tends to be more expedient both from an
implementation standpoint as well as a performance standpoint, as it does not
require reflection to determine name and position of arguments, and makes
handling wildcard arguments simpler. I've seen this latter approach handled
several ways:

```php
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
```

Another difference I've seen is in how route constraints, defaults, and names
are handled. The most elegant solutions usually allow chaining method calls in
order to alter this data:

```php
$app->get('/feed.:format', function ($app) {
    // ...
})->constraints(['format' => '/^(atom|xml|json)$/'])
  ->name('feed');
```

One common feature I've seen is the ability to generate URLs based on the
defined routes. Most commonly, this is a function or method `urlTo()`, which
takes a route name, and an associative array of replacements.

```php
echo $app->urlTo('feed', ['format' => 'atom']);
```

That's it in a nutshell: the ability to match HTTP request methods and path
information, and map it to controllers/handlers, and to generate URLs based on
those present in the application.

What are they good for?
-----------------------

In my research and experience, microframeworks have three typical use cases:

1. **Prototyping.** Because of their simplicity, microframeworks are fantastic
   for prototyping a basic website. Very often, in the early stages of a site, you
   have a limited number of pages, and most often simply need to render a template
   with limited variable substitutions. Microframeworks are a perfect fit for
   this.
2. **APIs**. API needs are usually quite well-defined, and often involve a
   small, finite number of URLs. The logic required is usually already
   encapsulated in business objects, so the application layer is simply for
   filtering and returning a representation. Microframeworks again offer a nice
   fit.
3. **Small, mostly static sites**. Similar to the first point, if you know the
   site will be relatively small and mostly static, then the minimal overhead of a
   microframework is often a good fit.

Where do microframeworks fail?
------------------------------

Because of the rather declarative nature of microframeworks, and the typically
1:1 mapping of a route to a controller, microframeworks do not tend to promote
code re-use. Additionally, this extends to how microframework applications are
organized: usually, there are no clear guidelines on how to organize routes and
controllers, much less separate them into multiple files. This can lead to
maintenance issues as the application grows, as well as logistical issues
whenever you need to add new routes and controllers (do they go at the top, or
bottom? are there other routes that could potentially match as well? etc.).

Additionally, though many frameworks offer ways to alter the workflow of the
application either via hooks, events, or “middleware”<sup>[5](#f5)</sup>, most
of these are limited in scope, often non-reusable, and often non-stackable. As
such, comprehensive manipulation of the application workflow is out of reach.

One other area that is overlooked, however, is one I find curious, particularly
in light of the MicroPHP movement: so much of the underlying plumbing is
basically the same, yet every microframework re-implements it. Specifically:

- Routing is basically the same across most implementations, following the same
  basic specifications outlined in Rails. There are very few differences in the
  public APIs.
- Request and Response object abstraction is largely the same as well,
  providing access to query/post/cookie/session/etc. parameters through roughly
  equivalent APIs.
- Many implement their own view layers.<sup>[6](#f6)</sup>

Most of this code should be considered commodity code at this point. There are
several outstanding view layers and templating engines available (Smarty, Twig,
Savant, `Zend\View`). Standalone routing libraries exist such as Horde Routes,
and even those bundled with frameworks are often available separately via
Composer or Pyrus; the same is true with Request and Response object
abstraction. It seems to me that a few microframework authors should be working
on abstracting these concerns, and then focussing their efforts on
differentiators in their own microframeworks.

An experiment
-------------

Building on my last point, I looked at the APIs of
[Limonade](http://limonade-php.github.com/) and
[Slim Framework](http://www.slimframework.com/), and built up a specification
for a microframework. I then matched as many pieces of it as possible to
existing components in [ZF2](http://packages.zendframework.com/), and started
building.

In a matter of a few hours, I had written up a complete test
suite<sup>[7](#f7)</sup> and all code for a microframework, featuring the
following (this is basically the testdox output from the unit test suite):

- Lazy loads request
- Lazy loads response
- Request is injectible
- Response is injectible
- Halt should raise halt exception
- Response should contain status provided to halt
- Response should contain message provided to halt
- Stop should raise halt exception
- Response should remain unaltered after stop
- Redirect should raise halt exception
- Redirect should set 302 response status by default
- Redirect should set response status based on provided status code
- Redirect should set location header
- Map creates a segment route when provided with a string route
- Map can receive a route object
- Passing invalid route raises exception
- Map can receive a callable
- Passing invalid controller to route does not immediately raise exception
- Accessing invalid controller raises exception
- Passing invalid method to route via method raises exception
- Can set methods route responds to singly
- Can set methods route responds to as array
- Can set methods route responds to as multiple arguments
- Can specify additional method types to respond to
- Can specify route name
- Adding route using method type creates route that responds to that method type
- Running with no matching routes raises page not found exception
- Routing sets list of named routes
- Routing sets lists of routes by method
- Successful routing dispatches controller
- Unsuccessful routing triggers 404 event
- Calling halt triggers halt event
- Invalid controller triggers 501 event
- Exception raised in controller triggers 500 event
- Can pass to next matching route
- Url for helper assembles url based on name provided
- Url for helper assembles url based on name and params provided
- Url for helper assembles url based on current route match when no name provided
- Composes logger instance by default
- Can inject specific logger instance
- Mustache view is used by default
- Can inject alternate view instance
- Render renders a template to the response
- View model returns mustache view model by default
- Subsequent calls to view model return separate instances
- Can provide view model prototype

I utilized ZF2's routing library from its MVC component, the request and
response objects from its HTTP component, its Log component, and the Session
component. These had a few other dependencies, but nothing terribly onerous.

For the view, I used my own [phly_mustache](http://weierophinney.github.com/phly_mustache),
and provided a basic "view model" implementation that receives the application
instance, thus allowing the ability to call application helpers (such as url
generation).

To make installation simple, I used [Composer](http://getcomposer.org) to
manage my dependencies on specific ZF2 components and for `phly_mustache`. The
microframework contains only the code it needs to get its work done, leveraging
the work of others whenever possible.

This post is not meant as a way to announce a new microframework,
however.<sup>[8](#f8)</sup> The point of the experiment was to prove something:
microframeworks are trivially easy to write, *particularly if you follow the
principals of MicroPHP, and re-use existing code*. Just because code comes from
a framework or a third-party library does not make it suspect or inferior; in
fact, whenever possible, you should leverage such code so you can focus on
*writing awesome applications*.

Lessons learned
---------------

I really like microframeworks for specific problems: prototyping, APIs, and
small, simple sites. I think they are ideally suited for these tasks. That
said, I'd love to see some solid libraries targetting the fundamental, shared
aspects of these efforts: routing, request and response abstraction, etc. With
dependency management tools such as Composer and Pyrus, having required
dependencies is not a big deal anymore, and re-use should be encouraged.

Also, writing a microframework is an excellent coding exercise. It helps a
developer appreciate the complexities of abstraction while limiting the number
of moving parts. I highly recommend it as an exercise — but do it using
available components, and be prepared to throw it away and instead collaborate
with others, or adopt something which better solves both the problems you have
and the problems you anticipate.

In sum: *Use the right tool for the job*. If you foresee expanding requirements
in your project's future, you may want to evaluate a full-stack
framework,<sup>[9](#f9)</sup> or consider building something robust that suits
your specific project's needs. Use microframeworks where and when they make
sense.

#### Afterword

I'm well aware that Fabien Potencier has written [a comprehensive series of posts on creating a microframework using Symfony 2 components](http://fabien.potencier.org/article/50/create-your-own-framework-on-top-of-the-symfony2-components-part-1).
I deliberately chose not to read them until (a) ZF2 was almost ready to
release, and (b) I'd had a chance to formulate my own opinions on
microframeworks. They're an excellent read, however, and show a nice
progression of development from flat PHP to a fully functional microframework;
click the link and see for yourself.

#### Footnotes

- <sup>[1](#t1)</sup> In particular, I feel that the movement (a) disparages
  components from larger libraries simply because they originate from a larger
  library, and (b) distrust any code that has additional dependencies. This
  latter I find truly puzzling, as I'd think it fits the idea of “use small
  things that work together to solve larger problems.” If the code solves a
  particular problem and allows you to focus on a larger problem, where it
  originates and the number of dependencies should not be an issue.
- <sup>[2](#t2)</sup> In fact, my first foray into MVC in PHP was writing a
  clone of Perl's [CGI::Application](http://cgi-app.org/), which in many ways
  is also a microframework.
- <sup>[3](#t3)</sup> Trivia: Both authors of Horde Routes worked at Zend when
  I first started at the company, and Mike Naberezny wrote the very first lines
  of code for Zend Framework.
- <sup>[4](#t4)</sup> I swear, you see new ones on Github daily, and on
  [PHP Developer](http://phpdeveloper.org/) at least once a week.
- <sup>[5](#t5)</sup> [Slim](http://www.slimframework.com) has this concept.
  Basically, any callables placed between the route string and the last
  callable when defining a route — i.e., the “middle” arguments, and thus
  middleware — will be executed in order prior to attempting to execute the
  controller.
- <sup>[6](#t6)</sup> [Slim](http://www.slimframework.com) is an outlier here,
  as it utilizes [Twig](http://twig.sensiolabs.org/) by default.
- <sup>[7](#t7)</sup> I'm sure that my TDD experiment will warm the soul of
  [the Grumpy Programmer](http://www.littlehart.net/atthekeyboard/).
- <sup>[8](#t8)</sup> That said, if you want to look at the results, you can
  [find Phlyty on Github](http://github.com/weierophinney/phlyty).
- <sup>[9](#t9)</sup> As you may guess, I'm biased towards [Zend Framework](http://framework.zend.com/).
  However, you should always carefully evaluate a framework against your
  project's needs.
