---
id: 26-CGIApplicationValidateRM-and-DataFormValidator
author: matthew
title: 'CGI::Application::ValidateRM and Data::FormValidator'
draft: false
public: true
created: '2004-02-05T20:07:43-05:00'
updated: '2004-09-20T13:41:21-04:00'
tags:
    - programming
    - perl
    - personal
---
I've been reading a lot of posts lately on the `CGI::App` mailing list about using
`CGI::Application::ValidateRM` (RM == Run Mode); I finally went and checked it
out.

`CGI::App::ValRM` uses `Data::FormValidator` in order to do its magic.
Interestingly, `D::FV` is built much like how I've buit our `formHandlers`
library at work — you specify a list of required fields, and a list of fields
that need to be validated against criteria, then provide the criteria. It goes
exactly how I would have done our libraries had we been working in perl —
supplying the constraint as a regexp or anonymous sub in a hashref for the
field.

Anyways, it looks like the combination of `CGI::App::ValRM` with `CGI::App`
could greatly simplify any form validations I need to do on the site, which will
in turn make me very happy!
