---
id: 2014-09-16-zend-server-deployment-part-7
author: matthew
title: 'Deployment with Zend Server (Part 7 of 8)'
draft: false
public: true
created: '2014-09-16T08:30:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
This is the seventh in a series of eight posts detailing tips on deploying to
Zend Server. [The previous post in the series](/blog/2014-09-11-zend-server-deployment-part-6.html)
detailed setting up and clearing page caching.

Today, I'm sharing how to use the [Zend Server SDK](https://github.com/zend-patterns/ZendServerSDK)
to deploy your Zend Server deployment packages (ZPKs) from the command line.

<!--- EXTENDED -->

Tip 7: zs-client
----------------

Zend Server has an API, which allows you to interact with many admin tasks
without needing access to the UI, and in an automated fashion. The API is
extensive, and has a very complex argument signing process, which makes it
difficult to consume. However, this is largely solved via
[zs-client, the Zend Server SDK](https://github.com/zend-patterns/ZendServerSDK).

The first thing you need to do after downloading it is to create an application
target. This simplifies usage of the client for subsequent requests, allowing
you to specify `--target={target name}` instead of having to provide the Zend
Server URL, API username, and API token for each call.

This is done using the `addTarget` command:

```bash
$ zs-client.phar addTarget \
> --target={unique target name} \
> --zsurl={URL to your Zend Server instance} \
> --zskey={API username} \
> --zssecret={API token} \
> --http="sslverifypeer=0"
```

The `zsurl` is the scheme, host, and port only; don't include the path. You can
find keys and tokens on the "Administration > Web API" page of your Zend
Server UI, and can even generate new ones there.

![](//uploads.mwop.net/2014-09-16-WebApiKeys.png)

Note the last line; Zend Server uses self-signed SSL certificates, which can
raise issues with cURL in particular — which the SDK uses under the hood.
Passing `--http="sslverifypeer=0"` fixes that situation.

Once you've created your target, you need to determine your application
identifier. Use the `applicationGetStatus` command to find it:

```bash
$ zs-client.phar applicationGetStatus --target={unique target name}
```

Look through the list of deployed applications, and find the of the application.

From here, you can now deploy packages using the `applicationUpdate` command:

```bash
$ zs-client.phar applicationUpdate \
> --appId={id} \
> --appPackage={your ZPK} \
> --target={unique target name}
```

In sum: the Zend Server SDK gives us the tools to automate our deployment.

Next time…
----------

The next tip in the series details automating deployments using zf-deploy and
zs-client.

Other articles in the series
----------------------------

- [Tip 1: zf-deploy](/blog/2014-08-11-zend-server-deployment-part-1.html)
- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)
