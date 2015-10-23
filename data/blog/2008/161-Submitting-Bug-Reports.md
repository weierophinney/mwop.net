---
id: 161-Submitting-Bug-Reports
author: matthew
title: 'Submitting Bug Reports'
draft: false
public: true
created: '2008-03-08T12:06:13-05:00'
updated: '2008-03-09T10:18:36-04:00'
tags:
    - programming
    - php
---
Full disclosure: I am employed by [Zend](http://www.zend.com/) to program
[Zend Framework](http://framework.zend.com/). That said, the following is all my
opinion, and is based on my experiences with Zend Framework, as well as
answering questions on a variety of mailing lists and with other OSS projects
(PEAR, Solar, and Cgiapp in particular).

One of my biggest pet peeves in the OSS world is vague bug/issue reports and
feature requests. I cannot count the number of times I've seen a report similar
to the following:

> `<Feature X>` doesn't work; you need to fix it **now!**

If such a report comes in on an issue tracker, it's invariably marked critical
and high priority.

What bothers me about it? Simply this: it gives those responsible for
maintaining Feature X absolutely no information to work on: what result they
received, what was expected, or how exactly they were using the feature. The
reviewer now has to go into one or more cycles with the reporter fishing for
that information — wasting everyone's time and energy.

Only slightly better are these reports:

> `<Feature X>` doesn't work — I keep getting `<Result X>` from it, which is
> incorrect.

At least this tells the reviewers what they reporter is receiving… but it
doesn't tell them how they got there, or what they're expecting.

So, the following should be your mantra when reporting issues or making feature
requests:

- What is the minimum code necessary to reproduce the issue or show the desired API?
- What is the expected result?
- What is the actual result?

<!--- EXTENDED -->

What makes up a good issue report?
----------------------------------

A good issue report has to contain the above three points, plain and simple.
Without this information, a reviewer simply does not have the tools with which
to properly deal with the issue.

### Reproduce code

Quite often, you'll find that an application breaks. It is up to you to find the
root cause of that breakage: what minimal amount of code do I need to write in
order to cause the breakage to occur? Sometimes this will require a little
digging — you may have a result that is unexpected, but it may be a symptom of
something breaking earlier.

For example, in working on `Zend_Form` in the past couple weeks, I had a number
of issues reported against how the new `MultiCheckbox` element was working. One
issue noted that `populate()` was not properly populating the checkboxes.

When pressed for reproduce code, the reporter provided the `$_POST` array with
which they were trying to populate the form:

```php
$_POST = array(
    'foo' => array(
        0 => array(0 => 'bar'),
        1 => array(0 => 'baz'),
        2 => array(0 => 'bat')
    )
);
```

In looking at it, I knew immediately that it was related to another issue that
had been reported. In that issue, the reporter noted that the input elements
rendered by `Zend_Form` for `MultiCheckbox` elements had redundant array notation:

```html
<input type="checkbox" value="foo" name="foo[][]" value="bar" />
```

In the former case, we're seeing a symptom of the latter case: the redundant
array notation was causing a form submission that simply could not populate the
element. If the reporter of the former case had looked at the form used to send
the `$_POST` data they posted in the tracker, either they likely would have
noticed the similar issue already reported in the tracker — or I, as the
reviewer, would have been able to quickly mark the bug as a duplicate.

Regardless, the main point is this: using the value of a POST request to
reproduce an issue is not doing your homework. You need to look for the
*minimal* code necessary to reproduce the issue, and the value provided in
`$_POST` is typically a *symptom* of an issue that has already occurred.

Another rule of thumb with creating reproduce code is to keep the environment
minimal. Try writing up *fresh* code in a scratchpad that you can run over and
over again until you get the result that you're trying to report. This does a
few things: it helps simplify the use case causing the issue, and it often will
help you track down exactly where things begin to break. Sometimes, and I can
attest to this, it helps you find places where you're doing things wrong in your
*own* code in the first place, alleviating the need entirely to submit a report.

What does the reviewer do with this code? Well, a *good* developer will use it
as a test case in the unit test suite — which is another reason to keep the
code down to the minimum required to reproduce the issue. This code will often
end up in the test suite in order to document the issue report — as well as to
prove, once a solution is in place, that the issue has been resolved.

The above advice is useful even when reporting a feature request, this
information is useful. The reviewer then gets an idea of the desired API, and
they can write a test case against it.

### Expected Results

In addition to the reproduce case, you should provide the expected results.
These show clearly your expectations of the code. The reviewer can use this
information in several ways:

- In the test suite, the reviewer can use the expected results in assertions to
  verify the issue (or prove that it is now corrected)
- To show where the reporter has flawed assumptions. In some cases, the
  expectations of the code are different than the documented assertions, and the
  reviewer can then point out where the differences lie — which helps to
  educate the reporter in proper usage of the code.
- In the case of a feature request, this will indicate how the reporter expects
  the new feature to behave. The reviewer can then use that expectation as an
  assertion in the test suite.

### Actual Results

The actual results are important as they contrast against the expected results,
showing where the breakage is. If the reviewer cannot recreate these results,
then it likely means that the reproduce code provided is not the actual code
needed to reproduce the issue, or it may mean that environmental differences —
differences in OS or PHP version, for instance — may be a factor in recreating
the issue.

In the case of a feature request, you could omit the actual results, as there won't be any.

### Always search for your issue or feature request

Finally, one additional mantra to add to your repertoire: search the issue
tracker and/or mailing lists *before* reporting an issue or requesting a
feature. I cannot tell you how many bugs I've closed as duplicates, or how many
times I've had to respond to an email with the phrase, "this is a known issue."
It pays to do your homework: search and see if others have made the same
request. In many cases, you may actually find a *solution* to your issue posted
by others — either a way to extend a class to get the behaviour you're
expecting, a patch to the software, or even a note regarding what public release
or snapshot contains a fix. There's no reason to waste people's time by
reporting a known issue.

The best time to search for your issue, believe it or not, is *after* you've
done the other steps. Until you know exactly what code reproduces the issue, and
have clearly defined your expectations and the real results, it can be difficult
to identify when your issue matches another.

In Conclusion
-------------

- What is the minimum code necessary to reproduce the issue?
- What is the expected result?
- What is the actual result?
- Have you searched for similar requests in public forums?

If you can start answering the above questions *before* posting your issues,
you'll start receiving more detailed and useful responses from those reviewing
your issues or feature requests, and reduce the number of "I don't understand"
or "I need more information" responses. Guaranteed.
