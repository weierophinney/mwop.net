---
id: 81-Notes-on-PEAR_Exception
author: matthew
title: 'Notes on PEAR_Exception'
draft: false
public: true
created: '2005-07-06T22:26:19-04:00'
updated: '2005-07-10T11:36:34-04:00'
tags:
    - php
---
I've been doing some thinking on exceptions, and
[PEAR_Exception](http://pear.php.net/package/PEAR/docs/1.3.3.1/PEAR/PEAR_Exception.html)
in particular. You may want to skip ahead to read about how to use
`PEAR_Exception`, as well as some of my thoughts on the class on first use. If
you want the background, read on.

I've created a package proposal on PEAR for a package called
[File_Fortune](http://pear.php.net/pepr/pepr-proposal-show.php?id=263), an OOP
interface to reading and writing fortune files. I've been using a perl module
for this on the family website for years, and now that I'm starting work on the
PHP conversion, I thought I'd start with the building blocks.

In creating the proposal, I started with a PHP5-only version, though I found
that I wasn't using much in PHP5 beyond the `public`/`private`/`protected`/`static`
keywords. For error handling, I decided to try out
[PEAR_ErrorStack](http://pear.php.net/packages/PEAR_ErrorStack), as I'd been
hearing buzz about it being the new "preferred" method for error handling in
PEAR. (Honestly, after using it, I'm not too happy with it; throwing
`PEAR_Error`s was much easier, and easier to manipulate as well — but that's a
subject for another post — and exceptions were easier still, though more
typing.)

The first comment I got on the proposal was *the* question: "Why PHP5?"
([Paul](http://paul-m-jones.com/blog/) wasn't too surprised by that reaction.)
I thought about it, and decided it wasn't really all that necessary, beyond the
fact that I'd need to take some extra steps to be able to actually test a PHP4
version. So, I did a PHP4 version.

Well, then some chatter happened, and a number of developers said, "Why *not*
PHP5?" So, I went back to PHP5. And then somebody else said, "Use
`PEAR_Exception`." So, I started playing with that, and we finally get to the
subject of this post.

<!--- EXTENDED -->

Exception handling is one of the advances PHP5 brought to PHP. I was very
excited to have it available, as I'm accustomed to exception handling in perl
(which is actually quite different than PHP's model, but the basics are the
same). When I saw the suggestion to use it, I realized that exception handling
would make the package solidly a PHP5 package. Simultaneously, I wondered why
it hadn't occurred to me. Guess I've been coding more PHP than perl for a while
now…

The problem is that there's very little documentation on `PEAR_Exception`, and
the tips I got on list, while helpful in getting my proposal out the door, left
me with a lot of questions.

For those who haven't used `PEAR_Exception`, here's the basics:

- Create a file in which to hold your exceptions classes. (Yes, plural; I'll
  get to that). If you're developing a PEAR-style package, you want to put it
  in the directory pertaining to your package name. So, since I was developing
  `File_Fortune`, my exception class became `File/Fortune/Exception.php`.
- Create a base exception class for your class that extends `PEAR_Exception`:

  ```php
  class File_Fortune_Exception extends PEAR_Exception
  {
  }
  ```

  Note: it doesn't override anything. It just creates a pseudo-namespace.

- For each unique exception type, extend your base class:

  ```
  class File_Fortune_FileException extends File_Fortune_Exception
  {
  }

  class File_Fortune_HeaderException extends File_Fortune_Exception
  {
  }
  ```

  (Yes, you should create docblocks for each, describing their purpose.)

- In your code, throw exceptions instead of raising errors:

  ```php
  if (false === ($fh = fopen($filename))) {
      throw new File_Fortune_FileException('Unable to open file');
  }
  ```
            
- In your phpDoc blocks, use `@throws Exception_Class_Name` with some descriptive text

And that's it in a nutshell.

The beauty of it is that you can really separate errors from return values —
errors are no longer a possible return value:

```php
try {
    $fortune = $ff->getRandom();
} catch (File_Fortune_Exception $e) {
    echo "Couldn't get fortune: " . $e->getMessage();
}
```

The problem I saw with the system is that you end up with a bunch of exception
classes, each of which has a veeeerrryyy looooonnnnngggg name, which leads to
lots of typing, the possibility for typos (I had one in the version I used for
the call to votes), and the possibility for more error handling than code,
depending on the number of possible exceptions and how carefully you want to
check for them:

```php
try {
    $fortune = $ff->getRandom();
} catch (File_Fortune_BadHeaderFileException $e) {
    echo "Could not parse header file";
} catch (File_Fortune_HeaderFileException $e) {
    echo "Could not open header file";
} catch (File_Fortune_BadFileException $e) {
    echo "Badly formed fortune file";
} catch (File_Fortune_Exception $e) {
    // Catch-all for File_Fortune errors...
    echo "Couldn't get fortune: " . $e->getMessage();
}
```

I got to thinking that there must be a better way. I haven't actually come up
with one yet, though. My idea so far, however, is to have a single exception
class, and in it define a number of class constants or statics — much like
`PEAR_Error`/`PEAR_ErrorStack`, where they map to integer values — and to have
these values map to actual error messages (which could possibly be localized
within the class as well). Then, when throwing an error, it might be something
like:

```php
if (false === ($fh = fopen($filename))) {
    throw new File_Fortune_Exception(1);
} 
// or
if (false === ($fh = fopen($filename))) {
    throw new File_Fortune_Exception(File_Fortune_Exception::FILE);
} 
```

The constructor would be overridden to set the code and message based on the
code passed (if a string was passed, that would be the message). Then you could
test for a single exception class, and use `$e->getCode()` to check for the
type if you need more fine-grained control.

I'd be more than happy to discuss possibilities. Exceptions are a fantastic way
to check for truly exceptional behaviour in code; in PHP5, they also seem to be
incredibly fast and lightweight (though I have no substantive data to back that
statement, other than API responsiveness). I'd like to see more people
developing with them.

On that note, what do other PHP develpers think of exception handling? I've
heard some say it's too 'goto-ish' (I'm not sure I follow that train of
thought), others prefer the simplicity of `PEAR_Error`. Leave a comment!
