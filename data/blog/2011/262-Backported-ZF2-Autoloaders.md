---
id: 262-Backported-ZF2-Autoloaders
author: matthew
title: 'Backported ZF2 Autoloaders'
draft: false
public: true
created: '2011-05-10T09:51:00-04:00'
updated: '2011-05-23T11:28:35-04:00'
tags:
    - php
    - 'zend framework'
    - zf2
---
In the past six weeks, I've delivered both a webinar and a tutorial on Zend
Framework 2 development patterns. The first pattern I've explored is our new
suite of autoloaders, which are aimed at both performance and rapid application
development — the latter has always been true, as we've followed PEAR standards,
but the former has been elusive within the 1.X series.

Interestingly, I've had quite some number of folks ask if they can use the new
autoloaders in their Zend Framework 1 development. The short answer is "yes,"
assuming you're running PHP 5.3 already. If not, however, until today, the
answer has been "no."

<!--- EXTENDED -->

I've recently backported the ZF2 autoloaders to PHP 5.2, and posted them on GitHub:

- [https://github.com/weierophinney/zf-examples/tree/feature%2Fzf1-classmap/zf1-classmap](https://github.com/weierophinney/zf-examples/tree/feature%2Fzf1-classmap/zf1-classmap)

I'm also posting a tarball here:

- [https://uploads.mwop.net/zf1-classmap.tgz](//uploads.mwop.net/zf1-classmap.tgz)

The functionality includes:

- A class map generation tool
- A [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)-compliant autoloader, with `include_path` fallback capabilities
- A class-map autoloader
- An autoloader factory for loading many autoloading strategies at once

I've included a README file that details most use cases:

- [https://github.com/weierophinney/zf-examples/blob/feature%2Fzf1-classmap/zf1-classmap/README.md](https://github.com/weierophinney/zf-examples/blob/feature%2Fzf1-classmap/zf1-classmap/README.md)

The most interesting use case, I find, is combining a class-map autoloader with
a PSR-0 autoloader configured with one or more paths and set as a fallback. This
allows the benefits of performance when the class-map is seeded well, and
developer performance when in active development. For it to work, you need to
create at least an empty class-map. I will do the following immediately after
generating a project in order to pre-seed it:

```bash
$ cd application/
$ php /path/to/zf/bin/classmap_generator.php -w
  Creating class file map for library in '/var/www/project/application'...
  Wrote classmap file to '/var/www/project/application/.classmap.php'
$ cd ../library/
$ php /path/to/zf/bin/classmap_generator.php -w
  Creating class file map for library in '/var/www/project/library'...
  Wrote classmap file to '/var/www/project/library/.classmap.php'
```

(The `-w` switch tells the generator to overwrite any classmap files already present.)

From there, I do the following in `public/index.php`:

```php
require_once __DIR__ . '/../library/ZendX/Loader/AutoloaderFactory.php';
ZendX_Loader_AutoloaderFactory::factory(array(
    'ZendX_Loader_ClassMapAutoloader' => array(
        __DIR__ . '/../library/.classmap.php',
        __DIR__ . '/../application/.classmap.php',
    ),
    'ZendX_Loader_StandardAutoloader' => array(
        'prefixes' => array(
            'My' => __DIR__ . '/../library/My',
        ),
        'fallback_autoloader' => true,
    ),
));
```

The above examples do the following:

- Create classmaps from the classes available in each of my `application/` and
  `library`" directories of my project.
- Instantiates a class-map autoloader from those classmaps, and registers them with the SPL autoloader.
- Creates a `StandardAutoloader` instance that's aware of the `My` vendor
  prefix, pointing to the `My/` subdirectory in my library; as I add class files
  here, they will automatically be found.
- Sets a fallback autoloader aware of my `include_path`.

This setup takes a minute or so to create, but ensures that I'm immediately
productive. I then periodically update my classmap, by rerunning the
`classmap_generator.php` script on my application and library directories and
checking this in under version control.

This library is an excellent way to start boosting your ZF1 application
performance (particularly if you [strip your `require_once` calls](http://framework.zend.com/manual/en/performance.classloading.html)),
while simultaneously starting to make your code forward-compatible with ZF2.

Updates
-------

- **2011-05-11 11:00 CDT**: Updated `classmap_generator.php` in the repository
  to remove a closure and thus make it truly PHP 5.2 compliant. Additionally,
  updated the `zf1-classmap.tgz` tarball with this change.

- **2011-05-11 16:00 CDT**: Updated `ClassFileLocator` to define PHP
  5.3-specific tokenizer constants when in earlier PHP versions.

- **2011-05-23 10:25 CDT**: Updated `generate_classmap.php` to (a) use
  `DIRECTORY_SEPARATOR` in paths to ensure portability from Windows to *nix
  environments, and (b) cache the results of `dirname(__FILE__)` to improve
  performance. Thanks to Tomas Fejfar for reporting these issues.
