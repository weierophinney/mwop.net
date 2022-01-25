---
id: 2022-01-25-openswoole-webhooks
author: matthew
title: 'Exposing webhooks via mezzio-swoole'
draft: false
public: true
created: '2022-01-25T08:32:00-06:00'
updated: '2022-01-25T08:32:00-06:00'
tags:
    - mezzio
    - openswoole
    - php
    - swoole
    - webhooks
---

I was first introduced to the concept of webhooks via [a 2009 blog post by John Herren](https://jhherren.wordpress.com/2009/03/05/twitter-and-the-case-for-web-hooks/), a former colleague at Zend.
At the time, they were in their infancy; today, they're ubiquituous, as they provide a mechanism for a service to notify interested parties of events.
This saves traffic; instead of consumers polling an API for event changes, the service notifies them directly.
It also means that the consumer does not need to [setup things like cronjobs](/blog/2022-01-21-openswoole-timer-cron.html); they instead setup a webhook endpoint, register it with the service provider, and their application takes care of the rest.

The thing is, _handling_ a webhook can often lead to additional processing, and you are expected to send an immediate response to the provider indicating you received the event.

How can you achieve this?

<!--- EXTENDED -->

### Offloading processing

It's likely [no secret that I'm a fan of Mezzio and OpenSwoole](/blog/tag/swoole)<sup><a id="footnote-1-src" href="#footnote-1">1</a></sup>.
Running PHP in a persistent process forces me to think about state in my applications, which in turn generally forces me to be more careful and explicit in how I code things.
On top of that, I get the benefit of persistent caching, better performance, and more.

