---
id: 66-Cgiapp-1.6.0-Released
author: matthew
title: 'Cgiapp 1.6.0 Released'
draft: false
public: true
created: '2005-04-22T23:50:31-04:00'
updated: '2005-04-25T16:33:42-04:00'
tags:
    - php
---
Cgiapp 1.6.0, "Wart Removal", has been released!

This release does not add any new methods, but adds quite a lot in terms of
functionality:

- **phpt tests.** I finished writing a suite of unit tests using the phpt
  framework popularized by the PHP-QA project and PEAR. This process helped me
  find some obscure bugs in the class, as well as some… well, downright ugly
  code, and to fix these areas. (To be honest, most of the 'ugly' code was a
  result of being too literal when porting from perl and not using more standard
  PHP functionality.) Among the bugs fixed:
  - `s_delete()` now works properly.  `param()` and `s_param()` now behave
    gracefully when given bad data (as do a number of other methods)
  - `_send_headers()` and the `header_*()` suite now function as documented.
  - All methods now react gracefully to bad input.
- **Error handling.** `carp()` and `croak()` no longer echo directly to the
    output stream (and, in the case of `croak()`, die); they use
    `trigger_error()`. This will allow developers to use `carp()` and `croak()`
    as part of their regular arsenal of PHP errors — including allowing PHP
    error handling. Additionally, most `croak()` calls in the class were changed
    to `carp()` as they were not truly fatal errors.
- **PEAR packaging.** Cgiapp can now be installed using PEAR's installer. Simply
  download the package and type `pear install Cgiapp-1.6.0.tgz` to get Cgiapp
  installed sitewide on your system!

As usual, Cgiapp is available at [the Cgiapp website](http://cgiapp.sourceforge.net/).
