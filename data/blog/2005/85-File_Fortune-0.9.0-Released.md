---
id: 85-File_Fortune-0.9.0-Released
author: matthew
title: 'File_Fortune 0.9.0 Released'
draft: false
public: true
created: '2005-07-14T14:38:12-04:00'
updated: '2005-07-14T14:44:33-04:00'
tags:
    - php
---
[File_Fortune](http://pear.php.net/package/File_Fortune) has been released via
[PEAR](http://pear.php.net/).

Changes since the proposal include the addition of a static method for
retrieving a random fortune from a list of files, the addition of a DocBook
tutorial listing several usage examples, renaming the exception classes to
conform to PEAR CS, some minor bugfixes, and some streamlining of the package
definition.

Included in the release is an example script, phpFortune, that can be used on
the command line to get random fortunes from one or more fortune files. Usage
is:

```bash
$ phpFortune fortunes
$ phpFortune fortunes simpsons hitchhiker
$ phpFortune
```

Enjoy!
