---
id: 88-Form-madness
author: matthew
title: 'Form madness'
draft: false
public: true
created: '2005-07-28T00:08:00-04:00'
updated: '2005-07-28T10:09:30-04:00'
tags:
    - php
---
Those who follow my blog may remember an [earlier entry on form validation](/blog/83-Thoughts-on-form-validation.html).
I looked into some of the possible solutions those who commented provided, but
other than [Solar_Form](http://solarphp.com/home/index.php?area=Solar_Form),
each was either trying to generate HTML, or not generating HTML was considered
a plugin type of behaviour (i.e., the HTML generation was the norm, and not
generating HTML typically added layers). Discouraged, I continued plugging away
on my own solution which was incorporating Solar_Valid for validations, adding
some filtering, adding the ability to load definitions from a file, and adding
the ability to use callbacks for either filters or validation rules.

I got some code together, tried it out, and felt that either something was
missing, or I was doing something a little wrong. I posted the code source, and
asked Paul Jones for feedback.

After some initial flurries of emails with Paul asking for more detail, me
providing it, Paul asking questions, me attempting to answer… Paul had me look
at the source for Solar_Form. In doing so, I discovered what he was suspecting,
namely, that we were trying to build something similar. Rather than continue on
parallel courses, I decided to jump in and help in this aspect of the
[Solar project](http://solarphp.com/).

<!--- EXTENDED -->

And thus was `Solar_Filter` born, and then `Solar_Form_Load_Xml`.
**Solar_Filter** is a class of static methods that provide ways to filter data;
its primary use would be for pre-filtering form data before validation. In most
cases, you may simply register a PHP function that accepts a value as an
argument and returns a value; otherwise, all filter methods expect the value to
filter as the first argument, and any other arguments follow.
**Solar_Form_Load_Xml** loads a **Solar_Form** definition from an XML file
using PHP5's `simplexml` functions. It simply takes the XML and creates an
array that is compatible with `Solar_Form::setElements()`.

I've also helped with a number of changes to `Solar_Form` — you can now pass
arrays to `validate()` instead of using the `$_POST` and/or `$_GET` arrays
(useful for validating subsets or data coming from XMLHTTP requests), elements
can have pre-filters via `Solar_Filter`, and you can `load()` form element
definitions (currently via `Solar_Form_Load_Xml` only).

You may view Paul's and my handiwork by checking out Solar via the
[Solar subversion repository](http://solarphp.com/svn/); the affected modules
are `Solar_Form`, `Solar_Filter`, and `Solar_Form_Load_Xml`.

Documentation will be forthcoming once we iron out some bugs and get some good
unit tests going. In the meantime, the API documentation is fairly coherent and
informative. Questions may be directed to the [Solar mailing list](http://lists.solarphp.com/mailman/listinfo/solar-talk) .

If you can, start testing so we can incorporate feedback for Paul's next
release of Solar!
