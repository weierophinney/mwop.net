---
id: 163-Using-Zend_View-Placeholders-to-Your-Advantage
author: matthew
title: 'Using Zend_View Placeholders to Your Advantage'
draft: false
public: true
created: '2008-03-18T17:13:26-04:00'
updated: '2008-03-18T21:22:00-04:00'
tags:
    0: php
    2: 'zend framework'
---
Somebody asked for some examples of how I use the `headLink()`, `headScript()`,
and other placeholder helpers, so I thought I'd take a crack at that today.

First off, let's look at what these helpers do. Each are concrete instances of a
*placeholder*. In Zend Framework, placeholders are used for a number of
purposes:

- Doctype awareness
- Aggregation and formatting of aggregated content
- Capturing content
- Persistence of content between view scripts and layout scripts

Let's look at these in detail.

<!--- EXTENDED -->

Doctype Hinting
---------------

The HTML specification encourages you to use a DocType declaration in your HTML
documents — and XHTML actually requires one. Simply put, the DocType helps tell
your browser what is considered valid syntax, as well as provides some hints to
how it should render.

Now, if you're like me, these are a pain to remember; the syntax is somewhat
arcane, very long, and not something I want to type very often. Fortunately, the
new `doctype()` helper allows you to use mnemonics such as
`XHTML1_TRANSITIONAL` or `HTML4_STRICT` to invoke the appropriate doctype:

```php
<?= $this->doctype('XHTML1_TRANSITIONAL') ?>
```

However, a doctype isn't just a hint to the browser; it's a contract that you
need to follow. If you select a particular doctype, you're agreeing to write
markup that follows the specification for it.

The `doctype()` helper is actually used internally in many of the placeholder
helpers (as well as the `form*()` helpers) to ensure that the markup they
generate — if any — adheres the the given doctype. However, for this to work,
you need to specify your doctype early. I recommend doing it either in your
bootstrap or in a plugin that runs before any output is emitted; typically, I
will pull the view from the `ViewRenderer` in order to do so:

```php
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
$viewRenderer->initView();
$viewRenderer->view->doctype('XHTML1_TRANSITIONAL');
```

Since this sets the doctype helper's state, you can then simply echo the return
value of the doctype helper later in your layout script:

```php
<?= $this->doctype() ?>
```

Content Aggregation
-------------------

Placeholders aggregate and store content across view instances. By aggregate, I
mean that they store the data provided in an `ArrayObject`, allowing you to
collect related data for later display. Since placeholders imlement
`__toString()`, and can be collections, we've added accessors to allow you to
set arbitrary text to prefix, append, and separate the items in the collection.
The various concrete placeholders — primarily the `head*()` helpers — make use
of this particular feature, storing each entry as a separate item in the
collection, and then decorating them when called on to render.

Additionally, the concrete instances each contain some custom logic. In the case
of `headLink()` and `headScript` helpers, we perform checks to ensure that when
specifying files, duplicate entries are ignored. Why is this a good idea? Well,
since you can `_forward()` to other actions, or even call the `action()` view
helper, you could potentially have multiple view scripts loading the same
stylesheets or javascript; we help protect against such situations.

So, as an example:

```php
<? // /foo/bar view script: ?>
<? 
$this->headLink()->appendStylesheet('/css/foo.css'); 
$this->headScript()->appendFile('/js/foo.js'); 
echo $this->action('baz', 'foo');
?>

<? // /foo/baz view script; ?>
<?
$this->headLink()->appendStylesheet('/css/foo.css'); 
$this->headScript()->appendFile('/js/foo.js'); 
?>
FOO BAZ!
```

It's a contrived example, for sure, but it shows the problem quite well: if two
view scripts are rendered during creation of the same content, then you have the
potential for duplicate content in your placeholders. However, in this case, the
duplicate content will not occur, as the helpers detect the duplicate entries
when they're added, and skip them.

Capturing Content
-----------------

