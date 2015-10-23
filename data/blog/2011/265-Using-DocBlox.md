---
id: 265-Using-DocBlox
author: matthew
title: 'Using DocBlox'
draft: false
public: true
created: '2011-08-03T14:00:00-04:00'
updated: '2011-08-05T02:39:58-04:00'
tags:
    - php
---
Until a few years ago, there were basically two tools you could use to generate
API documentation in PHP: [phpDocumentor](http://phpdocumentor.org/) and
[Doxygen](http://www.stack.nl/~dimitri/doxygen/). phpDocumentor was long
considered the standard, with Doxygen getting notice when more advanced features
such as inheritance diagrams are required. However, phpDocumentor is practically
unsupported at this time (though a small group of developers is working on a new
version), and Doxygen has never had PHP as its primary concern. As such, a
number of new projects are starting to emerge as replacements.

One of these is [DocBlox](http://docblox-project.org). I am well aware there are
several others — and indeed, I've tried several of them. This post is not here
to debate the merits or demerits of this or other solutions; the intention is to
introduce you to DocBlox so that you can evaluate it yourself.

<!--- EXTENDED -->

Getting DocBlox
---------------

DocBlox can be installed in a variety of ways:

- You can checkout the project [via GitHub](http://github.com/mvriel/docblox).
- You can [download a snapshot](https://github.com/mvriel/Docblox/zipball/master).
- You can [download a release](https://github.com/mvriel/Docblox/zipball/v0.12.2).
- You can use [use the PEAR installer](http://pear.docblox-project.org/).

I personally prefer using the PEAR installer, as it's as simple as this:

```bash
$ pear channel-discover pear.michelf.com
$ pear channel-discover pear.docblox-project.org
$ pear install -a docblox/DocBlox-beta
```

The first `channel-discover` is to grab a third-party package optionally used in
the rendering process to convert Markdown in the descriptions to HTML. And don't
let the "beta" status fool you — this project is quite stable at this point; the
author, [Mike van Riel](http://blog.naenius.com), is simply being conservative
as he rounds out features.

If you are checking out the project via Git or a snapshot, you simply need to
expand the archive and make a note of its location — when I've used this method
in the past, I usually create a symlink to the `bin/docblox.php` script in my
path:

```bash
$ ln -s path/to/docblox/bin/docblox.php ~/bin/docblox
```

Using DocBlox
-------------

Once you have installed DocBlox, how do you use it? It's really quite easy:

```bash
$ cd some/project/of/yours/
$ mkdir -p documentation/api/
$ docblox run -d path/to/source/ -t documentation/api/
```

At this point, DocBlox will merrily scan your source located in
`path/to/source`, and build API documentation using its default HTML templates
for you in `documentation/api`. Once complete, you can point your browser at
`documentation/api/index.html` and start browsing your API documentation.

Using DocBlox to identify missing docblocks
-------------------------------------------

While running, you may see some notices in your output stream, like the
following:

```
2011-08-02T16:08:34-05:00 ERR (3): No DocBlock was found for Property $request in file Mvc/Route/RegexRoute.php on line 16
```

This output is invaluable for identifying places you've omitted docblocks in
your code. You can capture this information pretty easily using `tee`:

```bash
$ docblox run -d path/to/source/ -t documentation/api/ 2>&1 | tee -a docblox.log
```

I recommend doing this whenever running DocBlox, going through the output, and
adding docblocks wherever you encounter these errors.

(You can do similarly using tools such as
[PHP_CodeSniffer](http://pear.php.net/PHP_CodeSniffer). More tools is never a
bad thing, though.)

If you want to disable the verbosity, however, you can, by passing either the
`-q` or `--quiet` options.

Class Diagrams
--------------

DocBlox will try and generate class diagrams by default. In order to do this,
you need to have [GraphViz](http://www.graphviz.org/) installed somewhere on
your path. The results are pretty cool, however — you can zoom in and out of the
diagram, and click on classes to get to the related API documentation.

(The class diagram is typically linked from the top of each page.)

Specifying an alternate title
-----------------------------

By default, DocBlox uses its own logo and name as the title of the documentation
and in the "header" line of the output. You can change this using the `--title`
switch:

```
$ docblox run -d path/to/source/ -t documentation/api/ --title "My Awesome API Docs"
```

Using alternate templates
-------------------------

While the default template of DocBlox is reasonable, one of its initial selling
points to me was the fact that you could conceivably create new templates. In
order to test this out, and also iron out some of the kinks, Mike wrote
templates for a few PHP OSS projects, including Zend Framework and Agavi.
Templates need to be in a location DocBlox can find them — in
`DocBlox/data/themes` under your PEAR install, or simply `data/themes` if you
installed a release tarball. Invoking a theme is as easy as using the
`--template` argument:

```bash
$ docblox run -d path/to/source/ -t documentation/api/ --title "My Awesome API Docs" --template zend
```

Try out each of the provided themes to see which you might like best — and
perhaps try your hand at writing a theme. Each given theme is simply an XML file
and a small set of XSL stylesheets, and optionally CSS and images to use with
the generated markup.

Iterative documentation
-----------------------

When you generate documentation, DocBlox actually creates a SQLite database in
which to store the information it learns while parsing your code base. This
allows it to be very, very fast both when parsing (it can free information from
memory once it's done analyzing a class or file) as well as when transforming
into output (as it can iteratively query the database for structures).

What does this mean for you?

Well, first, if you want to try out new templates, it won't need to re-parse
your source code — it simply generates the new output from the already parsed
definitions. This can be very useful particularly when creating new templates.
Generation is oftentimes instantaneous for small projects.

Second, it means that you can build the full documentation once, and only
periodically update it (which you can do using the `--force` option). This is
particularly useful for build processes.

Configuration
-------------

One problem with any rich CLI tool is that you often get a proliferation of
options, and remembering them between invocations can be hard (particularly if
you only run the tool during releases). DocBlox allows you to create a
configuration file, `docblox.xml`, in your project. The format is relatively
simple; the (mostly) equivalent to the above options I've used is as below:

```xml
<?xml version="1.0" encoding="UTF-8" ?>                                     
<docblox>
    <parser>
        <target>documentation/api</target>
    </parser>
    <transformer>
        <target>documentation/api</target>
    </transformer>
    <files>
        <directory>path/to/source</directory>
    </files>
    <transformations>
        <template>
            <name>zend</name>
        </template>
    </transformations>
</docblox>
```

You can't specify the title in the configuration, but often that will be
template-driven, anyways.

DocBlox will then look for this file in the current directory and simply use it,
allowing you to invoke it as follows:

```bash
$ docblox run
```

Or you can specify the configuration file yourself:

```bash
$ docblox run -c config.xml
```

(Side note: on the release current as of when I write, 0.12.2, I have not
successfully been able to specify the template name.)

Search
------

If you look carefully at the generated output, you'll notice a search box. By
default, this doesn't work… because it points to a PHP script! When installed on
a server capable of serving PHP, however, it can be used to help find classes,
methods, and more. As an example, you can
[search the Zend Framework 1.11 API documentation](http://framework.zend.com/apidoc/1.11/).

Conclusions
-----------

Hopefully this tutorial will get you started investigating DocBlox. I've been
quite happy with what I've seen so far of the project, and gladly recommend it.
There are other alternatives, however, and I also suggest you try those out;
[Liip recently published a comparison of features](http://blog.liip.ch/archive/2011/07/26/phpdoc-compilers-and-inheritdoc.html),
and that article can be used as a starting point for your own investigations.

*(Disclosure I've contributed a few patches and some advice to Mike van Riel as he's developed DocBlox).*
