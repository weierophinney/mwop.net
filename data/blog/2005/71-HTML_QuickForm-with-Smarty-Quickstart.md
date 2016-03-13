---
id: 71-HTML_QuickForm-with-Smarty-Quickstart
author: matthew
title: 'HTML_QuickForm with Smarty Quickstart'
draft: false
public: true
created: '2005-05-07T23:11:38-04:00'
updated: '2005-05-07T23:57:40-04:00'
tags:
    - php
---
I've been wanting to play with
[HTML_QuickForm](http://pear.php.net/package/HTML_QuickForm) for quite some
time, but the documentation has looked rather sparse and scary regarding the use
of `HTML_QuickForm` with [Smarty](http://smarty.php.net/). Since I've been busy
at work, and I haven't wanted to take the time to learn a new library, I've
simply been putting it off.

Last night, I browsed through the package documentation, and noticed a link to
an [HTML_QuickForm Getting Started Guide](http://www.thelinuxconsultancy.co.uk/quickform.php)
by Keith Edmunds. I was pleased to discover that he also has a
[guide to using Smarty with HTML_QuickForm](http://www.thelinuxconsultancy.co.uk/smarty-guide.php).
I got started with these tutorials, and found them excellent. I found myself
wanting a little more meat afterwards, and found that I could now turn to the
PEAR docs and actually make sense of it all.

While I think Mr. Edmunds tutorials are great for starters, I found that there
were a few pointers I could have used right off the bat. I present them here for
you.

<!--- EXTENDED -->

I'm not going to do any code samples; at this point, you should have read the
aformentioned tutorials. The following notes are to give a little more
information to help get you running *faster* with `HTML_Quickform` + Smarty — by
giving some practical tools and steps for utilizing the combo.

The way I see it, creating and using forms with `HTML_QuickForm` + Smarty uses
the following steps:

1. **Create the form elements**. This is done via the `addElement()` method of
   HQF, and involves setting a form element type, name, and label.
2. **Create form input filters (Optional)**. This is done via the
   `applyFilter()` method of HQF; commonly, you may want to `trim()` input, and,
   if `magic_quotes` are on, `stripslashes()`.
3. **Create validation rules**. This is done via the `addRule()` method, which
   takes the element name, the text to display on error, and the rule type as
   arguments (and a fourth argument if required by the rule type). There's some
   really slick stuff you can do here:
   - **You can add more than one rule to any given element.** For instance, a
     particular element may need to be both 'required' and match a given regex;
     you can add rules for both of these
   - The **regex** rule lets you pass a fourth argument to `addRule()` with the
     regex the value should match.
   - The **email** rule makes it so you don't have to remember a regex for
     emails every time you write a form.

4. **Test for validation**. What I discovered is that it's actually nice to **do
   this as an if-then-else block**. This allows you to do different things based
   on whether or not the `$form->validate()` succeeds; for instance, you may not
   wish to display the same template on success as on failure. For certain, you
   will not need to do anything with the renderer if the form validates
   (assuming you don't display the form or its values again).
5. **Set renderer options**. Basically, if the form fails validation, you'll
   need to display it again. Most likely, you want to display something new back
   to the user: what elements had errors, and the error message for them.
   Additionally, you may want to show which elements are required. Each renderer
   supplies several methods to allow such customization. These methods each can
   display elements:
   - `{$label}` — the form element's label
   - `{$html}` — the HTML for the form element
   - `{$error}` — the error text for the form element
   - `{$required}` — a flag indicating whether or not an element is required

   If either the `{$label}` or `{$html}` element is referenced in the template
   passed to one of these methods, the appropriate field (label or html) for
   that form element will be affected before being passed to the master form
   template.

   The following methods are supplied by a renderer class:

   - **setRequiredTemplate()**. Pass it a template as a string; this template
     will be called for each *required* element. So, you'll typically want to
     modify the `{$label}` element in the template, usually with some sort of
     flag, like an asterix.
   - **setErrorTemplate()**. Pass it a template as a string; this template will
     be displayed if an error occurs for a given element. Again, like
     `setRequiredTemplate()`, you'll typically want to modify the label, this
     time by appending the error string.
   - **setRequiredNote()**. Pass it a template as a string; this template can be
     displayed by calling the `$form_data.requirednote` field in the template
     (assuming you assign your form data to `$form_data`). This should be simply
     some text to the effect of "* These fields are required." (and really, in a
     Smarty template, this probably isn't necessary).

6. **Freeze and Process**. If the form validates, you should then freeze it,
   and, if necessary, process the submission. HQF has a method, 'process()',
   that allows you to pass the values to a callback. However, you might find it
   easiest to simply take the values and process them yourself. You can do this
   easily and safely with the `exportValues()` method, which returns an
   associative array with form key =&gt; value pairs as they exist *post
   filtering*; additionally, it will only pass an array containing those
   elements handled by the form (i.e., no other values, even if passed, will be
   returned).

#### Parting Notes

To my thinking, a config script/class/file could be written that would perform
the first three steps, defining the form elements, their filters, and their
validations; this could even be extended to handle the renderer options. Then,
it would be a simple matter of calling that, testing for validation, and then
either re-displaying the form or processing it with the values returned.

(I'll probably use the above to create a Cgiapp plugin for just this very
purpose, similar in nature to `CGI::Application`'s `Data::FormValidator`
plugin.)
