---
id: 2018-11-04-docker-redis
author: matthew
title: 'Fixing Redis background-save issues on Docker'
draft: false
public: true
created: '2018-11-04T08:52:00-05:00'
updated: '2018-11-04T08:52:00-05:00'
tags:
    - php
    - docker
    - redis
---

I've been running redis in Docker for a number of sites, to perform things such
as storing session data, hubot settings, and more.

I recently ran into a problem on one of my systems where it was reporting:

```text
Can't save in background: fork: Out of memory
```

<!--- EXTENDED -->

A quick google search showed this is a common error, so much so that there is an
[official FAQ about it](https://redis.io/topics/faq#background-saving-fails-with-a-fork-error-under-linux-even-if-i-have-a-lot-of-free-ram).
The solution is to toggle the `/proc/sys/vm/overcommit_memory` to 1.

The trick when using Docker is that this needs to happen on the _host machine_.

> This still didn't solve my problem, though. So I ran a `docker ps` on the host
> machine to get an idea of what was happening. And discovered that, somehow, I
> had two identical redis containers running, using the exact same configuration -
> which meant they were doing backups to the same volume. Killing the one no
> longer being used by my swarm services caused everything to work once again.
