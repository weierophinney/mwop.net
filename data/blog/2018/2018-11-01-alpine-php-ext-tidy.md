---
id: 2018-11-01-alpine-php-ext-tidy
author: matthew
title: 'Building a usable ext-tidy for Alpine-based PHP Docker images'
draft: false
public: true
created: '2018-11-01T17:23:00-05:00'
updated: '2018-11-01T17:23:00-05:00'
tags:
    - php
    - docker
---

I've been working on building PHP Docker images for the purposes of testing, as
well as to potentially provide images containing the Swoole extension. This is
generally straight-forward, as the official PHP images are well-documented.

This week, I decided to see if I could build Alpine-based images, as they can
greatly reduce the final image size. And I ran into a problem.

<!--- EXTENDED -->

One of the test-beds I use builds RSS and Atom feeds using [zend-feed](https://docs.zendframework.com/zend-feed).
When I tried one of these images, I started getting failures like the following:

```text
PHP Warning:  DOMDocument::loadXML(): xmlParseEntityRef: no name in Entity, line: 167 in /var/www/vendor/zendframework/zend-feed/src/Writer/Renderer/Entry/Atom.php on line 404
PHP Fatal error:  Uncaught TypeError: Argument 1 passed to DOMDocument::importNode() must be an instance of DOMNode, null given in /var/www/vendor/zendframework/zend-feed/src/Writer/Renderer/Entry/Atom.php:371
Stack trace:
#0 /var/www/vendor/zendframework/zend-feed/src/Writer/Renderer/Entry/Atom.php(371): DOMDocument->importNode(NULL, 1)
#1 /var/www/vendor/zendframework/zend-feed/src/Writer/Renderer/Entry/Atom.php(53): Zend\Feed\Writer\Renderer\Entry\Atom->_setContent(Object(DOMDocument), Object(DOMElement))
#2 /var/www/vendor/zendframework/zend-feed/src/Writer/Renderer/Feed/Atom.php(91): Zend\Feed\Writer\Renderer\Entry\Atom->render()
#3 /var/www/vendor/zendframework/zend-feed/src/Writer/Feed.php(237): Zend\Feed\Writer\Renderer\Feed\Atom->render()
#4 /var/www/src/Blog/Console/FeedGenerator.php(209): Zend\Feed\Writer\Feed->export('Atom')
```

During an initial search, this appeared to be a problem due to libxml2 versions,
and so I went down a rabbit hole trying to get an older libxml2 version in
place, and have all of the various XML extensions compile against it. However,
the error persisted.

So, I did a little more sleuthing. I fired up the container with a shell:

```bash
$ docker run --entrypoint /bin/sh -it php:7.2-cli-alpine3.8
```

From there, I used `apk` to add some editing and debugging tools so I could
manually step through some of the code. In doing so, I was able to discover the
exact feed item that was causing problems, and, better, get the content it was
trying to use.

I realized at that point that the problem was the content &mdash; which was
being massaged via the [tidy extension](http://php.net/tidy) before being passed
to `DOMDocument::loadXML()`. For some reason, the content generated was not
valid XML! (Which is really, really odd, as the whole point of tidy is to
produce valid markup!)

I checked the version of ext-tidy, and what version of libtidy it was compiled
against, and then checked against the php:7.2-cli image to see what it had, and
discovered that while Alpine was using libtidy 5.6.0, the Debian-based image was
using 5.2.0. In fact, Ubuntu 18:10 still distributes 5.2.0!

So, I then went on a quest to figure out how to get the earlier libtidy version,
and compile the tidy extension against it. This is what I came up with:

```Dockerfile
# DOCKER-VERSION        1.3.2

FROM php:7.2-cli-alpine3.8

# Compile-time dependencies
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/v3.6/community' >> /etc/apk/repositories
RUN apk update && \
  apk add --no-cache 'tidyhtml-dev==5.2.0-r1'

# Install the extension
RUN docker-php-ext-install -j$(nproc) tidy
```

Once I'd built an image using the above, I tried out my code, and the errors
disappeared!

> This post mainly exists because my google searches were finding nothing.
> Hopefully, somebody else who runs into the problem will get something useful
> going forward!
