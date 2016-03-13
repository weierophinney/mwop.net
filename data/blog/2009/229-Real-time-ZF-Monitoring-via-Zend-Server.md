---
id: 229-Real-time-ZF-Monitoring-via-Zend-Server
author: matthew
title: 'Real-time ZF Monitoring via Zend Server'
draft: false
public: true
created: '2009-12-10T15:51:22-05:00'
updated: '2009-12-17T10:49:20-05:00'
tags:
    - php
    - 'zend framework'
    - 'zend server'
---
When keeping tabs on your ZF applications, it's often difficult to separate
application errors from general PHP errors, and if you aggregate them in the
same location as your web server errors, this can become more difficult still.

Additionally, PHP's error reporting doesn't provide a ton of context, even when
reporting uncaught exceptions — typically you'll only get a cryptic exception
message, and what file and line emitted it.

Zend Server's Monitor extension has some capabilities for providing more
context, and does much of this by default: request and environment settings
available when the error was logged, the function name and arguments provided,
and a full backtrace are available for you to inspect. Additionally, the Monitor
extension includes an API that allows you to trigger custom Monitor events, and
you can provide additional context when doing so — such as passing objects or
arrays that may help provide context when debugging.

<!--- EXTENDED -->

![](/uploads/zend.server.event.png)

To tie into this feature, we've developed a new `Zend_Log` writer,
`Zend_Log_Writer_ZendMonitor`, that will emit such custom events. In addition,
some modifications were made to the `Zend_Log` API to allow passing extra
contextual information to log writers.

As an example, you could do the following:

```php
$log = new Zend_Log(new Zend_Log_Writer_ZendMonitor());
$log->crit('Exception occurred processing login', $e);

// or:
$log->crit('Exception occurred processing login', array(
    'request'   => $request, // Request object
    'exception' => $e,       // Exception
));
```

Zend Server's GUI would then present a tab, "Custom", that includes the extra
arguments passed; by default, if just an object is passed, the information will
be returned under the key "info". Passing an associative array is incredibly
useful, as it allows you to provide detailed contextual information.

![](/uploads/zend.server.custom.info.png)

One use case for this feature is to report application exceptions via the
`ErrorController`. This can then provide some great feedback via your Zend
Server GUI; you can filter based on a "Rule Name" of "Custom Event", and further
on criteria such as Severity to determine when and why your application is
hitting the `ErrorController` — and hopefully reduce such occurrences.

We thought this might make a good default use case, and have provided some code
generation surrounding it in `Zend_Tool`. The `ErrorController` will now check
to see if a Log resource is available, and if so, write to it.

To make this happen, we've also written a new Log bootstrap resource that
piggy-backs on some additional new functionality: a new `factory()` method in
`Zend_Log`. This allows you to create `Zend_Log` instances from configuration,
with one or more writers and configured filters. Enabling Zend Monitor logging
via the `ErrorController` is now as simple as adding a single line to your
configuration:

```ini
resources.log.zendmonitor.writerName = "ZendMonitor"
```

Note: you can log to *any* logger, or multiple loggers if desired.

If, within your controllers, you want to log other events, you can do so by
simply grabbing the bootstrap object and then the Log resource:

```php
$bootstrap = $this->getInvokeArg('bootstrap')
if ($bootstrap->hasResource('Log')) {
    $log = $bootstrap->getResource('Log');
    $log->info(/* ... */);
}
```

This kind of simple integration leads to some fantastic benefits for Zend
Framework users that are using Zend Server, and it's incredibly cheap to
implement (the ZendMonitor logger acts as a null logger when the Monitor
extension is not present). What other uses can you find to put it to?

*Note: this functionality is available now via the Zend Framework subversion repository, in trunk. It will be made available in a stable release with the upcoming 1.10 release.*

**Update:** I'd like to point out that the bootstrap resource was made possible by contributions of two [ibuildings](http://ibuildings.com) contributors, Martin Roest and Mark van der Velden, who contributed code that makes it possible to instantiate log instances via a new `factory()` method.
