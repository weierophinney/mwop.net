---
id: 208-Recursively-Destroying-Dojo-ContentPanes
author: matthew
title: 'Recursively Destroying Dojo ContentPanes'
draft: false
public: true
created: '2009-02-23T08:17:00-05:00'
updated: '2009-02-23T08:17:00-05:00'
tags:
    - dojo
---
I ran into an issue recently with Dojo's ContentPanes. I was using them with a TabContainer, and had made them closable; however, user actions might re-open tabs that pull from the same source. This led to conflicts with dijit IDs that I had to resolve.

Most Dijits have a `destroyRecursive()` method which should, theoretically, destroy any dijits contained within them. However, for many Dijits, this functionality simply does not work due to how they are implemented; many do not actually have any knowledge of the dijits beneath them.

ContentPanes fall into this latter category. fortunately, it's relatively easy to accomplish, due to Dojo's heavily object oriented nature.

<!--- EXTENDED -->

```javascript
dojo.provide(\"custom.ContentPane\");

dojo.require(\"dijit.layout.ContentPane\");

dojo.declare(\"custom.ContentPane\", [dijit.layout.ContentPane], {
    postMixInProperties: function(){
        if (dijit.byId(this.id)) {
            dijit.byId(this.id).destroyRecursive();
        }
    },

    destroyRecursive: function(){
        dojo.forEach(this.getDescendants(), function(widget){
            widget.destroyRecursive();
        });
        this.inherited(arguments);
    }
});
```

The `destroyRecursive()` method is not that different from the one in `dijit._Widget`; the difference is that instead of calling simply `destroy()` on any discovered widgets, we destroy recursively.

The `postMixInProperties` method I added due to IE issues I've run into. Basically, even though the ContentPane was being destroyed recursively, for some reason IE was keeping a reference to the original dijit floating around. `postMixInProperties()` checks to see if the dijit ID is still around, and if so, destroys it recursively. This allows the ContentPane initialization to proceed.
