TODO
====

Updates
----

* Modify module configuration to use "module-controller" aliases
  * Work out how this translates to view script location...
* Remove Renderer as a service and instead move into the default listener
* Figure out how to attach the EntryController listener statically

For deployment on site
----------------------

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
