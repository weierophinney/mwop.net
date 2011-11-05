TODO
====

Updates
-------

* Modify module configuration to use "module-controller" aliases
  * Work out how this translates to view script location...
* Remove Renderer as a service and instead move into the default listener
* Figure out how to attach the EntryController listener statically

For deployment on site
----------------------

* Better home page!
* Better mobile layout
    * iPhone layout works fine; however, neither android or iphone layouts work
      well on our nooks. 
    * Potentially use "Skeleton" (http://getskeleton.com)

### Prior to deployment

* Delete comments on test site on disqus
* Close commenting on s97
* Export all entries and comments from s9y
* Export comments to disqus

### After deployment

* Notify Planet PHP about change in feed

Ongoing
-------

* Configuration
  * Ensure all modules only include bare minimum, generic configuration
  * Move app-specific configuration into a new module, loaded last
    * Do as a .dist file, and track that
    * Have a non-.dist file, untracked, that has the actual version
* Create a "console" tool
  * bootstraps application
  * invokes the script provided within the bin/ dir of the specified module,
    appending ".php":
    console module:script
  * use for comics, blog posting, potentially caching stuff
* Add caching for individual entries
  * Ideally also add for listings, and have publishing a post expire them
* Consolidate several classes from Comic component
  * RSS class: Basic Instructions, XKCD, G-G, SfaM
  * DomQuery class: GoComics, PennyArcade, NIH, Dilbert, FoxTrot, Ctrl-Alt-Del,
    UF
