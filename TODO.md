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

* Need to generate feeds for each paginated view
* Layout should be done as a response listener.

## Ongoing

* Swap out Authentication for ZfcUser
* Modify blog to do static generation?
* Move phly PEAR channel over to new site?
  * do as SCS? or Pirum?