One way in which placeholders aggregate content is by *capturing* content. The
base placeholder class defines both a `captureStart()` and `captureEnd()`
method, allowing you to create content in your view scripts that you then
capture for use later.

This is particularly useful for the `headScript()` helper, as it allows you to
create javascript directly in your view that will be executed in the HTML head
(or, if you use the `inlineScript()`) helper, you can have it executed at the
end of your document, which is what Y!Slow recommends). The same goes for the
`headStyle()` helper; you can define custom stylesheets to include directly in
your document directly with the view that needs them.

As an example, [Dojo](http://dojotoolkit.org/) ships with some custom
stylesheets for rendering its various widgits, and also has the ability to load
custom classes and widgets dynamically. Let's say we want to present a Dojo
ComboBox in our page: we'll need a couple of stylesheets, as well as a few Dojo
resources:

First, let's tackle the stylesheets:

```php
<? $this->headStyle()->captureStart() ?>
@import \"/js/dijit/themes/tundra/tundra.css\";
@import \"/js/dojo/resources/dojo.css\";
<? $this->headStyle()->captureEnd() ?>
```

These are now aggregated in our `headStyle()` view helper, and we can render
them later; they will not appear inline in the page as they do here in the view
script.

Now, let's tackle the javascript. We need to load the main `dojo.js` file as a
script, and then create an inline script to load our various widgets. Dojo often
uses its own custom HTML attributes, and the `head*()` helpers typically don't
like this (they like to stick to those attributes defined in the specs), so
we'll need to tell the helper that this is okay so that Dojo will parse the page
when it finishes loading (to decorate our widget with the appropriate, requested
functionality).

```php
<? $this->headScript()
        ->setAllowArbitraryAttributes(true)
        ->appendFile('/js/dojo/dojo.js', 'text/javascript', array('djConfig' => 'parseOnLoad: true'))
        ->captureStart() ?>
djConfig.usePlainJson=true;
dojo.require(\"dojo.parser\");
dojo.require(\"dojox.data.QueryReadStore\");
dojo.require(\"dijit.form.ComboBox\");
<? $this->headScript()->captureEnd() ?>
```

What's the benefit to doing this? It allows you to keep the JS and CSS
functionality that's related to the specific view script at hand *with* that
view script — you have everything in one place. If you need to change what JS or
CSS is loaded, or modify the inline JS you're going to utilize, you can find it
with the rest of the content to which it applies.

Putting it Together: the Layout
-------------------------------

I keep talking about "when you render it later" in this narrative. "Later"
refers to your layout script. I'm not going to go into how you initialize or
define your layouts here, as it's been covered in
[other](/blog/162-Zend-Framework-1.5-is-on-its-way!.html)
[places](http://akrabat.com/2007/12/11/simple-zend_layout-example/). However,
let's look at how we can pull in our doctype and head helpers into our layout:

```php
<?= $this->doctype() ?>
<html>
    <head>
        <? // headTitle() is another concrete placeholder ?>
        <?= $this->headLink() ?> 
        <?= $this->headStyle() ?> 
        <?= $this->headScript() ?> 
    </head>
    ...
```

Sure, you may want to put more in there than that — if you have stylesheets or
scripts that load on every page, you may want to define them statically in the
layout… in addition to calling the placeholder helpers. But adding the
placeholder helpers gives you some definite benefits: increased separation of
code, more maintainable code (as the CSS and JS specific to a view is kept
*with* the view), simpler logic in your layouts, and the ability to prevent
duplicate file inclusions.

All this functionality is now standard with Zend Framework 1.5.0; if you haven't
given it a try, [download it today](http://framework.zend.com/download).

**Note:** my colleague, Ralph Schindler — the original proposal author of
`Zend_Layout` and a substantial contributor to the various placeholder helpers —
is [giving a webinar on Zend_Layout and Zend_View](http://www.zend.com/en/company/news/event/webinar-zend-layout-and-zend-view-enhancements)
tomorrow, 18 March 2008; if you're interested in this topic, you should check it
out.

**Updated:** fixed links to layout articles.
