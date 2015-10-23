---
id: 2014-08-11-zend-server-deployment-part-1
author: matthew
title: 'Deployment with Zend Server (Part 1 of 8)'
draft: false
public: true
created: '2014-08-26T15:15:00-05:00'
updated: '2014-09-18T08:30:00-05:00'
tags:
    - apigility
    - php
    - programming
    - zend-framework
    - zend-server
---
I manage a number of websites running on Zend Server, Zend's PHP application
platform. I've started accumulating a number of patterns and tricks that make
the deployments more successful, and which also allow me to do more advanced
things such as setting up recurring jobs for the application, clearing page
caches, and more.

<!--- EXTENDED -->

Yes, YOU can afford Zend Server
-------------------------------

"But, wait, Zend Server is uber-expensive!" I hear some folks saying.

Well, yes and no.

With the release of Zend Server 7, Zend now offers a "Development Edition" that
contains all the features I've covered here, and which runs $195. This makes
it affordable for small shops and freelancers, but potentially out of the reach
of individuals.

But there's another option, which I'm using, which is even more intriguing:
[Zend Server on the Amazon Web Services (AWS) Marketplace](http://www.zend.com/en/solutions/cloud-solutions/aws-marketplace).
On AWS, you can try out Zend Server free for 30 days. After that, you get
charged a fee on top of your normal AWS EC2 usage. Depending on the EC2
instance you choose, this can run as low as ~$24/month (this is on the
t1.micro, and that's the total per month for both AWS and Zend Server usage).
That's cheaper than most VPS hosting or PaaS providers, and gives you a full
license for Zend Server.

Considering Zend Server is available on almost every PaaS and IaaS offering
available, this is a great way to try it out, as well as to setup staging and
testing servers cheaply; you can then choose the provider you want based on its
other features. For those of you running low traffic or small, personal or
hobbyist sites, it's an inexpensive alternative to VPS hosting.

So… onwards with my first tip.

Tip 1: zf-deploy
----------------

My first trick is to use [zf-deploy](https://github.com/zfcampus/zf-deploy).
This is a tool [Enrico](https://twitter.com/ezimuel) and I wrote when prepping
[Apigility](https://apigility.org) for its initial stable release. It allows
you to create deployment packages from your application, including zip,
tarball, and ZPKs (Zend Server deployment packages). We designed it to simplify
packaging [Zend Framework 2](http://framework.zend.com) and Apigility
applications, but with a small amount of work, it could likely be used for a
greater variety of PHP applications.

zf-deploy takes the current state of your working directory, and clones it to a
working path. It then runs Composer (though you can disable this), and strips
out anything configured in your `.gitignore` file (again, you can disable this).
From there, it creates your package.

One optional piece is that, when creating a ZPK, you can tell it which
deployment.xml you want to use and/or specify a directory containing the
deployment.xml and any install scripts you want to include in the package. This
latter is incredibly useful, as you can use this to shape your deployment.

As an example, on my own website, I have a CLI job that will fetch my latest
[GitHub](https://github.com) activity. I can invoke that in my `post_stage.php`
script:

```php
if (! chdir(getenv('ZS_APPLICATION_BASE_DIR'))) {
    throw new Exception('Unable to change to application directory');
}

$php = '/usr/local/zend/bin/php';

$command = $php . ' public/index.php githubfeed fetch';
echo "
Executing `$command`
";
system($command);
```

One task I always do is make sure my application data directory is writable by
the web server. This next line builds on the above, in that it assumes you've
changed to your application directory first:

```php
$command = 'chmod -R a+rwX ./data';
echo "
Executing `$command`
";
system($command);
```

Yes, PHP has a built-in for chmod, but it doesn't act recursively.

For ZF2 and Apigility applications, zf-deploy also allows you to specify a
directory that contains the `*local.php` config scripts for your
`config/autoload/` directory, allowing you to merge in configuration specific for
the deployment environment. This is a fantastic capability, as I can keep any
private configuration separate from my main repository.

Deployment now becomes:

```bash
$ vendor/bin/zfdeploy.php mwop.net.zpk --configs=../mwop.net-config --zpk=zpk
```

and I now have a ZPK ready to push to Zend Server.

In sum: zf-deploy simplifies ZPK creation, and allows you to add deployment
scripts that let you perform other tasks on the server.

Next time…
----------

Next tip: creating scheduled Job Queue jobs, à la cronjobs.

Other articles in the series
----------------------------

- [Tip 2: Recurring Jobs](/blog/2014-08-28-zend-server-deployment-part-2.html)
- [Tip 3: chmod](/blog/2014-09-02-zend-server-deployment-part-3.html)
- [Tip 4: Secure your job scripts](/blog/2014-09-04-zend-server-deployment-part-4.html)
- [Tip 5: Set your job status](/blog/2014-09-09-zend-server-deployment-part-5.html)
- [Tip 6: Page caching](/blog/2014-09-11-zend-server-deployment-part-6.html)
- [Tip 7: zs-client](/blog/2014-09-16-zend-server-deployment-part-7.html)
- [Tip 8: Automate](/blog/2014-09-18-zend-server-deployment-part-8.html)
