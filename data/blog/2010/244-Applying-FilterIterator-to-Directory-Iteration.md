---
id: 244-Applying-FilterIterator-to-Directory-Iteration
author: matthew
title: 'Applying FilterIterator to Directory Iteration'
draft: false
public: true
created: '2010-08-16T10:30:00-04:00'
updated: '2010-08-20T15:45:21-04:00'
tags:
    - php
    - spl
---
I'm currently doing research and prototyping for autoloading alternatives in
[Zend Framework](http://framework.zend.com/) 2.0. One approach I'm looking at
involves creating explicit class/file maps; these tend to be much faster than
using the `include_path`, but do require some additional setup.

My algorithm for generating the maps was absurdly simple:

- Scan the filesystem for PHP files
- If the file does not contain an interface, class, or abstract class, skip it.
- If it does, get its declared namespace and classname

The question was what implementation approach to use.

I'm well aware of `RecursiveDirectoryIterator`, and planned to use that.
However, I also had heard of `FilterIterator`, and wondered if I could tie that
in somehow. In the end, I could, but the solution was non-obvious.

<!--- EXTENDED -->

What I Thought I'd Be Able To Do
--------------------------------

`FilterIterator` is an abstract class. When extending it, you must define an
`accept()` method.

```php
class FooFilter extends FilterIterator
{
    public function accept()
    {
    }
}
```

In that method, you typically will inspect whatever is returned by
`$this->current()`, and then return a boolean `true` or `false`, depending on
whether you want to keep it or not.

```php
class FooFilter extends FilterIterator
{
    public function accept()
    {
        $item = $this->current();

        if ($someCriteriaIsMet) {
            return true;
        }

        return false;
    }
}
```

I'll go into the mechanics of my criteria later; what's important now is knowing
that a `FilterIterator` allows you to limit the results returned by your
iterator.

I originally thought I'd be able to simply pass a `DirectoryIterator` or
`RecursiveDirectoryIterator` to my filtering instance. This worked in the former
case, as it's only one level deep. However, for the latter, it would only return
the first directory level for all classes that matched — i.e., if I ran it over
`Zend/Controller`, I'd get a match for each class under
`Zend/Controller/Action/Helper/`, but it would return simply
`Zend/Controller/Action` as the match. This certainly wasn't useful.

I then discovered `RecursiveFilterIterator`, which looked like it would solve
the recursion problem. However, I found one of two results occurred: either I'd
receive an entire subtree if at least one item matched, or it would skip an
entire subtree if the first item found failed the criteria. There was no middle
ground.

The Solution
------------

The solution was incredibly simple and elegant, once I stumbled upon it: pass my
`RecursiveIteratorIterator` instance to the `FilterIterator`.

```php
$rdi      = new RecursiveDirectoryIterator($somePath);
$rii      = new RecursiveIteratorIterator($rdi);
$filtered = new FooFilter($rii);
```

Really. It was that simple — but, as noted, non-obvious. It also required a
slight change within my filter — instead of using `current()`, I'd need to first
pull the "inner" iterator instance: `$this->getInnerIterator()->current()`. I
show an example of that below when I go over the filter implementation.

As for my criteria, I had several options. I could `require_once` the file, and
use the Reflection API to inspect the class to determine if it was an interface,
abstract class, or class, as well as to determine the namespace. However, I
couldn't be 100% sure the file would contain a class, so this seemed like
overkill. That, and horribly non-performant, due to using reflection.

The next option was to simply slurp in the file contents into a variable, and
use regular expressions. I love regular expressions, but in this case, it felt
like I could possibly end up with some false positives. Also, since some of
these files could be quite large, I was worried again about performance
implications — I don't want to have to wait forever to generate these maps.

The solution I went with was to use the [tokenizer](http://php.net/tokenizer) to
inspect the file. Tokenizing is incredibly fast, and it's also incredibly simple
to analyze the tokens.

I decided to store the detected namespace and classnames as public properties of
the `SplFileInfo` objects returned; this makes it simple to iterate over the
entire collection and utilize that information. Also, because I have
`SplFileInfo` objects, I already have the paths I need.

My implementation looks like this:

```php
/** @namespace */
namespace Zend\File;

// import SPL classes/interfaces into local scope
use DirectoryIterator,
    FilterIterator,
    RecursiveIterator,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator;

/**
 * Locate files containing PHP classes, interfaces, or abstracts
 * 
 * @package    Zend_File
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class ClassFileLocater extends FilterIterator
{
    /**
     * Create an instance of the locater iterator
     * 
     * Expects either a directory, or a DirectoryIterator (or its recursive variant) 
     * instance.
     * 
     * @param  string|DirectoryIterator $dirOrIterator 
     * @return void
     */
    public function __construct($dirOrIterator = '.')
    {
        if (is_string($dirOrIterator)) {
            if (!is_dir($dirOrIterator)) {
                throw new InvalidArgumentException('Expected a valid directory name');
            }

            $dirOrIterator = new RecursiveDirectoryIterator($dirOrIterator);
        }
        if (!$dirOrIterator instanceof DirectoryIterator) {
            throw new InvalidArgumentException('Expected a DirectoryIterator');
        }

        if ($dirOrIterator instanceof RecursiveIterator) {
            $iterator = new RecursiveIteratorIterator($dirOrIterator);
        } else {
            $iterator = $dirOrIterator;
        }

        parent::__construct($iterator);
        $this->rewind();
    }

    /**
     * Filter for files containing PHP classes, interfaces, or abstracts
     * 
     * @return bool
     */
    public function accept()
    {
        $file = $this->getInnerIterator()->current();

        // If we somehow have something other than an SplFileInfo object, just 
        // return false
        if (!$file instanceof \SplFileInfo) {
            return false;
        }

        // If we have a directory, it's not a file, so return false
        if (!$file->isFile()) {
            return false;
        }

        // If not a PHP file, skip
        if ($file->getBasename('.php') == $file->getBasename()) {
            return false;
        }

        $contents = file_get_contents($file->getRealPath());
        $tokens   = token_get_all($contents);
        $count    = count($tokens);
        $i        = 0;
        while ($i < $count) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                // single character token found; skip
                $i++;
                continue;
            }

            list($id, $content, $line) = $token;

            switch ($id) {
                case T_NAMESPACE:
                    // Namespace found; grab it for later
                    $namespace = '';
                    $done      = false;
                    do {
                        ++$i;
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            if (';' === $token) {
                                $done = true;
                            }
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        switch ($type) {
                            case T_STRING:
                            case T_NS_SEPARATOR:
                                $namespace .= $content;
                                break;
                        }
                    } while (!$done && $i < $count);

                    // Set the namespace of this file in the object
                    $file->namespace = $namespace;
                    break;
                case T_ABSTRACT:
                case T_CLASS:
                case T_INTERFACE:
                    // Abstract class, class, or interface found

                    // Get the classname
                    $class = '';
                    do {
                        ++$i;
                        $token = $tokens[$i];
                        if (is_string($token)) {
                            continue;
                        }
                        list($type, $content, $line) = $token;
                        switch ($type) {
                            case T_STRING:
                                $class = $content;
                                break;
                        }
                    } while (empty($class) && $i < $count);

                    // If a classname was found, set it in the object, and 
                    // return boolean true (found)
                    if (!empty($class)) {
                        $file->classname = $class;
                        return true;
                    }
                    break;
                default:
                    break;
            }
            ++$i;
        }

        // No class-type tokens found; return false
        return false;
    }
}
```

*Note: the Exceptions thrown in this class are defined in the same namespace;
I'll leave how they're implemented to your imagination.*

Iterating Faster
----------------

The next trick I discovered was in the form of `iterator_apply()`. Normally when
I use iterators, I use `foreach`, because, well, that's what you do. But in
looking through the various iterators for this exercise, I stumbled across this
gem.

Basically, you pass the iterator, a callback, and argument(s) you want passed to
the callback. Like `FilterIterator`, you don't get the actual item returned by
the iterator, so in most use cases, you pass the iterator itself:

```php
iterator_apply($it, $callback, array($it));
```

You can then grab the current value and/or key from the iterator itself:

```php
public function process(Iterator $it)
{
    $value = $it->current();
    $key   = $it->key();
    // ...
}
```

While you can use any valid PHP callback, I found the most interesting solution
was to use a closure, as it allows you to define everything up front:

```php
iterator_apply($it, function() use ($it) {
    $value = $it->current();
    $key   = $it->key();
    // ...
});
```

If you pass in a local value via a `use` statement, you can do some aggregation:

```php
$map = new \stdClass;
iterator_apply($it, function() use ($it, $map) {
    $file = $it->current();
    $namespace = !empty($file->namespace) ? $file->namespace . '\' : '';
    $classname = $namespace . $file->classname;
    $map->{$classname} = $file->getPathname();
});
```

Not only is this a nice, concise technique, it's also tremendously fast — I was
finding it was 200%–300% faster than using a traditional `foreach` loop.
Clearly it cannot be used in all situations, but if you *can* use it, you
probably should.

So, start playing with `FilterIterator` and `iterator_apply()` if you haven't
already — the two offer tremendous possibilities and capabilities for your applications.
