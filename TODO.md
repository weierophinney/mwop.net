TODO
====

For deployment on site
----------------------

* Posting entries
  * Needs some sort of authentication
  * Perhaps make it an API *only*?
* Comics
  * Require authorization of some sort; digest, maybe?
* Rewrite map of old URLs to new
* File assets
  * Need to add file assets to website
  * Update all paths to assets in blog posts
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
* Consolidate several classes from Comic component
  * RSS class: Basic Instructions, XKCD, G-G, SfaM
  * DomQuery class: GoComics, PennyArcade, NIH, Dilbert, FoxTrot, Ctrl-Alt-Del,
    UF
