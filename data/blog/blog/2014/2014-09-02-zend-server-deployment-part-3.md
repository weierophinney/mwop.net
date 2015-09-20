---
id: 2014-09-02-zend-server-deployment-part-3
author: matthew
title: 'Deployment with Zend Server (Part 3 of 8)'
draft: false
public: true
created: '2014-09-02T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the third in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-08-28-zend-server-deployment-part-2.html)
detailed creating recurring jobs via Zend Job Queue, à la cronjobs.

Today, I'm sharing a very short deployment script tip learned by experience.

<!--- EXTENDED -->

Tip 3: chmod
------------

In the [first tip](/blog/2014-08-11-zend-server-deployment-part-1.html), I
detailed writing deployment scripts. One of the snippets I shared was a chmod
routine:

```php
$command = 'chmod -R a+rwX ./data';
echo "
Executing `$command`
";
system($command);
```

The code is fine; what I did not share is *where* in the deployment script you
should invoke it. As I discovered from experience, this is key.

Zend Server's deployment scripts run as the zend user. If they are writing any
data to the data directory, that data is owned by the zend user and group —
and often will not be writable by the web server user. If you have scheduled
jobs that need to write to the same files, they will fail… unless you have
done the chmod after your deployment tasks are done.

So, that's today's tip: if you need any directory in your application to be
writable by scheduled jobs, which will run as the web server user, make sure
you do your chmod as the last step of your deployment script.

Next time…
----------

The next tip in the series is another short one, and will detail how to secure
your Job Queue job scripts.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)

