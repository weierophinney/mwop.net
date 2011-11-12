TODO
====

Updates
-------

* Remove Renderer as a service and instead move into the default listener
* Figure out how to attach the EntryController listener statically

### Prior to deployment

* Update resume area
    * better personal statement, more generalized
[X] Delete comments on test site on disqus
[X] Close commenting on s97
[X] Export all entries and comments from s9y
[X] Export comments to disqus
[_] export site to host
    [X] export data to host
    [X] tar site and send to host
    [X] tar ZF2 and send to host
[X] Switch to using ReCaptcha for contact form captcha...
[X] setup vhost for weierophinney.net (with redirects)
[X] test
[_] Switch DNS!

### After deployment

* Notify Planet PHP about change in feed

Ongoing
-------

* Configuration
  * Ensure all modules only include bare minimum, generic configuration
  * Move app-specific configuration into a new module, loaded last
    * Do as a .dist file, and track that
    * Have a non-.dist file, untracked, that has the actual version
* Add caching for individual entries
  * Ideally also add for listings, and have publishing a post expire them
* Consolidate several classes from Comic component
  * RSS class: Basic Instructions, XKCD, G-G, SfaM
  * DomQuery class: GoComics, PennyArcade, NIH, Dilbert, FoxTrot, Ctrl-Alt-Del,
    UF
