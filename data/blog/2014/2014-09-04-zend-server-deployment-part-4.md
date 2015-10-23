---
id: 2014-09-04-zend-server-deployment-part-4
author: matthew
title: 'Deployment with Zend Server (Part 4 of 8)'
draft: false
public: true
created: '2014-09-04T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the fourth in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-09-02-zend-server-deployment-part-3.html)
detailed a trick I learned about when to execute a chmod statement during
deployment.

Today, I'm sharing a tip about securing your Job Queue job scripts.

<!--- EXTENDED -->

Tip 4: Secure your job scripts
------------------------------

In the [second tip](/blog/2014-08-28-zend-server-deployment-part-2.html), I
detailed *when* to register job scripts, but not how to write them. As it turns
out, there's one very important facet to consider when writing job scripts:
security.

One issue with Job Queue is that jobs are triggered… via the web. This means
that they are exposed via the web, which makes them potential attack vectors.
However, there's a simple trick to prevent access other than from Job Queue;
add this at the top of your job scripts:

```php
if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}
```

While the jobs are invoked via HTTP, Zend Server has ways of tracking whether
or not they are being executed in the context of Job Queue, and for which job.
If the `ZendJobQueue::getCurrentJobId()` returns a falsy value, then it was not
invoked via Job Queue, and you can exit immediately. I like to set a 403 status
in these situations as well, but that's just a personal preference.

Next time…
----------

The next tip in the series is builds on this one, and gives some best practices
to follow when writing your job scripts.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)
