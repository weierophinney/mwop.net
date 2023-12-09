---
id: 2023-12-09-advent-forms
author: matthew
title: 'Advent 2023: Forms'
draft: false
public: true
created: '2023-12-09T11:00:00-06:00'
updated: '2023-12-09T11:00:00-06:00'
tags:
    - advent2023
    - forms
    - php
    - validation
---
The first thing I was tasked with after I moved full time to the Zend Framework team (17 years ago! Yikes!) was to create a forms library.
Like all the work I did for ZF in the early days, I first created a working group, gathered requirements, and prioritized features.
There were a _lot_ of requests:

- Ability to normalize values
- Ability to validate values
- Ability to get validation error messages
- Ability to render HTML forms, and have customizable markup
- Ability to do nested values
- Ability to handle optional values
- Ability to report missing values

and quite a lot more.
But those are some of the things that stuck out that I can remember off the top of my head.

[Zend_Form](https://framework.zend.com/manual/1.12/en/zend.form.html) was considered a big enough new feature that we actually bumped the version from 1.0 to 1.5 to call it out.

And, honestly, in hindsight, it was a mistake.

<!--- EXTENDED -->

### A mistake?

Considering the timeframe when I was developing Zend_Form, it was actually a good effort, and it's still one of those features that folks tell me sold them on the framework.
But within a year or two, I was able to see some of the drawbacks.

I first realized the issues when we started integrating the [Dojo Toolkit](https://dojotoolkit.org/) with ZF.
We ended up having to create first a suite of Dojo-specific form elements, and second a whole bnch of Dojo-specific _decorators_, which were what we used to render form elements.
While the library gave us this flexibility, I saw a few issues:

- **Duplication.**
  We had multiple versions of the same form elements, and it was actually possible to get the wrong version for your form context.
  And with duplication comes increased maintenance: any time we fixed an issue in one element, we had to check to see if the same issue existed with the Dojo versions, and fix them there as well.
- **Javascript**.
  One of the reasons for integrating Dojo was to allow doing fun things like client-side validation; this allowed giving early feedback, without a round-trip to the server.
  But this also meant that we had validation logic duplicated between the server-side and client-side logic.
  And more interestingly: the form might be sent as a request _by javascript_, instead of a standard form request, which meant that we needed to validate it only, and then serialize validation status and messages.
  Basically, all the rendering aspects of the form were irrelevant in this scenario.
  Which brings me to...
- **APIs.**
  Around this time, APIs started trending.
  It would be a few years before REST became popular and commonly understood by developers, but folks were starting to see that we'd be needing them for the nascent mobile application markets, and that they were going to be a useful way to conduct business-to-business transactions.
  Once you start having APIs in the mix, a library centered on _web forms_ becomes less interesting.

By the time we started planning for version 2 of ZF, we realized we'd need to reconsider how we did forms.
The first step we took was splitting the validation aspect from the form aspect, and created `Zend\InputFilter` to address the first, and `Zend\Form` to address the second.
Input filters encapsulated how to filter, normalize, and validate incoming data.
Forms composed an input filter, and then provided hints for the view layer to allow rendering the elements.
This separation helped a fair bit: you could re-use input filters for handling API or JS requests easily, while the form layer helped with rendering HTML forms.

But I still feel we didn't get it right:

- Our validation component and our input filter component were each _stateful_.
  When you performed validation, each would store the values, validation status, and validation messages as part of the state.
  This makes re-use within the same request more difficult (it was not uncommon to use the same validator with multiple elements, and this now required multiple instances), makes testing more difficult, and makes it harder to understand if the instance represents the definition, or the results of validation.
- The longer I've worked in web development, the more I've realized that while the HTML generation aspects of these form libraries are useful for prototyping, they inevitably cannot be used for the final production code.
  Designers, user experience experts, and accessibility developers will each want different features represented, and these will _never_ fall into the defaults the framework provides.
  Even if the framework provides customization features, the end result is _more_ programming effort.
  It's almost always better to code the HTML markup in your templates, and then feed state (e.g., element IDs/names, validation state, whether or not to display placeholders and/or error messages, etc.) from some object representing form or element state.

A few years back, I started an RFC for Laminas to create an idempotent validation library, one that would not even consider web form integration, but never quite hit on a good design.
What with my work role changing, and having more and more varied interests outside work, I essentially abandoned it.

### Uh oh, I did it again

Until recently.

I develop a number of internal tools for work to support some of the different functional teams with whom I work.
These often require validation at some point, with varying amounts of complexity.
As such, I've used these tools as a way for me to play with some of these ideas around validation and forms.

In developing the last couple of tools, I found a pattern that was working.
I decided to extract it, and then iterated on it some more.
Each iteration, I'd update one of these applications to see how it worked, what it enabled, and what was getting in the way.

I came up with a few goals:

- Provide an idempotent way to validate individual items and/or data sets.
- Provide an extensible framework for developing validation rules.
- Allow handling optional data, with default values.
- Allow reporting validation error messages.
- Ensure missing required values are reported as validation failures.
- Use as few dependencies as possible.

I also came up with some explicit _non-goals_:

- Creating an extensive set of validation rule classes.
- Providing extensive mechanisms for validating and returning nested data sets.
- Providing a configuration-driven mechanism for creating rule sets.
- Providing HTML form input representations or all metadata required to create HTML form input representations.

What I wanted was something that could validate an incoming data set, return a validation result, and then use that result to report back to the user.
In the case of an API, for an invalid result, I'd be able to get the validation error messages, which could then be used to seed a [Problem Details for HTTP APIs](https://www.rfc-editor.org/rfc/rfc7807) message.
In the case of a web form, I'd be able to extract values, validation status, and validation error messages.

One thing I realized early on was that it was also useful to be able to represent a form's _initial state_.
This would allow using the same template for both the initial form, as well as reporting form validation errors later.

Finally, I wanted a solution that reported types and would play nicely with static analysis.
If I'm pulling a result out of a result set, I want to know that the value _type_ is what I expect it to be.
This helps with testing, provides IDE hinting, and helps ensure I'm using the features correctly.
I think I ended up spending more time on this aspect than anything.

The result is my [phly/phly-rule-validation](https://github.com/phly/phly-rule-validation) library.
I developed it for PHP 8.2 and up, as I wanted to use some specific features (though the ones specific to 8.2 and up... I ended up having to remove, so it would likely work on 8.0 or 8.1 as well).
It's a little over 600 lines of code in total, and has no additional dependencies.
It's also incredibly sparse; I only include 2 default validation rules.

The basic idea is:

- You create a _rule set_, consisting of _rules_.
- A _rule_ defines:
  - The _key_ it maps to in the data set being validated.
  - A method for _validating_ a value, which produces a _result_.
  - A way to produce _results_ for each of a _default_ value, and when the value is _missing_.
- Rule validation produces a _result_, which composes:
  - The _key_ associated with the result.
  - The _value_ associated with the result.
    The validation routine _can_ normalize the result if desired, so this value might not be 1:1 with what was submitted.
    This approach allowed me to not require splitting filtering/normalization from validation, as it becomes an implementation detail.
  - The _validation state_: is it valid, or not?
  - The validation _message_: this will generally only be populated for _invalid_ values, and representes a validation _error message_.
- A _rule set_ produces a _result set_, which is a collection of _results_.

In all cases, there are static analysis templates provided to allow defining the _types_.
A validation result allows defining the _value type_, and a result set allows mapping keys to specific result types.
Rules return result types.
And so on.

A rule set can produce a _valid result set_, and this can be used to seed the initial state of a form.
And I built support for _nested results_, which allows having forms that have groups of data.

The library provides usage examples, and I wrote [quite a bit of documentation](https://github.com/phly/phly-rule-validation/tree/0.2.x/docs), if you want to see how it works.

### Some thoughts

Is the result perfect?
Probably not.
I know that folks used to things like ZF, Laminas, Symfony, or Laravel forms will likely dislike the approach, as it does not allow for quick prototyping of web forms.
I don't find that to be a detriment, however; as I noted earlier, the final production version of a form is likely going to be created by a designer, and won't work well with the HTML generation aspects of these systems anyways.
For folks who only want to validate API payloads, while it will be a nice, lightweight approach, it doesn't provide a lot of defaults.
Again, that's by design, as it allows developers to customize their validation logic and, more importantly, test it independently.

I've updated some of my applications to use this library.
In some cases, I had a net reduction of code.
In others, I ended up with more, but a far clearer understanding of what's in a form, how each item is validated, and what types are expected.
And since the bulk of phly-rule-validation is around interfaces, it means that I'm not concerned about _how the library works_; it's pretty clear how it _will_ work just from viewing the classes I've created.

One benefit of creating the library is that it helped me better understand [Psalm](https://psalm.dev) and type templates.
There are definitely limitations, and some things produce WTF moments, but when it all comes together, it's kind of magical.
In some forms I built, it was amazing to be in a view template and get completion for everything, along with an understanding of what various types were, and warnings when I was doing an operation that couldn't use the type for a given element.

And these are the reasons I developed the library.
I wanted something explicit, idempotent, and static analysis friendly, as these would make testing and IDE integration more straight-forward.
I think I succeeded in that goal.
