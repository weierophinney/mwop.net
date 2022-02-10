---
id: 2022-01-21-openswoole-timer-cron
author: matthew
title: 'Running cronjobs via an Openswoole timer'
draft: false
public: true
created: '2022-01-21T09:16:00-06:00'
updated: '2022-02-10T14:55:00-06:00'
tags:
    - mezzio
    - openswoole
    - php
    - swoole
image:
    url: https://live.staticflickr.com/7030/6812481635_ed463ae1fa_b.jpg
    creator: 'photosteve101'
    attribution_url: https://www.flickr.com/photos/42931449@N07/6812481635
    alt_text: 'Business Calendar & Schedule'
    license: 'BY'
    license_url: https://creativecommons.org/licenses/by/2.0/
---

Sites I build often utilize cronjobs to periodically pull in data from other sources.
For example, I might want to poll an API once a day, or scrape content from another website once a month.
Cronjobs are a perfect fit for this.

However, cron has a few problems:

- If the job is writing information into the file tree of your web application, you need to ensure permissions are correct, both at the filesystem level, and when writing the cronjob (e.g., running it as the same user, or changing permissions on completion).
- If you are running console tooling associated with your PHP application, you may need to worry about whether or not particular environment variables are in scope when you run the job.
- In containerized environments, usage of cron is strongly discouraged, as it means running another daemon.
  You can get around this with tools such as the [s6-overlay](https://github.com/just-containers/s6-overlay), but it's another vector for issues.

Since most sites I build anymore use [mezzio-swoole](https://docs.mezzio.dev/mezzio-swoole/), I started wondering if I might be able to handle these jobs another way.

<!--- EXTENDED -->

### Task workers

We introduced integration with Swoole's [task workers](https://openswoole.com/docs/modules/swoole-server-task) in version 2 of mezzio-swoole.
Task workers run as a separate pool from web workers, and allow web workers to offload heavy processing when the results are not needed for the current request.
They act as a form of per-server message queue, and are great for doing things such as sending emails, processing webhook payloads, and more.

The integration in mezzio-swoole allows you to decorate [PSR-14 EventDispatcher](https://www.php-fig.org/psr/psr-14/) listeners in mezzio-swoole `Mezzio\Swoole\Task\DeferredListener` or `DeferredServiceListener` instances; when that happens, the decorator creates a task with the Swoole server, giving it the actual listener and the event.
When the schedule process the task, it then calls the listener with the event.

The upshot is that to create a task, you just dispatch an event from your code.
Your code is thus agnostic about the fact that it's being handled asynchronously.

However, because tasks work in a separate pool, this means that the event instances they receive are technically _copies_ and not references; as such, your application code cannot expect the listener to communicate event state back to you.
If you choose to use this feature, only use it for fire-and-forget events.

I bring all this up now because I'm going to circle back to it in a bit.

### Scheduling jobs

Swoole's answer to scheduling jobs is its [timer](https://openswoole.com/docs/modules/swoole-timer).
With a timer, you can _tick_: invoke functionality each time a period has elapsed.
Timers operate within event loops, which means every server type that Swoole exposes has a `tick()` method, including the HTTP server.

The obvious answer, then, is to register a tick:

```php
// Intervals are measured in milliseconds.
// The following means "every 3 hours".
$server->tick(1000 * 60 * 60 * 3, $callback);
```

Now I hit the problems:

- How do I get access to the server instance?
- What can I specify as a callback, and how do I get it?

With mezzio-swoole, the time to register this is when the HTTP server starts.
Since Swoole only allows one listener per event, mezzio-swoole composes a PSR-14 event dispatcher, and registers with each Swoole HTTP server event.
The listeners then trigger events via the PSR-14 event dispatcher, using custom event types internally that provide access to the data originally passed to the Swoole server events.
This approach allows the application developer to attach listeners to events and modify how the application works.

To allow these "workflow" events to be separate from the application if desired, we register a `Mezzio\Swoole\Event\EventDispatcherInterface` service that returns a discrete PSR-14 event dispatcher implementation.
I generally alias this to the PSR-14 interface, so I can use the same instance for application events.

I use my own [phly/phly-event-dispatcher](https://github.com/phly/phly-event-dispatcher) implementation, which provides a number of different _listener providers_.
The easiest one is `Phly\EventDispatcher\AttachableListenerProvider`, which defines a single `listen()` method for attaching a listener to a given event class.

On top of that, Mezzio and Laminas have a concept of [delegator factories](https://docs.mezzio.dev/mezzio/v3/features/container/config/#delegator-factories).
These allow you to "decorate" the creation of a service.
One use case is to decorate the `AttachableListenerProvider` service, and call its `listen()` method to attach listeners.

This is the long-winded explanation for what comes next: a delegator factory on `AttachableListenerProvider` that registers a listener on `Mezzio\Swoole\Event\ServerStartEvent` that in turn registers a tick to run a job pulled from the container:

```php
namespace Mwop;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class RunPeriodicJobDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $provider->listen(
            ServerStartEvent::class,
            function (ServerStartEvent $e) use ($container): void {
                $e->getServer()->tick(
                    1000 * 60 * 60 * 3,
                    $container->get(SomeJobRunner::class),
                );
            },
        );

        return $provider;
    }
}
```

I then would attach this to the `AttachableListenerProvider` via configuration:

```php
use Mwop\RunPeriodicJobDelegatorFactory;
use Phly\EventDispatcher\AttachableListenerProvider;

return [
    'dependencies' => [
        'delegators' => [
            AttachableListenerProvider::class => [
                RunPeriodicJobDelegatorFactory::class,
            ],
        ],
    ],
];
```

This is... fine.
However, I ran into a scenarios almost immediately where this approach caused a segfault in the application, bringing down the server.

And that's where the tasks come back into play.

I modified the above example to now dispatch an event instead.

```php
namespace Mwop;

use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\AttachableListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class RunPeriodicJobDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $provider->listen(
            ServerStartEvent::class,
            function (ServerStartEvent $e) use ($container): void {
                // This is done in the listener to prevent a race condition!
                $dispatcher = $container->get(EventDispatcherInterface::class),

                $e->getServer()->tick(
                    1000 * 60 * 60 * 3,
                    function () use ($dispatcher): void {
                        $dispatcher->dispatch(new SomeJob());
                    }
                );
            },
        );

        return $provider;
    }
}
```

This approach requires a bit more work.
I need to now also register a listener for the `SomeJob` event, **and** I need to configure the listener to be _deferable_.

First, let's create a delegator to attach that listener; it will look a lot like the previous examples:

```php
namespace Mwop;

use Phly\EventDispatcher\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class SomeJobRunnerDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $provider->listen(
            SomeJob::class,
            // Since listeners are invokables, we can likely use the same class as previously
            $container->get(SomeJobRunner::class)
        );

        return $provider;
    }
}
```

Now for the wiring.
We'll register both delegator factories with the `AttachableListenerProvider`, but we will _also_ register a delegator factory for our `SomeJobRunner` class:

```php
return [
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\RunPeriodicJobDelegatorFactory;
use Phly\EventDispatcher\AttachableListenerProvider;

return [
    'dependencies' => [
        'delegators' => [
            AttachableListenerProvider::class => [
                RunPeriodicJobDelegatorFactory::class,
                SomeJobRunnerDelegatorFactory::class,
            ],
            SomeJobRunner::class => [
                DeferredServiceListenerDelegator::class,
            ],
        ],
    ],
];
```

This outlines why delegator factory configuration maps to _arrays_ instead of _class names_: so you can run more than one per service.
When we request our `AttachableListenerProvider` service, its factory will be passed to the first delegator, and the return value of that delegator passed to the next, and so on.
The result here is that we end up registering both of our listeners with it.

The second registration is a fun one.
The `DeferredServiceListenerDelegator` registers a `Mezzio\Swoole\Task\ServiceBasedTask` that incorporates the service name and the container.
When invoked, it passes the event instance provided to it to the task instance.
When the task is invoked, it pulls the listener from the container, and then calls it with the event.

The end result is that by dispatching an event in our tick handler, we effectively push execution into our task workers, ensuring we don't waste precious web workers on handling the periodic event.

### Scheduling jobs

The problem I saw with this approach is that it required adding a tick every time I want to create a new periodic job.
On top of that, I have no control over _when_ it would execute, only how frequently.
Say what you want about cron, but it _does_ understand how to schedule for specific times.

So, I grabbed [Chris Tankersley's cron-expression package](https://github.com/dragonmantank/cron-expression).
This excellent package allows you to pass a cron schedule string to it, and it will then let you know:

- If it is a valid schedule.
- If it's due to run at the given time (defaulting to "now").

With this in hand, I could create a generalized tick.

I decided that my configuration would be in the following format:

```php
[
    'jobs' => [
        'job name' => [
            'schedule' => 'crontab expression',
            'event'    => 'event class name',
        ],
    ],
]
```

From there, I created a `Cronjob` class that had properties for the schedule and event class:

```php
namespace Mwop;

class Cronjob
{
    public function __construct(
        public readonly string $schedule,
        public readonly string $eventClass,
    ) {
    }
}
```

and one representing the entire crontab:

```php
namespace Mwop;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function count;

class Crontab implements Countable, IteratorAggregate
{
    /** @var Cronjob[] */
    private array $jobs = [];

    public function count(): int
    {
        return count($this->jobs);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->jobs);
    }

    public function append(Cronjob $job): void
    {
        $this->jobs[] = $job;
    }
}
```

A cron event interface would allow me to instantiate events to listen to, and give me access to the timestamp if needed:

```php
namespace Mwop;

use DateTimeInterface;

interface CronEventInterface
{
    public static function forTimestamp(DateTimeInterface $timestamp): self;

    public function timestamp(): DateTimeInterface;
}
```

A config parser will validate the various entries, logging and omitting any that are invalid.
I'm not showing that code, as it's fairly verbose, and easy to create on your own.

With those changes, I could now update my delegator to be more general:

```php
namespace Mwop;

use Cron\CronExpression;
use DateTimeImmutable;
use Mezzio\Swoole\Event\ServerStartEvent;
use Phly\EventDispatcher\AttachableListenerProvider;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class RunPeriodicJobDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): AttachableListenerProvider {
        /** @var AttachableListenerProvider $provider */
        $provider = $factory();

        $config = $container->get('config')['cron']['jobs'] ?? [];

        /** @var Crontab $crontab */
        $crontab = (new ConfigParser())($config, $container->get(LoggerInterface::class));

        // Do not register if there are no jobs!
        if (0 === $crontab->count()) {
            return $provider;
        }

        $provider->listen(
            ServerStartEvent::class,
            function (ServerStartEvent $e) use ($container, $crontab): void {
                // This is done in the listener to prevent a race condition!
                $dispatcher = $container->get(EventDispatcherInterface::class),

                $e->getServer()->tick(
                    1000 * 60, // every minute
                    function () use ($dispatcher, $crontab): void {
                        $now = new DateTimeImmutable('now')
                        foreach ($crontab as $job) {
                            $cron = new CronExpression($job->schedule);
                            if (! $cron->isDue($now)) {
                                continue;
                            }
                            $dispatcher->dispatch(($job->eventClass)::forTimestamp($now));
                        }
                    }
                );
            },
        );

        return $provider;
    }
}
```

From there, I could configure jobs:

```php
namespace Mwop;

return [
    'cron' => [
        'jobs' => [
            'some-job' => [
                'schedule' => '*/15 * * * *',
                'event'    => SomeJob::class,
            ],
        ],
    ],
];
```

> In the final version I extracted a callable class to register with the tick, but still pull that service from the container only within the anonymous function serving as the `ServerStartEvent` listener, in order to prevent a race condition from trying to pull the event dispatcher service, which then requires the listener providers... which in turn require the dispatcher.
> You can see where that's going.

This approach works brilliantly!

By running the tick every minute, I can evaluate if there are any cronjobs that should run, and, if so, dispatch them.
Since I configure the listeners to run as tasks, they are offloaded into the task worker queue, so that my web workers to not block on them.
Because this is running in the same process group, I don't have to worry about permissions, and the environment is exactly the same as the web workers.
In many ways, it ends up being a more robust solution than using cron.

### Takeaways

Over the years, I've seen a number of solutions to running cronjobs from PHP applications.
It's not uncommon for frameworks and PHP applications to include functionality to periodically run cronjobs after flushing buffers to the webserver.
The key benefits they have is that they share the same environment and permissions as the web server &mdash; which is typically useful for application-related jobs &mdash; and they don't require a separate daemon be present on the webserver.
However, I've tended to steer away from these, as they rely on the idea that your website is getting regular traffic, and they tie up web worker processes (whether those are mod_php or php-fpm).

Having the ability to offload these to a separate worker pool entirely erases that objection for me.
If all task workers are busy, the task will be processed once they work through the queue.
And no incoming requests will be blocked by this queue or the cronjob itself once it processes.

There _is_ added complexity to the application.
However, by abstracting the cron runner, adding new cronjobs becomes:

- Creating a custom event type.
- Creating a listener for that event that does the work.
- Registering the listener with a listener provider.
- Configuring the listener such that it will be deferred.
- Adding configuration detailing the schedule and the event.

I don't have to worry about whether or not I'm running the job as the correct user, whether or not the user has a login shell (web worker users often do not, which adds complexity to setting up your cronjob), whether or not the cronjob operates with the same environment as the application, and so on.
And those last three items are trivial dependency and configuration wiring, so long as they're documented.

I'm still testing the functionality, but plan to either propose it to mezzio-swoole, or create a package for it.
Since mezzio-swoole is an ideal target for containerized applications, having this functionality will be a nice feature for those who want to provide scheduled jobs with their applications.
