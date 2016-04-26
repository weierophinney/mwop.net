---
id: 2016-04-26-on-locators
author: matthew
title: 'On Deprecating ServiceLocatorAware'
draft: false
public: true
created: '2016-04-26T12:25:00-05:00'
updated: '2016-04-26T12:25:00-05:00'
tags:
    - patterns
    - php
    - programming
    - 'zend framework'
---
A month or two ago, we pushed a new release of
[zend-mvc](https://zendframework.github.io/zend-mvc) that provides a number of
forwards-compatibility features to help users prepare their applications for the
upcoming v3 release.

One of those was, evidently, quite controversial: in v3, zend-servicemanager no
longer defines the `ServiceLocatorAwareInterface`, and this particular release
of zend-mvc raises deprecation notices when you attempt to inject a service
locator into application services, or pull a service locator within your
controllers.

The arguments go something like this:

- "Dependency injection is too hard to understand!"
- "This feature simplifies development!"
- "If this is so bad, why was it in there in the first place?"

These are usually followed by folks:

- saying they'll switch frameworks (okay, I guess?);
- asking for re-instatement of the feature (um, no);
- asking for removal of the deprecation notices (why? so you can delay your pain
  until upgrading, when you'll ask for re-instatement of the feature?); or
- asking for a justification of the change.

So, I've decided to do the last, justify the change, which addresses the reasons
why we won't do the middle two, and addresses why the assumptions and assertions
about `ServiceLocatorAware`'s usefulness are mostly misguided.

<!--- EXTENDED -->

> ## Originally posted elsewhere
>
> This was originally posted as a comment on an issue. I've decided to post it
> to my blog to reach a larger audience, and to provide a bit more background
> and detail.

The intent of zend-servicemanager is for use as an
[Inversion of Control](https://en.wikipedia.org/wiki/Inversion_of_control) container.

It was never intended as a general purpose [service locator](https://en.wikipedia.org/wiki/Service_locator_pattern)
(interestingly, that link details mostly disadvantages to the pattern!); that
role was something foisted onto it in the spirit of "rapid application
development" and to "simplify initial development," but the intention even there
was that, once a class has stabilized, you should refactor to inject
dependencies. (And we all know what happens with busy developers: refactoring is
put off or never occurs.)

Why shouldn't you inject a service locator?

[Google for "service locator anti pattern"](https://www.google.com/search?q=service%20locator%20anti%20pattern)
to get an idea of why it shouldn't be used. The main points boil down to:

- Dependency hiding.
- Error indirection.
- Type safety.
- Brittleness.

Let's look at each of these individually.

## Dependency hiding

What is meant by "dependency hiding?"

Take a look at this class signature:

```php
class Foo implements DispatchableInterface, ServiceLocatorAwareInterface
{
    /* Defined by DispatchableInterface */
    public function dispatch(Request $request, Response $response);

    /* Defined by ServiceLocatorAwareInterface */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
    public function getServiceLocator();
}
```

Based on that, you'd expect:

- that you can instantiate the object with no dependencies.
- if you feel the need to, you *could* pass a service locator to the instance.
- you should be able to execute `dispatch()` by passing it a request and
  response instance, and it should successfully return.

The service locator is nebulous; its purpose isn't clear, and it's clearly not a
*required* dependency, as it's in a setter method.

So, you go and write a test for the `dispatch()` method, and you get a
`ServiceNotFoundException`. What's wrong?

You dive into the code of the `dispatch()` method:

```php
public function dispatch(Request $request, Response $response)
{
    $authentication = $this->serviceLocator->get('authentication');

    if (! $authentication->hasIdentity()) {
        $response->setStatus(401);
        return $response;
    }

    $identity = $authentication->getIdentity();
    $response->setBody(
        $this->serviceLocator->get('renderer')->render(
            'foo',
            ['identity' => $identity]
        )
    );
    return $response;
}
```

There's two possible places that `ServiceNotFoundException` may have been
thrown: on the first line of the method, or within the `setBody()` call. In both
cases, you're faced with a conundrum:

- You now know that the service locator is *required*. That wasn't obvious from
  looking at the class originally; it appeared to be an *optional* dependency.
- You have no idea what instance types are expected for each of the
  `authentication` and `renderer` services.

This latter is particularly troubling. You now have to understand all the
various locations within the application that services may be defined, and start
hunting through those. Chances are, you'll discover that those service names may
actually be *aliases*, which means you'll determine what it aliases to, but then
have to re-start your search to determine what the actual service is.

That, in a nutshell, is dependency hiding. The *requirements* for operation of
the class are hidden *within* the code, and the types cannot necessarily be
inferred.

(Sure, you could add annotations above the `get()` calls to detail the types.
But that's a band-aid; you still need to look in the code itself to determine
what the requirements are.)

One side effect of dependency hiding is that *it makes testing more difficult*.
I think the example above illustrates that; you cannot look at the signatures
alone to get an idea of behavior and requirements, but need to dig into the
code. Additionally, test setup becomes more difficult, and more brittle, as
you're now required to add a dependency on a container, populate the container,
and hope you didn't miss something. I'll speak more on this later. The point is:
anything that makes testing more difficult means developers will avoid testing,
and that decreases code quality.

Let's break it down a bit more:

- You want a particular object instance.
- You are now coupled to the service locator for retrieving the instance.
- You retrieve the instance from the service locator via a string name, which
  could be *anything*, and not necessarily indicative of *purpose* or its
  *capabilities*.
- That retrieval *may raise exceptions* unrelated to the component being used,
  which you need to account for either in your code or when debugging later.

All you really want is the object instance. *Why not just inject that instance
in the constructor?* Defining the requirements *as constructor arguments* makes
them explicit, and ensures somebody looking solely at the API understands what
is required for operation.

**tl;dr**: You want the dependency you're consuming, not the three steps of
indirection required to get at it. Make all dependencies required, and inject
them in the constructor.

## Error indirection

Re-using the example from above, let's examine the fact that we got a
`ServiceNotFoundException`. This is happening *at runtime*. Essentially, the
work of bootstrapping, routing, instantiating the controller, and pre-dispatch
listeners have already run, only to fail once we get to the actual logic
requested *because a dependency was missing*.

In a typical PHP application workflow, this is not much different from if the
dependencies were directly injected. But if you consider usage in a system such
as [React](http://reactphp.com), where bootstrapping the application can occur
once, and dispatch happens over and over again, it's quite problematic; it's in
essence a runtime exception, *due to misconfiguration*.  This is quite difficult
to trace, and not something you want to have happen in production.

## Type safety

Again, going back to the original example: we don't know what the types expected
are, *nor can we guarantee that what we pull from the container will be correct.*

An inexperienced developer, or one not familiar with all the use cases for a
given instance in a container, could map the service to an unexpected class.
You won't know until runtime, in production, that this has occurred,
when you suddenly get "method does not exist" fatal PHP errors. These are
difficult to track down, as you will not know what the type is, what was
expected, nor where the instance is originally defined. It will take several
steps through the debugger to determine that it's due to a misconfigured
container.

Compare this to dependencies declared in the constructor:

```php
class Foo implements DispatchableInterface
{
    public function __construct(
        AuthenticationService $authentication,
        RendererInterface $renderer
    );

    /* Defined by DispatchableInterface */
    public function dispatch(Request $request, Response $response);
}
```

You'll still get a fatal error, but you'll know that the class was being
instantiated with an invalid argument from the beginning, and know that you need
to check your mappings and/or factories. This type of problem can often be found
with static analysis tools, giving another way for you to help improve your code
quality easily.

Another aspect of this is that your IDEs will now also be able to assist you in
understanding what methods are available. Because the property is injected in
the constructor, the *static analysis* (I'm using that word again!) built-in to
most IDEs will be able to infer the type when you access it in your code, and
give you type hinting. This is not universally true with service locators (I
understand PHPStorm is making some headway on this, but I also know it's a very
difficult task to accomplish, and error prone).

## Brittleness

Relying on a service locator introduces brittleness into your designs.

Every time you add a call to `get()`, you're introducing a new dependency. This
often breaks tests:

- If you're mocking the service locator, you now have additional calls to its
  methods that may be called in the course of a test, making the mock fail its
  assertions.
- If you're using a concrete locator instance, and an instance is expected to be
  present, you now get exceptions raised over the course of test execution.

This sort of brittleness leads to developers not wanting to test, making the
code more brittle and more likely to break in unexpected ways in the future.
_**Any practice that makes testing more difficult should be reconsidered.**_

Additionally, it leads to *undocumented requirements*, making it less clear for
a consumer to know what services need to be present for the code to work. When
you work across teams, this is critical.

Another aspect of using a service locator is that it's very easy for your class
to grow to span too many responsibilities. Let me explain.

One argument often used in favor of using a service locator is to facilitate
*optional* dependencies: dependencies that are only used during specific paths
of code execution. If the dependency is particularly heavy (web services,
database access, etc.), the argument is that it makes sense to pull these from
the container only if they're about to be used.

There are two ways to address this:

- zend-servicemanager (and several other IoC implementations) already offers
  [lazy services](http://zendframework.github.io/zend-servicemanager/lazy-services/),
  which solve the problem by creating a proxy class that wraps the factory for
  retrieving the service. You interact with it just as you would the original
  instance, but the "heavy instantiation" is delayed until first use.
- Split your concerns into multiple classes! This is the better solution
  anyways; if you know certain dependencies are only in certain code paths,
  create a new controller for that path, and route specifically to it. As an
  example, if you know that database access will only occur on (a) POST requests
  to the service, and (b) when validation occurs, then:
    - Create a controller that maps specifically to POST requests for the given
      path, and
    - optionally, wrap the database connection as a lazy service. Chances are,
      though, that if you get the request routed to that specific controller,
      having the database access ready will be acceptable performance-wise.

**tl;dr**: dependence on service locators leads to brittle design and scope
creep. When you pay attention to dependencies, you end up splitting concerns
into multiple classes, making them easier to test and maintain.

## There *are* valid use cases

Service locators have some valid use cases. When you have a number of related
instances, and pulling them at runtime will be based on input, a service locator
is ideal. This scenario includes things like:

- plugin and helper systems
- strategy patterns
- routing systems

In these cases, however, we're not dealing with general application
dependencies; we're working with specific contexts, and the instances pulled
work in that context.

In many cases, even these could be directly injected. If you *know* your code
paths include specific plugins or helpers, you can also inject those. (We've
done this with several Apigility controllers, as it has simplified testing!)

## Takeaways

As a general-purpose way of getting dependencies, service locators are an
anti-pattern at best, and lead to quality decline and brittle architecture.

We introduced the `ServiceLocatorAwareInterface` due to pressure from users who
wanted "rapid application development" features, and who were unclear about the
benefits of dependency injection. At the time, *it seemed like a good idea*; we
were listening and responding to our users.

However, with the benefit of hindsight, I think we made a mistake when we did
so, and ultimately did a disservice to our users; the implementation promotes
bad habits and reduces code quality for those who rely on the pattern. Hopefully
the discussion above sheds more light on why we've finally decided to remove it,
and how we feel the removal will help you improve your code.
