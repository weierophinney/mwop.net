---
id: 73-Cgiapp-1.7.0-released
author: matthew
title: 'Cgiapp 1.7.0 released'
draft: false
public: true
created: '2005-05-20T23:32:03-04:00'
updated: '2005-05-20T23:39:29-04:00'
tags:
    - php
---
I'm getting ready to move in another week, and thought it was time to push a new
release out the door… before life descends into utter chaos.

Cgiapp 1.7.0 adds a plugin architecture (which has been present in the perl
version since last autumn). Plugins register with the class, and, once
registered, their methods may be called from your Cgiapp-based class as if they
were part of it through the magic of overloading. This allows for a standard
library of utilities to be written — such as form validation (a sample class for
this has been provided utilizing [HTML_QuickForm](http://pear.php.net/HTML_QuickForm)),
authentication, error logging, etc.

Additionally, I created a `Cgiapp5` class that inherits from and extends Cgiapp.
Along with it is a `CgiappErrorException` class that can handle PHP errors and
rethrow them as exceptions. Combined, the two create some very elegant run mode
error handling that simply isn't possible in PHP4.

Visit the [Cgiapp website](http://cgiapp.sourceforge.net/) for more information
on Cgiapp; if you want to try it, [download it](http://prdownloads.sourceforge.net/cgiapp/Cgiapp-1.7.0.tgz?download).
