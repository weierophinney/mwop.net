---
id: 162-Zend-Framework-1.5-is-on-its-way!
author: matthew
title: 'Zend Framework 1.5 is on its way!'
draft: false
public: true
created: '2008-03-16T21:26:54-04:00'
updated: '2008-03-18T21:31:33-04:00'
tags:
    0: php
    2: 'zend framework'
---
As many know, [Zend Framework](http://framework.zend.com/) 1.5.0 is almost ready
for release… heck, it might even be released by the time you read this. There
are a ton of new features worth looking into, but I'll list some of my own
favorites here - the ones I've been either working on or using.

<!--- EXTENDED -->

### Zend_Layout and Zend_View enhancements

I've been using the Two Step View pattern for years now, and its practically
second nature with each project to set it up. With `Zend_Layout` now released, I
no longer have to setup a plugin of my own and think about how it all works — I
can just create my site layout and start working.

But `Zend_Layout` by itself isn't that revolutionary. What *is* revolutionary is
the combination of `Zend_Layout` with a variety of new view helpers: partials,
placeholders, and actions. In particular, placeholders are increasingly finding
a place in my toolbox.

As an example, I use the new `headScript()` and `headLink()` view helpers in my
applications a ton. They allow me to specify javascript and CSS includes for my
HTML head section… and also prevent me from accidently including the same file
multiple times. Add to this the fact that, as placeholders, they can also
capture arbitrary content for later inclusion, I can now create javascript that
I need for a particular view *in that view* and include it in the document head
easily. This helps me keep my UI logic close to the application, while still
keeping it located in the appropriate place in the document.

### Zend_Form

To be honest, about half-way through writing `Zend_Form`, I was seriously
wondering if a form component was necessary; having `Zend_Filter_Input` directly
in your model, and simply writing your forms using `Zend_View` helpers seemed
perfectly straightforward and painless.

However, I've had a chance to play with `Zend_Form` a fair bit since then, and
I'm very much appreciating the component. With it, I can put all the information
about an element — how it should validate, any pre-filtering I want, any
metadata I want to associate with it, and also hints as to how I want it
rendered — all in one place. While it seems like a dangerous mix of business
logic and presentation logic, it's not. The display logic is self-contained in
the decorators used to render the element — and are not even necessary. But
having them present also means that my views can be *much* simpler than they
were in the past, as can my controller and/or model logic — I can pull all the
information in from the form, and simply use it.

As an example, consider this controller action:

```php
<?php
class FooController extends Zend_Controller_Action
{
    public function formAction()
    {
        $request = $this->getRequest();
        $form    = $this->getForm(); // helper method to pull in form

        if (!$request->isPost()) {
            // display form
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($request->getPost())) {
            // form not valid; re-display
            $this->view->form = $form;
            return;
        }

        // Valid form; save data
        $this->getModel()->save($form->getValues());
        $this->_helper->redirector('success');
    }
}
?>
```

And the related view script for displaying the form:

```php
<h2>Please fill out the form</h2>
<?= $this->form ?>
```

Seriously — while there may be a fair amount of code to setup the form, it's
self-contained, and leads to some seriously shorter methods and views — making
the rest of the application logic much cleaner and easier to read. I like clean
and easily read code.

### Zend_Search_Lucene

Sure, this component has been around for a long time, but Alex has done some
increcible stuff with it. You can now build queries programmatically, and have
support for wildcard searches. Additionally, you now have the ability to modify
the index in place, without re-building — which means that as your site grows,
you don't have to worry that indexing the site is going to consume more and more
resources.

### Zend_Db_Table Improvements

One thing that has always been a thorn in my side is that I will often want to
do a query that includes a JOIN from within a `Zend_Db_Table`… but it hasn't
allowed this. This has led to having custom methods that return arrays, or
adding logic that returns custom `ResultSets`… all of which is tedious. Simon
has put in a ton of time in the past few months to make these headaches go away.
You can now return *read-only* resultsets that contain joins, letting you access
your JOIN'ed rowsets just as you would one containing only the current table's
data.

Additionally, instead of needing to build your SQL by hand, or build it with
`Zend_Db_Select` and then cast it to a string, Simon has also added the ability
to create `Zend_Db_Select`'s directly in your table classes, and pass them to
any method that performs a query. This improvement will also save me time —
particularly when coupled with the ability to return JOINed `ResultSet`s.

### Context Switching and REST

Something I've been playing with for some time now is the ability to change view
context by simply changing a parameter in the request. For example, you may want
to return XML or JSON in addition to HTML from a given action. One thing I've
added for 1.5.0 is a `ContextSwitch` action helper that helps automate exactly
this.

With your actions `ContextSwitch` enabled, you can then request an action and add
a `format` parameter with the desired context: `/foo/bar/format/xml`. The
parameter is detected, checked against a list of allowed contexts, and then, if
available, a separate view is rendered and the response headers changed to match
the format.

In the above example, `/foo/bar/format/xml`, this would mean that a response
`Content-Type` header of `text/xml` would be returned, and instead of rendering
the `foo/bar.phtml` view script, the `foo/bar.xml.phtml` script would be
rendered. This allows you to have separate display logic for different contexts.

In addition to context switching, at the request of [some vocal contributors](http://benramsey.com/),
I've added a variety of methods to `Zend_Controller_Request` to allow detecting
the various HTTP request types (HEAD, GET, POST, PUT, DELETE, etc.), as well as
retrieving raw post data.  These, combined with context switching, will allow
you to build REST services into your existing MVC apps trivially.

### Conclusion

There are a ton more features coming out with 1.5.0. [Visit the framework site](http://framework.zend.com/)
in the coming days to see what all is new, and download the new release to give
it a try!
