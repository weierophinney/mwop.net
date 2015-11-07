---
id: 2014-08-28-zend-server-deployment-part-2
author: matthew
title: 'Deployment with Zend Server (Part 2 of 8)'
draft: false
public: true
created: '2014-08-28T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the second in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-08-11-zend-server-deployment-part-1.html)
detailed getting started with [Zend Server on the AWS marketplace](http://www.zend.com/en/solutions/cloud-solutions/aws-marketplace)
and using [zf-deploy](https://github.com/zfcampus/zf-deploy) to create ZPK
packages to deploy to Zend Server.

Today, I'm looking at how to created scheduled/recurring jobs using Zend
Server's Job Queue; think of this as application-level cronjobs.

<!--- EXTENDED -->

Tip 2: Recurring Jobs
---------------------

I needed to define a few recurring jobs on the server. In the past, I've used
cron for this, but I've recently had a slight change of mind on this: if I use
cron, I have to assume I'm running on a unix-like system, and have some sort of
system access to the server. If I have multiple servers running, that means
ensuring they're setup on each server. It seems better to be able to define
these jobs at the applicaton level.

Since Zend Server comes with Job Queue, I decided to try it out for scheduling
recurring jobs. This is not terribly intuitive, however. The UI allows you to
define scheduled jobs… but only gives options for every minute, hour, day,
week, and month, without allowing you to specify the exact interval (e.g.,
every day at 20:00).

The PHP API, however, makes this easy. I can create a job as follows:

```php
$queue = new ZendJobQueue();
$queue->createHttpJob('/jobs/github-feed.php', [], [
    'name'       => 'github-feed',
    'persistent' => false,
    'schedule'   => '5,20,35,40 * * * *',
]);
```

Essentially, you provide a URL to the script to execute (Job Queue "runs" a job
by accessing a URL on the server), and provide a schedule in crontab format. I
like to give my jobs names as well, as it allows me to search for them in the
UI, and also enables linking between the rules and the logs in the UI. Marking
them as *not* persistent ensures that if the job is successful, it will be
removed from the events list.

The question is, where do you define this? I decided to do this in my
post\_activate.php deployment script. However, this raises two new problems:

- Rules need not just a path to the script, but also the scheme and host. You
  _can_ omit those, but only if the script can resolve them via `$_SERVER`…
  which it cannot due during deployment.
- Each deployment adds the jobs you define… but this does not overwrite or
  remove the jobs you added in previous deployments.

I solved these as follows:

```php
$server = 'http://mwop.net';

// Remove previously scheduled jobs:
$queue = new ZendJobQueue();
foreach ($queue->getSchedulingRules() as $job) {
    if (0 !== strpos($job['script'], $server)) {
        // not one we're interested in
        continue;
    }

    // Remove previously scheduled job
    $queue->deleteSchedulingRule($job['id']);
}

$queue->createHttpJob($server . '/jobs/github-feed.php', [], [
    'name'       => 'github-feed',
    'persistent' => false,
    'schedule'   => '5,20,35,40 * * * *',
]);
```

So, in summary:

- Define your rules with names.
- Define recurring rules using the schedule option.
- Define recurring rules in your deployment script, during post\_activate.
- Remove previously defined rules in your deployment script, prior to defining them.

Next time…
----------

The next tip in the series is a short one, perfect for following the US Labor
Day weekend, and details something I learned the hard way from Tip 1 when
setting up deployment tasks.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)
