TODO
====

Updates
-------

* Remove Renderer as a service and instead move into the default listener
* Figure out how to attach the EntryController listener statically

### Prior to deployment

* Move ZendCon slides into repository, and link from resume area
* Update resume area
    * better personal statement, more generalized
    * new selection of slides
        * ZF2 talk from this year
        * Beautiful software talk from this year
        * Maybe an additional page with full list of past presentations and
          links to slides?
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
* Add caching for individual entries
  * Ideally also add for listings, and have publishing a post expire them
* Consolidate several classes from Comic component
  * RSS class: Basic Instructions, XKCD, G-G, SfaM
  * DomQuery class: GoComics, PennyArcade, NIH, Dilbert, FoxTrot, Ctrl-Alt-Del,
    UF
