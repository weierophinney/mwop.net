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

### Prior to deployment

* Delete comments on test site on disqus
* Close commenting on s97
* Export all entries and comments from s9y
* Export comments to disqus

### After deployment

* Notify Planet PHP about change in feed

In general
----------

* Rewrite to use new HTTP functionality from ZF2 http-foundation
* Rewrite to use rewritten Router from ZF2
* Create a renderer platform, and potentially rewrite to use PHP driven
  templating (instead of mustache)
* Add caching support
