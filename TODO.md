TODO
====

## After deployment

* Notify Planet PHP about change in feed

## Ongoing

* Move phly PEAR channel over to new site?
  * do as SCS? or Pirum?
* Update resume area
  * better personal statement, more generalized
* Remove Renderer as a service and instead move into the default listener
  * Incorporate layout component?
* Figure out how to attach the EntryController listener statically
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
