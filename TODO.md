TODO
====

For modules conversion
----------------------

* Move generic resource code into a module (?)
  * Would contain code and tests
  * Potentially two modules: 
    * Filter
    * MapperBase
  * Potentially, could contain views
* Create a blog module
  * Will contain all blog-specific resource/entity code
  * Will contain blog-specific controller, views
    * Views will be converted to PHP
  * Is blog module a dependency for Application module (since tag cloud
    technically goes in site template)?
  * Will contain blog-specific configuration
  * Will contain blog-specific CSS, JS
    * Move these out of site.css, view scripts
    * symlink into public asset directories
  * Will contain blog-specific tests (Entry entity, Entry resource
* Get rid of mwop\Mvc namespace, mwop\Stdlib\ViewPresentation,
  UniqueFilteringIterator
* Add logic for aggregating view script paths (? this might be in ModuleManager
  already...)


For deployment on site
----------------------

* Posting entries
  * Needs some sort of authentication
  * Perhaps make it an API *only*?
* Comics
  * Require authorization of some sort; digest, maybe?
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
* Consolidate several classes from Comic component
  * RSS class: Basic Instructions, XKCD, G-G, SfaM
  * DomQuery class: GoComics, PennyArcade, NIH, Dilbert, FoxTrot, Ctrl-Alt-Del,
    UF
