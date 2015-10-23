---
id: 99-Simple-Caching-for-PHP
author: matthew
title: 'Simple Caching for PHP'
draft: false
public: true
created: '2005-11-08T14:32:29-05:00'
updated: '2005-11-09T10:01:22-05:00'
tags:
    - php
---
I ran across an article on
["How to build a simple caching system, with PHP"](http://www.phpit.net/article/build-caching-system-php/trackback/)
on PHPit today. Overall, it's a fairly decent article, and uses some good
principles (using the output buffer to capture content, using a callback to
grab the captured content). There are a few minor improvements I'd make,
however.

<!--- EXTENDED -->

There are some definite areas I'd change. First off, `die()` doesn't make sense
to me as a way to abort execution if content is found. `exit(0)` makes more
sense — it indicates that execution was successful.

Also, it's often useful to have the ability to clear the cache for a given
page. Adding a check for a `$_GET` or `$SERVER['PATH_INFO']` element could
accomodate that.

The article says to include the file with the caching functions on every page.
I have a couple issues with that:

1. Too easy to forget to include it.
2. Namespacing — using function in the global area could conflict with other
   user defined functions.

To solve (1), use an `auto_prepend_file` directive. You can do this in a
.htaccess file (`php_value "auto_prepend_file" "/path/to/cache.php"`), in the
`httpd.conf`, or directly in your `php.ini`; in either case, it only needs to be
defined once, and you never have to worry about it in your scripts. If there
are scripts you never want to cache, you can override the value in individual
`.htaccess` directives, or setup a hash-lookup in the caching routines to skip
such scripts.

To solve (2), wrap the functions into a class. You could still call items
statically (`Cache::get_url()`, etc.). This would also allow you to define the
hash lookup as noted above — simply place it in a static property.

Finally, this has all been done before.
[PEAR::Cache_Lite](http://pear.php.net/Cache_Lite) offers all of this
functionality (minus the routines to create unique identifiers per page), and a
little more — efficiently, even. The only difference I've seen in practice is
that when using `PEAR::Cache_Lite`, I had to do an `auto_append_file` as well
to stop the output buffering.

Overall, a nice article and introduction to page caching.
