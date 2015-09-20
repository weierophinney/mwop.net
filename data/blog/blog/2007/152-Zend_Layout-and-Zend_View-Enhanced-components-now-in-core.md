---
id: 152-Zend_Layout-and-Zend_View-Enhanced-components-now-in-core
author: matthew
title: 'Zend_Layout and Zend_View Enhanced components now in core'
draft: false
public: true
created: '2007-12-19T08:44:11-05:00'
updated: '2007-12-19T12:24:33-05:00'
tags:
    0: php
    2: 'zend framewor'
---
I'm pleased to announce that the [Zend_View
Enhanced](http://framework.zend.com/wiki/pages/viewpage.action?pageId=33071) and
[Zend_Layout](http://framework.zend.com/wiki/display/ZFPROP/Zend_Layout)
components are now in the [Zend Framework](http://framework.zend.com/) core.
With these two components, you can now create some truly
[complex views](http://blog.astrumfutura.com/archives/291-Complex-Views-with-the-Zend-Framework-Part-6-Setting-The-Terminology.html)
for your application with relative ease.

The two components tackle several view related tasks:

- Layouts, or Two Step Views
- Partials (view fragment scripts with their own variable scope)
- Placeholders (store data and/or markup for later retrieval)
- Actions (dispatch a controller action)

<!--- EXTENDED -->

So, what's the big deal? Much, if not all of this, was already possible, I hear
some people saying. Well, yes, technically it was; in fact, all of these, except
layouts, were accomplished by the addition of extra view helpers, which anybody
could have written (and, in fact, some did). However, by having these as a
standard part of the library, there are now standard ways to perform these tasks
— meaning consistency between applications.

Plus, these helpers just make things so much simpler!

For instance, who out there has all the DOCTYPE declarations memorized? I
personally know all the types, but can't rattle off the entire declarations
associated with each to save my life. With the doctype() helper, all I have to
do is:

```php
<?= $this->doctype('XHTML1_TRANSITIONAL') ?>
```

and it's now present. Furthermore, by putting this at the top of my layout, when
I display my scripts as aggregated in the `headScript()` helper, they'll now be
properly escaped as XML CDATA, as helpers that need to be DOCTYPE aware now
determine this information from that helper.

Speaking of the `headScript()` helper, it's pretty handy. Let's say you have an
application that requires javascript. Instead of unconditionally specifying the
javascript include for every controller, or setting up complex logic for
determining when to include it, you can have your application view specify it's
needed:

```php
<?php $this->headScript()->appendFile('/js/foo.js') ?>
```

Then, in your master layout script, you tell it to include any scripts aggregated:

```php
<?= $this->headScript() ?>
```

You can do similarly for specifying feeds (via `headLink()`), stylesheets (via
`headLink()` for external files, `headStyle()` for inline stylesheets), and even
your title element (for instance, you could aggregate your various breadcrumbs,
and then specify a custom separator to use between them).

This is really just the tip of the iceberg. Using a combination of placeholders,
partials, actions, and normal view helpers, you can then create some pretty
complex layouts using minimal markup. As an example:

```php
<?= $this->doctype('XHTML1_TRANSITIONAL') ?>
<html>
    <head>
        <?= $this->headTitle() ?>
        <?= $this->headMeta()->setIndent(8) ?>
        <?= $this->headLink()->setIndent(8) ?>
        <?= $this->headStyle()->setIndent(8) ?>
        <?= $this->headScript()->setIndent(8) ?>
    </head>
    <body>
        <?= $this->partial('header.phtml') ?>
        <div id=\"content\">
            <?= $this->layout()->content ?>
        </div>
        <?= $this->subnav() ?>
        <?= $this->partial('footer.phtml') ?>
        <?= $this->inlineScript() ?>
    </body>
</html>
```

The example above makes use of several placeholders (`doctype`, `HeadTitle`,
`HeadMeta`, `HeadLink`, `HeadStyle`, `HeadScript`, and `InlineScript`), two
partials (for the header and footer), layout content, and a custom view helper
(for navigation); the entire thing is less than 20 lines long, yet contains
everything necessary for your site layout.

The functionality of these new components is not only broad, but deep as well,
and can't be covered in a single blog post. Look for a series of tutorials on
the [Zend Developer Zone](http://devzone.zend.com/) detailing them in the coming
weeks. In the meantime, you can read the documentation available in the ZF
subversion repository.
