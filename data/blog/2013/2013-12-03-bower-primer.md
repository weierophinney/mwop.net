---
id: 2013-12-03-bower-primer
author: matthew
title: 'A Bower Primer'
draft: false
public: true
created: '2013-12-03T09:50:00-06:00'
updated: '2013-12-03T09:50:00-06:00'
tags:
    - javascript
    - bower
---
Recently, I've been doing a fair bit of frontend development with my team as
we've worked on the [Apigility](http://apigility.org/) admin. This has meant
working with a variety of both JavaScript and CSS libraries, often trying
something out only to toss it out again later. Working with frontend libraries
has been quite a hassle, due to a combination of discovery, installation
issues, and build issues (minimization, primarily). I figured there must a
better way.

<!--- EXTENDED -->

Background
----------

Until recently, discovery of JS and CSS libraries has gone something like this:

1. Search for functionality via Google
2. Generally find a solution on StackOverflow
3. Discover said solution relies on a third-party library
4. Google for said library
5. Generally find said library on GitHub
6. Clone the library locally
7. Either build the final assets, or try and locate them in the repo
8. Minimize the assets
9. Copy the assets into the project

Frontend development sucks.

Then I started noticing these files called `.bowerrc` and `bower.json` in many
of the aforementioned libraries, and also that [Ralph](http://ralphschindler.com/)
had put some inside our Apigility skeleton.  I got curious as to what this
"bower" might be.

Bower: Package management for the web
-------------------------------------

Essentially, [Bower](http://bower.io/) is, to use the project's words, "a
package manager for the web." Written in JavaScript, and running on
[node.js](http://nodejs.org/), it is to frontend assets what
[npm](https://npmjs.org/) is to node, or [Composer](https://getcomposer.org) is
to PHP. It allows you to define what assets you need in your application,
including the versions, and then install them. If any of those assets have
other dependencies, those, too, will be installed.

Later, you can update the dependencies, add or remove dependencies, and more.

On top of that, bower allows you to *search* for packages, which essentially
allows you to eliminate most of the steps 4 and on in my list above.

A Bower Primer
--------------

So, how do you use bower?

In my experience, which is not extensive by any stretch, the usage is like
this:

1. Search for functionality via Google
2. Generally find a solution on StackOverflow
3. Discover said solution relies on a third-party library
4. Use bower to search for said library
5. Add the discovered library to your `bower.json` file
6. Run `bower install` or `bower update`

I've found that most projects registered with bower have minimized builds
available (as well as the full source build), which is a huge boon in terms of
performance. It also eliminates the "minimize the assets" step from my original
list.

To use bower, you'll need two files. The first is `.bowerrc` which goes in your
project root; you'll run `bower` from this same directory. This file tells
bower how to run, and where to install things, and, despite being an RC file,
is written in JSON. Mine usually looks like this:

```javascript
{
    "directory": "public/assets/vendor"
}
```

The above tells bower to install dependencies in the `public/assets/vendor`
subdirectory.

The second file you need is `bower.json`. This file tells bower what asset
packages you want to install, and the preferred version. (The file can also be
used to define a package, just like with Composer or npm.) As an example, the
following is a definition I used for an Apigility example:

```javascript
{
    "name": "ag-contacts-demo",
    "version": "0.0.1",
    "ignore": [
        "**/.*"
    ],
    "dependencies": {
        "angular": "~1.2",
        "angular-resource": "~1.2",
        "angular-route": "~1.2",
        "bootstrap": ">=3.0.0",
        "font-awesome": "~3.2.1"
    }
}
```

Bower requires that packages use [Semantic Versioning](http://semver.org/). You
can specify exact versions, minor versions, or major versions, combine them
with comparison operators (`<`, `>`, `=`, etc.), or use the "next significant
release" operator (`~`) to indicate a given version up to the next more
general release (e.g., `~1.2` is equivalent to `>=1.2,<2.0`).

Once you have these defined, you should also add an entry to your `.gitignore`
file to exclude the directory you list in your `.bowerrc`; these files can be
installed at build time, and thus help you keep your project repository lean.
Per the above example:

```
public/assets/vendor/
```

At this point, run `bower install`, and bower will resolve all dependencies and
install them where you want.

At any point, you can list what packages bower has installed, as well as the
versions it has installed. The `bower help` command is your friend should those
needs arise.

Closing Thoughts
----------------

I'm quite happy with the various tools emerging to make modern web development
easier by allowing developers to more easily share their work, as well as
ensure that all dependencies are easily installable. Bower is another tool in
my arsenal as a web developer, giving me a consistent set of dependency
management tools from my server-side development all the way to my client-side
application.
