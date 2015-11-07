---
id: 52-Smarty-_SERVER-vars
author: matthew
title: 'Smarty $_SERVER vars'
draft: false
public: true
created: '2004-12-31T23:00:38-05:00'
updated: '2004-12-31T23:04:45-05:00'
tags:
    - personal
    - php
---
I don't know why I never bothered to look this up, but I didn't. One thing I
typically do in my parent Cgiapp classes is to pass `$_SERVER['SCRIPT_NAME']`
to the template. I just found out — through the pear-general newsgroup — that
this is unnecessary: use `$smarty.server.KEY_NAME` to access any `$_SERVER` vars
your template might need.