One feature I pushed into [mezzio-swoole (the Swoole and OpenSwoole bindings for Mezzio)](https://docs.mezzio.dev/mezzio-swoole/) was functionality for working with swoole [task workers](https://openswoole.com/docs/modules/swoole-server-task).
There's a variety of ways to use the functionality, but my favorite is by using a [PSR-14 EventDispatcher](https://www.php-fig.org/psr/psr-14/) to dispatch an event to which I attach deferable listeners.

What does that look like?

Let's say I have a `GitHubWebhookEvent`, for which I have associated a `GitHubWebhookListener`<sup><a id="footnote-2-src" href="#footnote-2">2</a></sup> in my event dispatcher.
I would dispatch this event as follows:

```php
/** @var GitHubWebhookEvent $event */
$dispatcher->dispatch($event);
```

The nice part about this is that the code dispatching the event does not need to know how the event is processed, or even when.
It just dispatches the event and moves on.

To make the listener _deferable_, in [Mezzio](https://docs.mezzio.dev/) applications, I can associate a special [delegator factory](https://docs.mezzio.dev/mezzio/v3/features/container/config/#delegator-factories) provided by the mezzio-swoole package with the listener.
This is done with standard Mezzio dependency configuration:

```php
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;

return [
    'dependencies' => [
        'delegators' => [
            GitHubWebhookListener::class => [
                DeferredServiceListenerDelegator::class,
            ],
        ],
    ],
];
```

This approach means that my listener can have any number of dependencies, and be wired into the container, but when I request it, I'll be returned a `Mezzio\Swoole\Task\DeferredServiceListener` instead.
This class will create a swoole task from the listener and event, which defers execution to the task workers, offloading it from the web workers.

> #### Event state
>
> Task workers receive a _copy_ of the event, not the original instance.
> Any state changes your listener makes in the event instance will not be reflected in the instance present in your web workers.
> As such, you should only defer listeners that do not communicate state back to the dispatching code via the event.

> #### Sharing an event dispatcher with the web server
>
> mezzio-swoole defines a marker interface, `Mezzio\Swoole\Event\EventDispatcherInterface`.
> This interface is used to define an event-dispatcher service consumed by `Mezzio\Swoole\SwooleRequestHandlerRunner` for the purpose of dispatching swoole HTTP server events, getting around the "one event, one handler" rule swoole follows.
> However, that can mean that you end up with two different dispatchers in your application: one used by the swoole web server, and one by the application, and that means you cannot delegate tasks.
>
> To get around this, alias the `Mezzio\Swoole\Event\EventDispatcherInterface` service to the `Psr\EventDispatcher\EventDispatcherInterface` service:
>
> ```php
> use Mezzio\Swoole\Event\EventDispatcherInterface as SwooleEventDispatcher;
> use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcher;
>
> return [
>     'dependencies' => [
>         'alias' => [
>             SwooleEventDispatcher::class => PsrEventDispatcher::class,
>         ],
>   ],
> ];
> ```
>
> Then make sure that any listener providers used with your event dispatcher include the following mappings (all classes are in the `Mezzio\Swoole\Event` namespace):
>
> - `ServerStartEvent` maps to `ServerStartListener`
> - `WorkerStartEvent` maps to `WorkerStartListener`
> - `RequestEvent` maps to `StaticResourceRequestListener`
> - `RequestEvent` maps to `RequestHandlerRequestListener`
> - `ServerShutdownEvent` maps to `ServerShutdownListener`
> - `TaskEvent` maps to `TaskInvokerListener`
>
> As an example, using my [phly/phly-event-dispatcher package](https://github.com/phly/phly-event-dispatcher):
>
> ```php
> /** @var Phly\EventDispatcher\AttachableListenerProvider $provider */
> $provider->listen(ServerStartEvent::class, $container->get(ServerStartListener::class));
> $provider->listen(WorkerStartEvent::class, $container->get(WorkerStartListener::class));
> $provider->listen(RequestEvent::class, $container->get(StaticResourceRequestListener::class));
> $provider->listen(RequestEvent::class, $container->get(RequestHandlerRequestListener::class));
> $provider->listen(ServerShutdownEvent::class, $container->get(ServerShutdownListener::class));
> $provider->listen(TaskEvent::class, $container->get(TaskInvokerListener::class));
> ```

### Offloading processing via webhooks

What this means is you can write a handler for a webhook that receives a payload, creates an event from that payload, dispatches the event, and immediately returns a response.

As a simple example, let's say that the webhook event will take just the request content in its entierty:

```php
declare(strict_types=1);

namespace App;

class WebhookEvent
{
    public function __construct(
        public readonly string $requestContent,
    ) {
    }
}
```

Our webhook would then create an event with content from the request, dispatch it, and return a 204 (empty) response, indicating success:

```php
declare(strict_types=1);

namespace App;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AtomHandler implements RequestHandlerInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new WebhookEvent((string) $request->getBody()));

        return $this->responseFactory->createResponse(204);
    }
}
```

GitHub gets an immediate 204 response back, indicating we've accepted the payload, and a task worker is delivered the payload to process when it gets a chance.

I like this approach, as it keeps the web logic minimal and simple, while giving me the power to process the webhook event with all the tools at my disposal.

> #### Validation
>
> You will want to make sure you validate your payload before doing any actual processing.
> You can do that in the handler if desired, and return a 4xx error if needed.
> My experience, however, is that most service providers that use webhooks don't do anything with such errors, other than potentially stop sending payloads after a series of such responses.
> As such, I usually put validation into my listeners, where I can log problems and then follow-up on them later.

### Other considerations

- Many services will use a _shared secret_ when sending webhooks.
  This might be used to generate a signature that is sent in the header, or even just a header value that indicates the payload came from them.
  I put such validation into [middleware](https://docs.mezzio.dev/mezzio/v3/features/middleware-types/#psr-15-middleware), as it (a) becomes reusable in scenarios where the secret is the same, or where I might have multiple webhooks registered for different events from the same provider.
  Mezzio makes it possible to add middleware when defining a route, ensuring that the middleware only gets triggered when it's needed:

  ```php
  $app->post('/api/github/release', [
      GitHubWebhookValidationMiddleware::class, // validation middleware
      GitHubReleaseWebhookHandler::class,       // webhook handler
  ], 'webhook.github.release');
  ```

- You'll want to manage errors gracefully for your webhook endpoints.
  Even though there's not much code in the handler, another listener might raise an exception, or some of your middleware might (see above point).
  I recommend putting the [mezzio-problem-details middleware](https://docs.mezzio.dev/mezzio-problem-details/) in your webhook handler's pipeline:

  ```php
  $app->post('/api/github/release', [
      \Mezzio\ProblemDetails\ProblemDetailsMiddleware::class,
      GitHubWebhookValidationMiddleware::class, // validation middleware
      GitHubReleaseWebhookHandler::class,       // webhook handler
  ], 'webhook.github.release');
  ```

- Similarly, your listener should let you know when there are errors.
  The best way to do that is via logging, or via any monitoring APIs you may be using in your application.

### Footnotes

- <sup><a href="#footnote-1-src" id="footnote-1">1</a></sup> I'll refer to the two projects collectively as "swoole" throughout the document.
- <sup><a href="#footnote-2-src" id="footnote-2">2</a></sup> PSR-14 defines a `ListenerProviderInterface` from which event dispatchers can optionally retrieve listeners associated with the dispatched event.
  Wiring these is up to the application developer; PSR-14 libraries generally provide these mechanisms.
