---
id: 199-A-Simple-PHP-Publish-Subscribe-System
author: matthew
title: 'A Simple PHP Publish-Subscribe System'
draft: false
public: true
created: '2008-12-15T10:26:00-05:00'
updated: '2008-12-31T07:05:35-05:00'
tags:
    - php
    - dojo
---
I've been playing a lot with [Dojo](http://dojotoolkit.org/) lately, and have
been very impressed by its elegant publish-subscribe system. Basically, any
object can publish an event, and any other object can subscribe to it. This
creates an incredibly flexible notification architecture that's completely
opt-in.

The system has elements of Aspect Oriented Programming (AOP), as well as the
Observer pattern. Its power, however, is in the fact that an individual object
does not need to implement any specific interface in order to act as either a
Subject or an Observer; the system is globally available.

Being a developer who recognizes good ideas when he sees them, of course I
decided to port the idea to PHP. You can see the results
[on github](http://github.com/weierophinney/phly/tree/master/Phly_PubSub).

<!--- EXTENDED -->

Usage is incredibly simple: an object publishes an event, which triggers all
subscribers.

Probably the most illustrative solution would be for optionally logging. Say
for instance that you create a logger instance in your application bootstrap;
you could then subscribe it to all "log" events:

```php
$log = new Zend_Log(new Zend_Log_Writer_Stream('/tmp/application.log'));
Phly_PubSub::subscribe('log', $log, 'info');
```

Then, in your code, whenever you might want to log some information, simply
publish to the "log" topic:

```php
Phly_PubSub::publish('log', 'Log message...');
```

In production, you could simply comment out the log definition and
subscription, disabling logging throughout the application. Events that publish
to topics without subscribers simply return early — meaning no ramifications
for code that uses the system. You could then enable the logger at will when
you need to debug or determine what events are triggering.

As another example, consider a model that has a `save` method. You may want to
log the data sent to it, as well as the id returned. Additionally, you may want
to update your search index and caches once the item has been saved to your
persistence store.

Your model's `save` method might then look like this:

```php
class Foo
{
    public function save(array $data)
    {
        Phly_PubSub::publish('Foo::save::start', $data, $this);

        // ...

        Phly_PubSub::publish('Foo::save::end', $id, $this);
        return $id;
    }
}
```

Elsewhere, you may have defined your logger, indexer, and cache. Where those
are defined, you would tell them what topics you're subscribing each to.

```php
Phly_PubSub::subscribe('Foo::save::start', $logger, 'logSaveData');
Phly_PubSub::subscribe('Foo::save::end', $logger, 'logSaveId');
Phly_PubSub::subscribe('Foo::save::end', $cache, 'updateFooItem');
Phly_PubSub::subscribe('Foo::save::end', $index, 'updateFooItem');
```

The beauty of the approach is the simplicity: Foo doesn't need to implement
its own pub/sub interface — in fact, if Foo already existed in your
application, you could drop in this functionality trivially. On the other side
of the coin, if you have no subscribers to the events, there are no drawbacks.

Some places it could be improved:

- The ability for return values could be useful, to allow interruption of
  method execution or to modify arguments sent by the publisher. However, since
  each topic may have multiple handlers, a simple interface would be difficult
  to achieve.
- Exception handling. In most cases, you probably don't want method execution
  to halt due to a subscriber raising an exception. However, you still need
  some way to report such errors.

I'm excited to see what uses *you* may be able to put this to; drop me a line
if you start using it!

**Update (2008-12-30):** Based on some of the comments to this post, I created
`Phly_PubSub_Provider`, which is a non-static implementation that can be
attached to individual classes — basically providing a per-object plugin
system. Usage is as follows:

```php
class Foo
{
    protected $_plugins;

    public function __construct()
    {
        $this->_plugins = new Phly_PubSub_Provider();
    }

    public function getPluginProvider()
    {
        return $this->_plugins;
    }

    public function bar()
    {
        $this->_plugins->publish('bar');
    }
}

$foo = new Foo();

// Subscribe echo() to the 'bar' event:
$foo->getPluginProvider()->subscribe('bar', 'echo');

$foo->bar(); // echo's 'bar'
```
