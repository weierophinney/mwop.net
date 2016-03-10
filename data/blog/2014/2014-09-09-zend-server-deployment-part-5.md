---
id: 2014-09-09-zend-server-deployment-part-5
author: matthew
title: 'Deployment with Zend Server (Part 5 of 8)'
draft: false
public: true
created: '2014-09-09T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the fifth in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-09-04-zend-server-deployment-part-4.html)
detailed how to secure your Job Queue job scripts.

Today, I'm sharing some best practices around writing job scripts, particularly
around how to indicate execution status.

<!--- EXTENDED -->

Tip 5: Set your job status
--------------------------

You should always set your job script status, and exit with an appropriate
return status. This ensures that Job Queue knows for sure if the job completed
successfully, which can help you better identify failed jobs in the UI. I use
the following:

```php
// for failure:
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
exit(1);

// for success:
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
```

I also have started returning relevant messages. Since Job Queue aggregates
these in the UI panel, that allows you to examine the output, which often helps
in debugging.

```php
exec($command, $output, $return);
header('Content-Type: text/plain');
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    echo implode("
", $output);
    exit(1);
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
echo implode("
", $output);
exit(0);
```

Here's sample output:

![](//uploads.mwop.net/2014-09-09-ZendServer-JobStatus.png)

(The `[0;34m]`-style codes are colorization codes; terminals capable of color
would display the output in color, but Zend Server, of course, is seeing plain
text.)

In sum: return appropriate job status via the
`ZendJobQueue::setCurrentJobStatus()` static method and the `exit()` code, and
send output to help diagnose issues later.

Next time…
----------

The next tip in the series discusses setting up page caching in Zend Server, as
well as creating jobs to clear page caches.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)

