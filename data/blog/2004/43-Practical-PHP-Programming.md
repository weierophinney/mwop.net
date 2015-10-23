---
id: 43-Practical-PHP-Programming
author: matthew
title: 'Practical PHP Programming'
draft: false
public: true
created: '2004-10-07T23:29:31-04:00'
updated: '2004-10-07T23:29:38-04:00'
tags:
    - personal
    - php
---
In the past two days, I've seen two references to [Practical PHP Programming](http://www.hudzilla.org/php/),
an online book that serves both as an introduction to programming with PHP5 and
MySQL as well as a good advanced reference with many good tips.

This evening, I was browsing through the Performance chapter (chapter 18), and
found a number of cool things, both for PHP and MySQL. Many were common sense
things that I've been doing for a while, but which I've also seen and shaken my
head at in code I've seen from others (calculating loop invariables at every
iteration, not using variables passed to a function, not returning a value from
a function, not using a return value from a function). Others were new and gave
me pause for thought (string concatenation with the '.' operator is expensive,
especially when done more than once in an operation; `echo` can take a comma
separated list).

Some PHP myths were also dispelled, some of which I've been wondering about for
awhile. For instance, the amount of comments and whitespace in PHP are not a
factor in performance (and PHP caching systems will often strip them out
anyways); double quotes are not more expensive than single quotes unless
variable interpolation occurs.

It also has some good advice for SQL optimization, and, more importantly, MySQL
*server* optimization. For instance, the author suggests running `OPTIMIZE TABLE
table;` on any table that has been added/updated/deleted from to any large
extent since creation; this will defrag the table and give it better
performance. Use `CHAR()` versus `VARCHAR()`; `VARCHAR()` saves on space, but
MySQL has to calculate how much space was used each time it queries in order to
determine where the next field or record starts. However, if you have any
variable length fields, you may as well use as many as you need — or split off
variable length fields (such as a `TEXT()` field) into a different table in
order to speed up searching. When performing `JOIN`s, compare on numeric fields
instead of character fields, and always `JOIN` on rows that are indexed.

I haven't read the entire book, but glancing through the TOC, there are some
potential downfalls to its content:

- It doesn't cover PhpDoc It doesn't appear to cover unit testing Limited
- coverage of templating solutions (though they are mentioned) Limited usage of
- PEAR. The author does mention PEAR a number of times, and
  often indicates that use of certain PEAR modules is preferable to using the
  corresponding low-level PHP calls (e.g., `Mail` and `Mail_MIME`, `DB`), but in
  the examples rarely uses them.
- PHP-HTML-PHP… The examples I browsed all created self-contained scripts that
  did all HTML output. While I can appreciate this to a degree, I'd still like
  to see a book that shows OOP development in PHP *and which creates re-usable
  web components* in doing so. For instance, instead of creating a message board
  *script*, create a message board *class* that can be called from anywhere with
  metadata specifying the database and templates to use.

All told, there's plenty of meat in this book — I wish it were in dead tree
format already so I could browse through it at my leisure, instead of in front
of the computer.
