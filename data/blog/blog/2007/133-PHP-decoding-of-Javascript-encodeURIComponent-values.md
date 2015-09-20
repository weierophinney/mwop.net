---
id: 133-PHP-decoding-of-Javascript-encodeURIComponent-values
author: matthew
title: 'PHP decoding of Javascript encodeURIComponent values'
draft: false
public: true
created: '2007-01-31T12:36:53-05:00'
updated: '2007-02-01T13:33:48-05:00'
tags:
    - php
---
Recently, I was having some issues with a site that was attempting to use UTF-8
in order to support multiple languages. Basically, you could enter UTF-8
characters — for instance, characters with umlauts — but they weren't going
through to the web services or database correctly. After more debugging, I
discovered that when I turned off javascript on the site, and used the
degradable interface to submit the form via plain old HTTP, everything worked
fine — which meant the issue was with how we were sending the data via XHR.

We were using [Prototype](http://prototypejs.org), and in particular, POSTing
data back to our site — which meant that the UI designer was using
`Form.serialize()` to encode the data for transmission. This in turn uses the
javascript function `encodeURIComponent()` to do its dirty work.

I tried a ton of things in PHP to decode this to UTF-8, before stumbling on
[a solution written in Perl.](http://www.garayed.com/perl/218742-how-decode-javascripts-encodeuricomponent-perl.html)
Basically, the solution uses a regular expression to grab urlencoded hex values
out of a string, and then does a double conversion on the value, first to
decimal and then to a character. The PHP version looks like this:

```php
$value = preg_replace('/%([0-9a-f]{2})/ie', \"chr(hexdec('\1'))\", $value);
```

We have a method in our code to detect if the incoming request is via XHR. In
that logic, once XHR is detected, I then pass `$_POST` through the following
function:

```php
function utf8Urldecode($value)
{
    if (is_array($value)) {
        foreach ($key => $val) {
            $value[$key] = utf8Urldecode($val);
        }
    } else {
        $value = preg_replace('/%([0-9a-f]{2})/ie', 'chr(hexdec($1))', (string) $value);
    }

    return $value;
}
```

This casts all UTF-8 urlencoded values in the `$_POST` array back to UTF-8, and
from there we can continue processing as normal.

Man, but I can't wait until PHP 6 comes out and fixes these unicode issues…
