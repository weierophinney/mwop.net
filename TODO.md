TODO
====

For deployment on site
----------------------

* Feeds need to be in place
  * At the very least, full blog feed
  * Ideally also per-tag
* Authentication/Authorization for blog posters (me!)
  * Require authorization key for posting entries
* Rewrite map of old URLs to new
* Mobile layout
  * Barebones -- just a changed stylesheet, potentially with fewer items
  * Ideally some JS functionality around the footers, sidebars, etc.
* Add caching for individual entries
  * Ideally also add for listings, and have publishing a post expire them


In general
----------

* Rewrite to use new HTTP functionality from ZF2 http-foundation
* Rewrite to use rewritten Router from ZF2
* Create a renderer platform, and potentially rewrite to use PHP driven
  templating (instead of mustache)
* Add caching support
