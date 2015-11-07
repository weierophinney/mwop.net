---
id: 207-Using-JSLint
author: matthew
title: 'Using JSLint'
draft: false
public: true
created: '2009-02-20T08:11:41-05:00'
updated: '2009-02-20T08:11:41-05:00'
tags:
    - dojo
---
I've been doing a fair bit of programming in [Dojo](http://dojotoolkit.org/) lately, and have on occasion run into either inconsistent interfaces, or interfaces that simply fail to load in Internet Explorer. Several people have pointed out to me some optimizations to make, but, being a lazy programmer, I often forget to do so.

Fortunately, there's a tool for lazy developers like myself: [JSLint](http://jslint.com). Linters are commonly used in static development languages so that developers can verify that their programs are syntactically correct prior to compilation; they basically ensure that you're not accidentally attempting to compile something that will never compile in the first place. Many dynamic languages also have them; I've had a key bound in vim to run the current file through PHP's linter for many years now. JSLint provides linting capabilities for JavaScript, as well as some code analysis to point you towards some best practices — mainly geared for cross-browser compatability.

<!--- EXTENDED -->

JSLint looks, at first, like it needs to be a web-based tool. However, this is not so; there are a number of JavaScript VMs you can utilize. Dojo's source builds, for instance, come with a version of Apache's Rhino, a JS VM written in Java, and JSLint provides a script for use with Rhino.

To get JSLint running on the command line using the Rhino shipped with Dojo, you'll need to download the following file:

- [jslint.js](http://jslint.com/rhino/jslint.js)

Put these files in a directory of your choosing. Then, create a file called `jslint`, with the following:

```bash
#!/bin/sh
exec java \
-jar /path/to/dojo/util/shrinksafe/custom_rhino.jar \
/path/to/jslint.js $1
```

Note: you'll need to put in the correct paths to your Dojo installation as well as to where you placed the `jslint.js` file.

Make that file executable, and put it somewhere on your path. Once you do, you can invoke it quite simply:

```bash
$ jslint foo.js
```

and get some nice output. Something I will often do is to grab all JS files in a tree using globbing, and then pass them individually to the linter. In zsh, that might look like this:

```bash
$ for f in *.js;do jslint $f;done
```

I found in most cases, following the advice of the linter eliminated any issues in IE, as well as fixed any inconsistencies I was observing in the UI. Your results may vary, of course — but it's a tremendously useful tool to have in your toolbox if you're a JavaScript developer.
