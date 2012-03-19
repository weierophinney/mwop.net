TODO
====

## Current: Static Blog

* Posts are PHP files that return an Entry entity
* Compiler:
  * Iterates a specified directory, tree, or set of directories
    * include() each PHP file
      * If the result is not an Entity, throw it away
      * If result is an entity, keep it in memory
  * Creates a view script for each discovered entry
    * "blog/<entry id>.phtml"
  * Sorts discovered Entries by date created, in reverse order
    * Creates paginated view scripts
      * "blog/page/<page>.phtml"
    * Create feed
      * "blog/feed/atom.phtml" (?)
      * "blog/feed/rss.phtml" (?)
  * For each year:
    * Sort by date created, in reverse order
      * Generate paginated view scripts
        * "blog/year/<year>/page/<page>.phtml"
    * For each month:
      * Sort by date created, in reverse order
        * Generate paginated view scripts
          * "blog/month/<year>/<month>/page/<page>.phtml"
      * For each day:
        * Sort by time created, in reverse order
            * Generate paginated view scripts
              * "blog/day/<year>/<month>/<day>/page/<page>.phtml"
  * For each tag:
    * Sort by date created, in reverse order
      * Generate paginated view scripts
        * "blog/tag/<tag>/page/<page>.phtml"
    * Create feed
      * "blog/tag/<tag>/feed/atom.phtml" (?)
      * "blog/tag/<tag>/feed/rss.phtml" (?)
  * Create tag cloud

## Ongoing

* Swap out Authentication for ZfcUser
* Modify blog to do static generation?
* Move phly PEAR channel over to new site?
  * do as SCS? or Pirum?
