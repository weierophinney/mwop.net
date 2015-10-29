---
id: 130-Getting-XDebug-working-with-PHPUnit-for-code-coverage-reports
author: matthew
title: 'Getting XDebug working with PHPUnit for code coverage reports'
draft: false
public: true
created: '2006-12-28T13:59:00-05:00'
updated: '2006-12-29T15:00:26-05:00'
tags:
    - php
---
I've been playing with [PHPUnit](http://phpunit.de/) a lot of late, particularly with [framework development](http://framework.zend.com/). One thing that's always hard to determine is how well your code is exercised — basically, how much of the code is tested in the unit tests?

In PHPUnit 3, you can now generate code coverage reports using [XDebug](http://xdebug.org), and the usage is very simple:

```bash
matthew@localhost:~/dev/zend/framework-svn/tests$ phpunit --report ~/tmp/report AllTests
```

The above command creates a coverage report directory `report` under my tmp directory. You can then browse through the reports in a web browser and visually see which lines of code were executed during tests, and which were not, as well as a synopsis showing the percentage of coverage for any given file or directory — useful stuff indeed!

So, what's the problem? Getting XDebug running.

The executive summary:

- Enable the extension using `zend_extension = /full/path/to/xdebug.so`, not as `extension = xdebug.so`, in your `php.ini`
- Use the setting `xdebug.default_enable = Off` in your `php.ini`.
- If compiling using pecl or pear, make sure it compiles against the correct PHP; if not, hand compile it using:

  ```bash
  $ /path/to/phpize
  $ ./configure --with-php-config=/path/to/php-config
  $ make
  $ make install
  ```

For the detailed narrative, read on.

<!--- EXTENDED -->

First off, I tried installing XDebug using pecl and pear. Even though my `pear config-show` showed my correct PHP install and extension directory, for some reason it found the PHP 4.4.1 installation I have elsewhere in the filesystem, and it compiled against that. So, I followed the directions for compiling by hand, and all was mostly well. I discovered, however, that you need to specify the `--with-php-config=/path/to/php-config` switch to ensure that it uses the appropriate `php-config` (particularly if you have multiple PHP installs on your system).

Next up was getting it to work with PHP. I edited my `php.ini` file, and did a standard `extension=xdebug.so`. What was odd is that I then showed xdebug as present (using `php -m`), but not as a Zend extension. I tried `zend_extension=xdebug.so`, but then nothing showed. Then, in the end, I followed the instructions, and used `zend_extension=/full/path/to/xdebug.so`, and it was available.

Okay, let's test it out… I started running tests and… segmentation fault. Disabling the extension brought everything back to normal… only when enabled did the segmentation fault occur. I decided to look at the xdebug `php.ini` settings to see what I could find.

After some trial and error, I discovered that setting `xdebug.default_enable = Off` fixed the issue, and I was able to start generating some wonderful coverage reports.

Now, to write more tests…
