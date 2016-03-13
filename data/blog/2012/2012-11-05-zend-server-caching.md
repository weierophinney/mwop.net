---
id: 2012-11-05-zend-server-caching
author: matthew
title: 'Zend Server, ZF2, and Page Caching'
draft: false
public: true
created: '2012-11-05T15:25:00-06:00'
updated: '2012-11-05T15:25:00-06:00'
tags:
    - zf2
---
Zend Server has a very cool [Page Caching feature](http://www.youtube.com/watch_v=i2XXn2SA5zM.html).
Basically, you can provide URLs or URL regular expressions, and tell Zend
Server to provide full-page caching of those pages. This can provide a
tremendous performance boost, without needing to change anything in your
application structure; simply enable it for a set of pages, and sit back and
relax.

<!--- EXTENDED -->

![Zend Server Page Caching](/images/blog/2012-11-04-Server-CachingRule.png "Zend Server Page Caching")

However, this feature is not entirely straight-forward when using a framework
that provides its own routing, such as ZF2. The reason is because it assumes by
default that each match maps to a specific file on the filesystem, and prepares
the caching based on the actual *file* it hits. What this means for ZF2 and
other similar frameworks is that any page that matches will return the cached
version for the *first* match that also matches the same *file* — i.e.,
`index.php` in ZF2. That's every page the framework handles. As an example, if
I match on `/article/\d+`, it matches this to the file `index.php`, and then
any other match that resolves to `index.php` gets served that same page. Not
handy.

The good part is that there's a way around this.

When creating or modifying a caching rule, simply look for the text, "Create a
separate cached page for each value of:" and click the "Add Parameter" button.
Select `_SERVER` from the dropdown, and type `[REQUEST_URI]` for the value.
Once saved, each page that matches the pattern will be cached separately.

![Zend Server Page Caching by Request](/images/blog/2012-11-04-Server-Caching-Request.png "Zend Server Page Caching by Request")

Note: the `_SERVER` key may vary based on what environment/OS you're deployed
in. Additionally, it may differ based on how you define rewrite rules — some
frameworks and CMS systems will append to the query string, for instance, in
which case you may want to select the "entire query string" parameter instead
of `_SERVER`; the point is, there's likely a way for you to configure it.
