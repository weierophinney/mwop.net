---
id: 142-File_Fortune-refactored
author: matthew
title: 'File_Fortune refactored'
draft: false
public: true
created: '2007-07-05T17:26:00-04:00'
updated: '2007-07-10T08:34:52-04:00'
tags:
    - php
    - pear
---
Over the past few evenings, I've refactored
[File_Fortune](http://pear.php.net/trackback/trackback.php?id=File_Fortune) to
have it implement `Iterator`, `Countable`, and `ArrayAccess` — basically allowing it
to act like an array for most intents and purposes. As a result, I've eliminated
the need for the `File_Fortune_Writer` package, and greatly simplified the
usage.

(Note: sure, `File_Fortune` may not be that big of a deal, but over 1000
downloads in the past two years indicates *somebody* is using it. Plus, it
powers the random quotes on the family website. :-) )

As some examples:

```php
require_once 'File/Fortune.php';

// Initialize and point it to a directory of fortunes
$fortunes = new File_Fortune('/path/to/fortunedir');

// Retrieve a random fortune 
// (works with either a directory or a single fortune file)
echo $fortunes->getRandom();

// Set to a specific fortune file:
$fortunes->setFile('myfortunes');

// Loop through and print all fortunes
foreach ($fortunes as $fortune) {
    echo str_repeat('-', 72), "\n", $fortune, "\n\n";
}

// Hmmm.. let's change one:
$fortunes[7] = "I never really liked that fortune anyways.";

// No need to explicitly save, as it's done during __destruct(), 
// but if you really want to:
$fortunes->save();

// Let's add a new fortune:
$fortunes->add('This is a shiny new fortune!');

// and now we'll verify it exists:
$index = count($fortunes) - 1;
echo $fortunes[$index];
```

All-in-all, it's a much better interface. Lesson learned: when porting code from
other languages, it pays to take some time and determine if there might be a
better API in your own.

In upcoming releases, I hope to modify the backend to use PHP's Streams API
instead of direct file access, and also to allow providing a list of fortune
files explicitly. After that, I should be ready for the initial stable release.

**Update (2007-07-10): fixed parse error in examples**
