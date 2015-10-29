---
id: 188-Proper-Layer-files-when-using-Dojo-with-Zend-Framework
author: matthew
title: 'Proper Layer files when using Dojo with Zend Framework'
draft: false
public: true
created: '2008-09-05T09:30:00-04:00'
updated: '2008-09-08T09:17:34-04:00'
tags:
    - dojo
    - php
    - 'zend framework'
---
During my [Dojo and ZF webinar](http://www.zend.com/en/resources/webinars/framework) on Wednesday,
[Pete Higgins](http://higginsforpresident.net/) of
[Dojo](http://dojotoolkit.org/) fame noted that I could do something different
and better on one of my slides.

This particular item had to do with how I was consuming custom Dojo build
layers within my code. I contacted him afterwards to find out what he
suggested, and did a little playing of my own, and discovered some more Dojo
and javascript beauty in the process.

<!--- EXTENDED -->

The code in question looked like this:

```php
Zend_Dojo::enableView($view);
$view->dojo()->setDjConfigOption('usePlainJson', true)
             // ->setDjConfigOption('isDebug', true)
             ->addStylesheetModule('dijit.themes.tundra')
             ->addStylesheet('/js/dojox/grid/_grid/tundraGrid.css')
             ->setLocalPath('/js/dojo/dojo.js')
             ->addLayer('/js/paste/main.js')
             // ->addLayer('/js/paste/paste.js')
             ->registerModulePath('../paste', 'paste')
             ->addJavascript('paste.main.init();')
             ->disable();
```

The lines he was commenting on were the `addLayer()` lines.

As noted in my webinar, layers, or custom builds, are a fantastic feature of
Dojo. Dojo is incredibly modular, and acts in many ways like a good server-side
library should — only include what is needed, and when its needed. The problem
comes at deployment: the user suddenly experiences a situation where the
application is making dozens of requests back to the server to get what it
needs. The solution is to create a custom build, which pulls in all
dependencies into a single file, inters any templates, and then does
minification heuristics on the code prior to stripping all whitespace and
compressing it. Once done, you now have a single, small file that needs to load
on the request — making the final deployed application snappy.

When I displayed this during the webinar, I noted that after doing so, you have
to change your code to point at the new build — and that's what the two lines
I pointed out are for. In essence, one is for development, the other for
production. Of course, this is just ripe for problems — you forget to switch
comments in production, or accidently re-merge the development version, etc.

Pete showed me another solution that was much more elegant, and which also got
rid of another line in that solution above, the `addJavascript()`

The solution is to write your code in the same layer file as you'll compile to.
When doing so, you can put all your `dojo.require()` statements in the file, as
well as mixin any code you want in the main module namespace:

```javascript
dojo.provide("paste.layer");

/* Dojo modules to require... */
dojo.require("dijit.layout.ContentPane");
/* ... */

/* onLoad actions to perform... */
dojo.addOnLoad(function() {
    paste.upgrade(); 
});

/* mixin functionality to the "paste" namespace: */
dojo.mixin(paste, {
    /* paste.newPasteButton() */
    newPasteButton:  function() {
        var form = dijit.byId("pasteform");
        if (form.isValid()) {
            form.submit(); 
        }
    },
    
    /* ... */
});
```

In my original code, I had a `paste.main.init` method that performed all my
`dojo.require` and `dojo.addOnLoad` statements, but these now can be simply a
part of the layer — eliminating more work for me.

Then, when creating the profile, you simply have it create the layer in the
same file — in this case, `paste/layer.js` — but also have it create a
*dependency* on the original layer file. The compiler will ensure that the
original code gets slurped into the build. As an example:

```javascript
dependencies = {
    layers: [
        {
            name: "../paste/layer.js",
            dependencies: [
                "paste.layer",
                /* other dependencies...*/
            ]
        },
    ],
    prefixes: [
        [ "paste", "../paste" ],
        /* other prefixes -- dijit, etc. */
    ]
}
```

This changes the original ZF snippet above to simply:

```php
Zend_Dojo::enableView($view);
$view->dojo()->setDjConfigOption('usePlainJson', true)
             // ->setDjConfigOption('isDebug', true)
             ->addStylesheetModule('dijit.themes.tundra')
             ->addStylesheet('/js/dojox/grid/_grid/tundraGrid.css')
             ->setLocalPath('/js/dojo/dojo.js')
             ->addLayer('/js/paste/layer.js')
             ->registerModulePath('../paste', 'paste')
             ->disable();
```

Not much shorter — but because I no longer need to worry about changing the
file name, I can rest easier at night.

I'll be blogging more tips such as these in the coming weeks, to help support
the new [Dojo integration](http://framework.zend.com/announcements/2008-09-03-dojo)
in Zend Framework.
