---
id: 28-HTMLFillInForm
author: matthew
title: 'HTML::FillInForm'
draft: false
public: true
created: '2004-02-05T20:23:17-05:00'
updated: '2004-09-20T13:45:33-04:00'
tags:
    - programming
    - perl
    - personal
---
The `CGI::Application::ValidateRM` module utilizes `HTML::FillInForm` to fill in
values in the form if portions did not pass validation. Basically, it utilizes
`HTML::Parser` to go through and find the elements and match them to values.
It's used because the assumption is that you've built your form into an
`HTML::Template`, and that way you don't need to put in program logic into the
form.

Seems another good candidate for using `FillInForm` would be to populate a form
with values grabbed from a database… I should look into that as well!
