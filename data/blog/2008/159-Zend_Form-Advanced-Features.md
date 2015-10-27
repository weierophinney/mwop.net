---
id: 159-Zend_Form-Advanced-Features
author: matthew
title: 'Zend_Form Advanced Features'
draft: false
public: true
created: '2008-02-08T22:56:00-05:00'
updated: '2008-04-07T14:39:15-04:00'
tags:
    0: php
    2: 'zend framework'
---
I've been working on
[Zend_Form](http://framework.zend.com/wiki/display/ZFPROP/Zend_Form) for the
past few weeks, and it's nearing release readiness. There are a number of
features that Cal didn't cover in his [DevZone coverage](http://devzone.zend.com/article/3030-Lifting-the-Skirt-on-Zend-Framework-1.5---Zend_Form)
(in part because some of them weren't yet complete) that I'd like to showcase,
including:

- Internationalization
- Element grouping for display and logistical purposes
- Array support

This post will serve primarily as a high-level overview of some of these
features; if you're looking for more in-depth coverage, please review the unit
tests. :-)

<!--- EXTENDED -->

### Internationalization

When using form components in many libraries, internationalization (i18n) is
often tricky to accomplish. There are many potential translation targets:
labels, submit and reset buttons, and error messages all potentially need to be
treated.

`Zend_Form` allows setting a translation object at both the element and form
level, and also allows setting a default translation object for all forms and
elements. I personally feel this latter is the most flexible; in most projects,
you'll have a single set of translation files, so why not simply re-use the same
object throughout?

```php
// Create your translation object
$translate = new Zend_Translate(...);

// Set it as the default object for all forms and elements:
Zend_Form::setDefaultTranslator($translate);
```

What do you get for this?

- Legends. If a fieldset legend has a translation available, it will be translated.
- Labels. If the label you provide has a translation available, it will be used.
- Buttons. Submit, reset, and regular form button values will be translated.
- Error messages. Validation error messages will be translated, *with* any value substitutions as provided by `Zend_Validate`.

In other words, translation in `Zend_Form` is pretty well integrated.

### Element Grouping

In `Zend_Form`, we distinguish between two types of element grouping: grouping
for display purposes (DisplayGroups) and grouping for logistical purposes (Sub
Forms)

With DisplayGroups, you're basically saying you want to visually or semantically
group elements together on the page. Usually (and by default) this is done with
fieldsets. DisplayGroups provide a simple mechanism for doing this. The elements
remain children of the parent form object, but are rendered within the display
group.

Other times, you want to group the elements logically. For instance, you might
want to group a billing address separately from a shipping address. This
grouping may be simple namespacing under array keys (I'll cover this more
later), shared filters or decorators, or, in advanced use cases, separate pages
of a multi-page form.

`Zend_Form`'s answer to these situations are "Sub Forms". They are actually a
subclass of `Zend_Form`, and the only real difference is the class and the
default decorators used (by default, they render in a fieldset). Since they
share the same functionality as a regular form, this means they can validate
their elements, render themselves, etc. However, `Zend_Form` itself *cannot*
iterate over or render a sub forms elements; only the sub form can do that.

One potentially powerful use case for sub forms is for multi-page forms. You
could easily create a form consisting of several sub forms, and display a single
sub form per page, persisting data in the session between form submissions; only
when all pages have received their data would the parent form be valid, allowing
you to finally pass the data to the model.

Form grouping at the display and logical level both are powerful tools, and this
functionality is trivial with `Zend_Form`.

#### Array Support

Many developers like to namespace their form elements under nested arrays. This
allows for groupings of related data, as well as having several groups with
similar data on the same page. As an example, imagine a form showing both a
shipping and a billing address:

```html
<form action="/foo/bar" method="post">
    <fieldset>
        <legend>Shipping Address</legend>
        <dl>
            <dt>Address:</dt>
            <dd><input name="shipping[address]" type="text" value="" /></dd>
            
            <dt>City:</dt>
            <dd><input name="shipping[city]" type="text" value="" /></dd>
            
            <dt>Postal:</dt>
            <dd><input name="shipping[postal]" type="text" value="" /></dd>
        </dl>
    </fieldset>

    <fieldset>
        <legend>Billing Address</legend>
        <dl>
            <dt>Address:</dt>
            <dd><input name="billing[address]" type="text" value="" /></dd>
            
            <dt>City:</dt>
            <dd><input name="billing[city]" type="text" value="" /></dd>
            
            <dt>Postal:</dt>
            <dd><input name="billing[postal]" type="text" value="" /></dd>
        </dl>
    </fieldset>
</form>
```

PHP will receive two arrays from the submitted form, 'shipping' and 'billing'.

`Zend_Form` now allows this (as of today). To keep all existing features, and to
allow elements and sub forms to stay de-coupled from their parent forms, you
need to do a little configuration:

```php
$shipping = new Zend_Form_SubForm('shipping');

// This next line tells the elements, validators, and decorators that they are
// part of an array; by default, the sub form name is used:
$shipping->setIsArray(true);

// This can also be accomplished by explicitly setting the array name:
$shipping->setElementsBelongTo('shipping');
```

The fun part is that this can be arbitrarily deep, by specifying the array key
as it would appear in the form. So, as an example, if we wanted the entire form
returned in the 'demographics' array, and 'shipping' and 'billing' were keys in
that array, we could do the following:

```php
// Set base key for entire form:
$form->setElementsBelongTo('demographics');

// Set subkey for shipping sub form:
$shipping->setElementsBelongTo('demographics[shipping]');

// Set subkey for billing sub form:
$billing->setElementsBelongTo('demographics[billing]');
```

When you set or retrieve values, or validate, these array keys are honored.
What's more, since they are configurable, you can leave them out of your generic
forms, and only set them in your concrete instances — allowing re-use and
re-purposing.

### Conclusion

This post is mainly to serve as high-level overview of some of the more advanced
features of `Zend_Form`. In the coming weeks, more thorough documentation will be
present in the Zend Framework repository, allowing developers to understand the
functionality in more depth. Hopefully I've whetted some people's appetites out
there, and we'll get more of you experimenting with the current code base.

**Update:** fixed array notation HTML example to show separate billing and shipping addresses.
