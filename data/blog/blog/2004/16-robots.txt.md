---
id: 16-robots.txt
author: matthew
title: robots.txt
draft: false
public: true
created: '2004-01-23T17:24:05-05:00'
updated: '2004-09-20T13:22:43-04:00'
tags:
    - programming
    - personal
---
One thing I've wondered about is the syntax of the `robots.txt` file, where it's
placed, and how it's used. I've known that it is used to block spiders from
accessing your site, but that's about it. I've had to look into it recently
because we're offering free memberships at work, and we don't want them indexed
by search engines. I've also wondered how we can exclude certain areas, such as
where we collate our site statistics, from these engines.

As it turns out, it's really dead simple. Simply create a `robots.txt` file in
your htmlroot, and the syntax is as follows:

```apacheconf
User-agent: *
Disallow: /path/
Disallow: /path/to/file
```

The `User-agent` can specify specific agents or the wildcard; there are so many
spiders out there, it's probably safest to simply disallow all of them. The
`Disallow` line should have only one path or name, but you can have multiple
`Disallow` lines, so you can exclude any number of paths or files.
