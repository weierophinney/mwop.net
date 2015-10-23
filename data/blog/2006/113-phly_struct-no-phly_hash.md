---
id: 113-phly_struct-no-phly_hash
author: matthew
title: 'Phly_Struct? no, Phly_Hash...'
draft: false
public: true
created: '2006-05-22T17:08:00-04:00'
updated: '2006-05-23T08:56:27-04:00'
tags:
    - php
---
After some discussion with [Paul](http://paul-m-jones.com/blog/) and [Mike](http://mikenaberezny.com/), I was convinced that 'Struct' was a bad name for `Phly_Struct`; structs are rarely if ever iterable, and one key feature of `Phly_Struct` is its iterable nature.

The question is: what to name it? Associative arrays go by a variety of names in different languages. In Perl, they're 'hashes'; Ruby and Javascript, 'collections'; Python, 'dictionaries'. I ruled out `Phly_Dictionary` immediately, as (a) I don't want it to be confused with online dictionaries, and (b), it's too long. The term 'Collection' also feels too long (although I write things like `Cgiapp2_ErrorException_Observer_Interface`, so I don't know why length should be such an issue), as well as unfamiliar to many PHP developers. Hash can imply cryptographic algorithms, but, overall, is short and used often enough in PHP circles that it makes sense to me.

So, I've renamed `Phly_Struct` to [Phly_Hash](http://weierophinney.net/phly/index.php?package=Phly_Hash), and updated `Phly_Config` to use the new package as its dependency. In addition, I've had it implement `Countable`, so you can do things like:

```php
$idxCount = count($struct);
```

Go to the [channel page](http://weierophinney.net/phly/) for instructions on adding Phly to your PEAR channels list, and grab the new package with `pear install -a phly/Phly_Hash`, or `pear upgrade -a phly/Phly_Config`.
