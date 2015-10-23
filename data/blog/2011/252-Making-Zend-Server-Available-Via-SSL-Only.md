---
id: 252-Making-Zend-Server-Available-Via-SSL-Only
author: matthew
title: 'Making Zend Server Available Via SSL Only'
draft: false
public: true
created: '2011-01-06T15:51:58-05:00'
updated: '2011-01-07T16:53:43-05:00'
tags:
    - php
---
In light of the [recent remote PHP exploit](http://bugs.php.net/bug.php?id=53632),
I decided to update a couple servers I manage to ensure they weren't
vulnerable. In each case, I had been using hand-compiled PHP builds, but
decided that I'm simply too busy lately to be trying to maintain updates — so I
decided to install [Zend Server](http://www.zend.com/en/products/server/). I've
been using Zend Server CE on my laptop since before even any initial private
betas, and have been pretty happy with it — I only compile now when I need to
test specific PHP versions.

One thing I've never been happy about, however, is that by default Zend Server
exposes its administration GUI via both HTTP and HTTPS. Considering that the
password gives you access to a lot of sensitive configuration, I want it to be
encrypted.

<!--- EXTENDED -->

The Zend Server GUI runs on a [lighttpd](http://www.lighttpd.net/) instance,
which means you can configure access to the GUI via lighttpd; in fact, the
[documentation even details some approaches to securing it](http://files.zend.com/help/Zend-Server-Community-Edition/securing_the_administration_interface.htm).
The recommendations, however, are to restrict by IP address — which is great if
you have a fixed IP, are the only one accessing the admin, or never access
from, say, your phone, but not terribly useful if any of those are not true.

With a little help from [Shahar](http://prematureoptimization.org/), I figured
out what to do, however. I added this clause to my
`lighttpd.conf`[<sup>[1]</sup>](#f1) file:

```perl
# Disable access via http (i.e., make admin https-only)
$SERVER["socket"] == ":10081" {
  $HTTP["remoteip"] !~ "127.0.0.1" {
      $HTTP["url"] =~ "^/ZendServer/" {
          url.access-deny = ( "" )
      }
  }
}
```

The above basically reads as follows:

- If the request comes in on port 10081 (the default HTTP port for the Zend Server admin)

- and the remote address is not localhost (IP `127.0.0.1`):

- Deny access to any URL starting with "/ZendServer/"

Once you add the stanza, restart lighttpd[<sup>[2]</sup>](#f2) for the changes
to take effect. When accessing the site via
`http://servername:10081/ZendServer`, you should now receive a "403 -
Forbidden" page, while access via `https://servername:10082/ZendServer` remains
open.

- <sup>[1]</sup>In linux versions of Zend Server, `/usr/local/zend/gui/lighttpd/etc/lighttpd.conf`
- <sup>[2]</sup> In linux versions of Zend Server, `/usr/local/zend/bin/zendctl.sh restart-lighttpd`
