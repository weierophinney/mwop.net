---
id: 111-mbstring-comes-to-the-rescue
author: matthew
title: 'mbstring comes to the rescue'
draft: false
public: true
created: '2006-05-16T18:25:00-04:00'
updated: '2006-05-18T13:25:33-04:00'
tags:
    - php
---
I've been working with SimpleXML a fair amount lately, and have run into an issue a number of times with character encodings. Basically, if a string has a mixture of UTF-8 and non-UTF-8 characters, SimpleXML barfs, claiming the "String could not be parsed as XML."

I tried a number of solutions, hoping actually to automate it via mbstring INI settings; these schemes all failed. iconv didn't work properly. The only thing that did work was to convert the encoding to latin1 — but this wreaked havoc with actual UTF-8 characters.

Then, through a series of trial-and-error, all-or-nothing shots, I stumbled on a simple solution. Basically, I needed to take two steps:

- Detect the current encoding of the string
- Convert that encoding to UTF-8

which is accomplished with:

```php
$enc = mb_detect_encoding($xml);
$xml = mb_convert_encoding($xml, 'UTF-8', $enc);
```

The conversion is performed even if the detected encoding is UTF-8; the conversion ensures that *all* characters in the string are properly encoded when done.

It's a non-intuitive solution, but it works! QED.
