---
id: 46-MySQL-Miscellanae
author: matthew
title: 'MySQL Miscellanae'
draft: false
public: true
created: '2004-10-20T16:51:20-04:00'
updated: '2004-10-20T16:51:28-04:00'
tags:
    - programming
    - personal
---
Inspired by a [Slashdot book review](http://slashdot.org/article.pl?sid=04/10/13/2016211)
of [High Performance MySQL](http://www.oreilly.com/catalog/hpmysql/index.html).

I've often suspected that I'm not a SQL guru… little things like being self
taught and having virtually no resources for learning it. This has been
confirmed to a large degree at work, where our DBA has taught me many tricks
about databases: indexing, when to use `DISTINCT`, how and when to do `JOIN`s,
and the magic of `TEMPORARY TABLE`s. I now feel fairly competent, though far
from being an expert — I certainly don't know much about how to tune a server
for MySQL, or tuning MySQL for performance.

Last year around this time, we needed to replace our MySQL server, and I got
handed the job of getting the data from the old one onto the new. At the time, I
looked into replication, and from there discovered about binary copies of a data
store. I started using this as a way to backup data, instead of periodic
mysqldumps.

One thing I've often wondered since: would replication be a good way to do
backups? It seems like it would, but I haven't investigated. One post on the
aforementioned Slashdot article addressed this, with the following summary:

1. Set up replication
2. Do a locked table backup on the slave

Concise and to the point. I only wish I had a spare server on which to implement
it!
