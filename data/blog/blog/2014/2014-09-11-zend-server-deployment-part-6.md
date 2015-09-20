---
id: 2014-09-11-zend-server-deployment-part-6
author: matthew
title: 'Deployment with Zend Server (Part 6 of 8)'
draft: false
public: true
created: '2014-09-11T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the sixth in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-09-09-zend-server-deployment-part-5.html)
detailed setting job script status codes.

Today, I'm sharing some tips around setting up page caching, and jobs for
clearing the Zend Server page cache.

<!--- EXTENDED -->

Tip 6: Page caching
-------------------

Zend Server offers page caching. This can be defined per-application or
globally. I typically use global rules, as I most often define server aliases;
application-specific rules are based on the primary server name only, which
makes it impossible to cache per-hostname.

I define my rules first by setting up my rules using regular expressions. For
instance, for my current site, I have this for the host:

```
(www\.)?mwop.net
```

This allows me to match with or without the `www.` prefix.

![](http://uploads.mwop.net/2014-09-11-ZendServer-PageCacheRule.png)

After that, I define regular expressions for the paths, and ensure that matches
take into account the `REQUEST_URI` (failure to do this will cache the same page
for any page matching the regex!).

![](http://uploads.mwop.net/2014-09-11-ZendServer-PageCacheRule-ByUri.png)

When I deploy, or when I run specific jobs, I typically want to clear my cache.
To do that, I have a Job Queue job, and in that script, I use the
`page_cache_remove_cached_contents()` function defined by the page cache
extension in Zend Server.

This function accepts one argument. The documentation says it's a URL, but in
actuality you need to provide the pattern from the rule you want to match; it
will then clear caches for any pages that match that rule. That means you have
to provide the full match — which will include the scheme, host, *port*, and
path. Note the port — that absolutely *must* be present for the match to work,
even if it's the default port for the given scheme.

What that means is that in my example above, the argument to
`page_cache_remove_cached_contents()` becomes
`http://(www\\.)?mwop\\.net:80/resume`. If I allow both HTTP and HTTPS access,
then I also will need to explicitly clear
`https://(www\\.)?mwop\\.net:443/resume`. Note that the regexp escape characters
are present, as are any conditional patterns.

My current cache clearing script looks like this:

```php
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$paths = [
    '/',
    '/resume',
];

foreach ($paths as $path) {
    page_cache_remove_cached_contents(
        'http://(www\.)?mwop\.net:80' . $path
    );
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
```

If I wanted to get more granular, I could alter the script to accept rules and
URLs to clear via arguments provided by Job Queue; see the
[Job Queue](http://files.zend.com/help/Zend-Server/zend-server.htm#zendserverapi/zend_job_queue-php_api.htm#function-createHttpJob)
documentation for information on passing arguments.

I queue this script in my `post_activate.php` deployment script, but without a
schedule:

```php
$queue->createHttpJob($server . '/jobs/clear-cache.php', [], [
    'name' => 'clear-cache',
    'persistent' => false,
]);
```

This will schedule it to run immediately once activation is complete. I will
also queue it from other jobs if what they do should result in flushing the
page cache; I use the exact same code when I do so.

### Note on cache clearing

The Zend Server PHP API offers another function that would appear to be more
relevant and specific: `page_cache_remove_cached_contents_by_uri()`. This
particular function accepts a rule name, and the URI you wish to clear, and, as
documented, seems like a nice way to clear the cache for a specific URI as a
subset of a rule, without clearing caches for all pages matching the rule.
However, as of version 7.0, this functionality does not work properly (in fact,
I was unable to find any combination of rule and url that resulted in a cache
clear). I recommend using `page_cache_remove_cached_contents()` only for now,
or using full page caching within your framework.

Next time…
----------

The next tip in the series discusses using the
[Zend Server SDK](https://github.com/zend-patterns/ZendServerSDK) for deploying
your application from the command line.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)
