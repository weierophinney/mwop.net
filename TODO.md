TODO
====

## Current: Static Blog

* Compiler:
  * Sorts discovered Entries by date created, in reverse order
    * Create feed
      * "blog/feed/atom.phtml" (?)
      * "blog/feed/rss.phtml" (?)
* Controller becomes "dumb"
  * actions simply calculate which template to use
  * no need to be restful

### Issues

* Needs some serious refactoring and cleanup... but works
  * Modify routes to append ".html"
  * Layout and view scripts likely need some slight changes
    * to get syntax highlighting working
    * to ensure RSS/Atom links are correct
* Remove controller and listener
  * Including configuration
* Add compiler script to module? or simpler version in module, and more complex
  version in application? if latter, where?

## Ongoing

* Swap out Authentication for ZfcUser
* Modify blog to do static generation?
* Move phly PEAR channel over to new site?
  * do as SCS? or Pirum?
