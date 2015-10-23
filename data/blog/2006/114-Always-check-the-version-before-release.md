---
id: 114-Always-check-the-version-before-release
author: matthew
title: 'Always check the version before release'
draft: false
public: true
created: '2006-06-04T22:17:35-04:00'
updated: '2006-06-04T22:23:24-04:00'
tags:
    - php
---
Last week, I had someone bring to my attention that the [SPL's](http://php.net/spl) `Countable` interface was actually first released in PHP 5.1.0… which means I needed to update the PHP dependency on [Phly_Hash](/phly/index.php?package=Phly_Hash). I also needed to do so on [Phly_Config](/phly/index.php?package=Phly_Config) as it depends on `Phly_Hash`. I released 1.1.1 versions of each yesterday; the only change in each is the PHP version dependency.
